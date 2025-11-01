<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

// ✅ Redirect if cart empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$errors = [];
$success = false;

// ✅ Load products from DB for total validation
$ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
$stmt = $pdo->query("SELECT id, title, price FROM catalog_items WHERE id IN ($ids) AND status='published'");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Compute total (secure server-side)
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
    $method  = $_POST['pay_method'] ?? ''; // paypal or stripe

    // Basic validation
    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!in_array($method, ['paypal', 'stripe'])) $errors[] = 'Please select a payment method.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // ✅ Insert new order (status=pending)
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

            // ✅ Save order ID for payment
            $_SESSION['order_id'] = $orderId;

            // Redirect based on chosen payment method
            if ($method === 'stripe') {
                header("Location: checkout-stripe.php");
            } else {
                header("Location: checkout-paypal.php");
            }
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Order could not be created: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 40px; }
    h1 { text-align: center; color: #0056b3; }
    form { width: 80%; max-width: 500px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px;
           box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    label { display: block; font-weight: bold; margin-top: 10px; }
    input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    button { background: #007bff; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; margin-top: 15px; width: 100%; }
    button:hover { background: #0056b3; }
    .error-list { color: red; list-style: none; padding: 0; }
    .total { text-align: center; font-size: 18px; margin-top: 20px; }
    .pay-buttons { display: flex; gap: 10px; margin-top: 15px; }
    .pay-buttons button { flex: 1; }
    .stripe-btn { background: #6772e5; }
    .stripe-btn:hover { background: #5469d4; }
    .paypal-btn { background: #ffc439; color: #111; }
    .paypal-btn:hover { background: #ffb400; }
</style>
</head>
<body>

<h1>🧾 Checkout</h1>

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

    <div class="pay-buttons">
        <button type="submit" name="pay_method" value="paypal" class="paypal-btn">💰 Pay with PayPal</button>
        <button type="submit" name="pay_method" value="stripe" class="stripe-btn">💳 Pay with Stripe</button>
    </div>
</form>

<p style="text-align:center;"><a href="cart.php">← Back to Cart</a></p>

</body>
</html>
