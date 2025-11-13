<?php
session_start();
require_once __DIR__ . '/../app/security-init.php';
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

$errors = [];

// ✅ Redirect if cart empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// ✅ Load products from DB
$cartIds = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($cartIds), '?'));
$stmt = $pdo->prepare("SELECT id, title, price FROM catalog_items WHERE id IN ($placeholders)");
$stmt->execute($cartIds);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Compute total server-side
$total = 0;
foreach ($products as $p) {
    $total += $p['price'] * $_SESSION['cart'][$p['id']];
}

// ✅ Start a fresh order if requested
if (isset($_GET['neworder'])) {
    unset($_SESSION['order_id'], $_SESSION['order_ref'], $_SESSION['user_details']);
    header("Location: checkout.php");
    exit;
}

// ✅ Check for existing pending order to resume
if (isset($_SESSION['order_ref'])) {
    $stmt = $pdo->prepare("SELECT status, stripe_session_id FROM orders WHERE order_ref=? LIMIT 1");
    $stmt->execute([$_SESSION['order_ref']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order && $order['status'] === 'pending') {

        $resumeUrl = "checkout-stripe.php"; // fallback

        if (!empty($order['stripe_session_id'])) {
            try {
                Stripe::setApiKey($stripeSecretKey);
                $stripeSession = StripeSession::retrieve($order['stripe_session_id']);
                $resumeUrl = $stripeSession->url; // official Stripe redirect
            } catch (Exception $e) {
                error_log("Stripe resume error: " . $e->getMessage());
            }
        }

        echo "
        <div style='background:#fff3cd;border:1px solid #ffeeba;padding:20px;
        margin:20px auto;width:80%;max-width:500px;border-radius:8px;text-align:center'>
            <h3>⚠️ Pending Payment Found</h3>
            <p>You have an unpaid order. Complete it now?</p>
            <a href='$resumeUrl' style='background:#28a745;color:#fff;padding:10px 20px;
            border-radius:6px;text-decoration:none;'>Resume Payment</a>
            <br><br>
            <a href='checkout.php?neworder=1' style='font-size:13px;text-decoration:none;'>
                Cancel and Create New Order
            </a>
        </div>";
        exit;
    }
}

// ✅ Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $method = $_POST['pay_method'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!in_array($method, ['paypal', 'stripe'])) $errors[] = 'Choose a payment method.';

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $_SESSION['user_details'] = compact('name', 'email', 'address');

            // ✅ Generate unique order reference
            $orderRef = 'ORD-' . strtoupper(uniqid());

            // ✅ Insert order into DB
            $stmt = $pdo->prepare("
                INSERT INTO orders (customer_name, customer_email, customer_address,
                    total_amount, status, created_at, payment_gateway, order_ref)
                VALUES (?, ?, ?, ?, 'pending', NOW(), ?, ?)
            ");
            $stmt->execute([$name, $email, $address, $total, $method, $orderRef]);
            $orderId = $pdo->lastInsertId();

            // ✅ Insert order items
            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            foreach ($products as $p) {
                $itemStmt->execute([
                    $orderId,
                    $p['id'],
                    $_SESSION['cart'][$p['id']],
                    $p['price']
                ]);
            }

            $pdo->commit();

            $_SESSION['order_id'] = $orderId;
            $_SESSION['order_ref'] = $orderRef;

            // ✅ Redirect to payment gateway
            header("Location: checkout-" . $method . ".php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Order Creation Failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<style>
body { font-family: Arial,sans-serif;background:#f5f5f5;padding:40px; }
h1 { text-align:center;color:#007bff; }
form { width:80%;max-width:480px;margin:20px auto;background:#fff;
       padding:20px;border-radius:10px;box-shadow:0 3px 8px rgba(0,0,0,.1);}
input, textarea { width:100%;padding:10px;margin-top:8px;border:1px solid #ccc;
                  border-radius:4px; }
button {margin-top:15px;width:100%;padding:12px;border:none;border-radius:5px;
        color:#fff;font-size:16px;cursor:pointer;}
.stripe-btn {background:#6772e5;}
.paypal-btn {background:#ffc439;color:#111;}
.error-list {color:#d00;text-align:center;}
</style>
</head>
<body>

<h1>🛒 Checkout</h1>

<?php if ($errors): ?>
<ul class="error-list">
    <?php foreach($errors as $e): ?>
    <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<div style="text-align:center;font-size:18px;margin-bottom:12px;">
    <strong>Total: $<?= number_format($total,2) ?></strong>
</div>

<form method="post" action="">
    <label>Name *</label>
    <input type="text" name="name" required>

    <label>Email *</label>
    <input type="email" name="email" required>

    <label>Address</label>
    <textarea name="address" rows="3"></textarea>

    <button type="submit" name="pay_method" value="stripe" class="stripe-btn">💳 Pay with Stripe</button>
    <button type="submit" name="pay_method" value="paypal" class="paypal-btn">💰 Pay with PayPal</button>
</form>

<p style="text-align:center;">
    <a href="cart.php">← Back to Cart</a>
</p>

</body>
</html>
