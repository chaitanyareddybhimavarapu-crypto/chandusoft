<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Stripe SDK

use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

// ✅ Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$errors = [];

// ✅ Load products for total calculation
$ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
$stmt = $pdo->query("SELECT id, title, price FROM catalog_items WHERE id IN ($ids) AND status='published'");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Securely calculate total
$total = 0;
foreach ($products as $p) {
    $qty = $_SESSION['cart'][$p['id']];
    $total += $p['price'] * $qty;
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // ✅ Insert new order (status = pending)
            $stmt = $pdo->prepare("
                INSERT INTO orders (customer_name, customer_email, customer_address, total_amount, status, created_at)
                VALUES (?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([$name, $email, $address, $total]);
            $orderId = $pdo->lastInsertId();

            // ✅ Insert order items
            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            foreach ($products as $p) {
                $pid = $p['id'];
                $qty = $_SESSION['cart'][$pid];
                $price = $p['price'];
                $itemStmt->execute([$orderId, $pid, $qty, $price]);
            }

            $pdo->commit();

            // ✅ Stripe Checkout Integration
            Stripe::setApiKey('sk_test_51SNs3wAhJkYkXNzXsOYQJ2VYGfYIfcXd20lVISFv3Dq4W1eafNWaVQQQFEVUplug1FUx2jY4PDivCDC3bJSL2gX900hbscfEG2');

            $lineItems = [];
            foreach ($products as $p) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => ['name' => $p['title']],
                        'unit_amount' => intval($p['price'] * 100), // Stripe uses cents
                    ],
                    'quantity' => $_SESSION['cart'][$p['id']],
                ];
            }

            // ✅ Pass order_id to success URL so we can update it as "paid"
            $checkoutSession = CheckoutSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => 'http://localhost/chandusoft/public/checkout-success.php?order_id=' . $orderId,
                'cancel_url'  => 'http://localhost/chandusoft/public/checkout-cancel.php',
            ]);

            // Save order_id in session (optional)
            $_SESSION['order_id'] = $orderId;

            // Redirect to Stripe Checkout
            header("Location: " . $checkoutSession->url);
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = "Order could not be created: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout with Stripe</title>
<style>
body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 40px; }
h1 { text-align:center; color:#0056b3; }
form { width: 80%; max-width: 500px; margin: 20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
label { display:block; font-weight:bold; margin-top:10px; }
input, textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; }
button { background:#007bff; color:#fff; border:none; padding:12px; border-radius:4px; cursor:pointer; margin-top:15px; width:100%; }
button:hover { background:#0056b3; }
.error-list { color:red; list-style:none; padding:0; }
.total { text-align:center; font-size:18px; margin-top:20px; }
</style>
</head>
<body>

<h1>💳 Checkout with Stripe</h1>

<?php if ($errors): ?>
<ul class="error-list">
<?php foreach ($errors as $err): ?>
    <li><?= htmlspecialchars($err) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<div class="total">
    <p><strong>Order Total:</strong> $<?= number_format($total, 2) ?></p>
</div>

<form method="post" action="">
    <label>Name *</label>
    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>

    <label>Email *</label>
    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

    <label>Address (optional)</label>
    <textarea name="address" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>

    <button type="submit">Pay with Stripe →</button>
</form>

<p style="text-align:center;"><a href="cart.php">← Back to Cart</a></p>

</body>
</html>
