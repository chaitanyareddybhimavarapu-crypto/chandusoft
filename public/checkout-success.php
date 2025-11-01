<?php
session_start();
require_once __DIR__ . '/../app/config.php';

if (!isset($_GET['order_id'])) {
    die("Order ID missing.");
}

$orderId = (int)$_GET['order_id'];

// Fetch order details to display
$stmt = $pdo->prepare("SELECT id, customer_name, total_amount, status FROM orders WHERE id=?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Update the order status to 'paid' if not already
if ($order['status'] !== 'paid') {
    $updateStmt = $pdo->prepare("UPDATE orders SET status='paid' WHERE id=?");
    $updateStmt->execute([$orderId]);
}

// Clear cart
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Successful</title>
<style>
body { font-family: Arial, sans-serif; background:#f9f9f9; padding:40px; text-align:center; }
h1 { color: #28a745; }
p { font-size:16px; }
a { color:#007bff; text-decoration:none; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<h1>✅ Payment Successful!</h1>
<p>Thank you, <strong><?= htmlspecialchars($order['customer_name']) ?></strong>, for your order.</p>
<p><strong>Order #<?= $order['id'] ?></strong> totaling <strong>$<?= number_format($order['total_amount'],2) ?></strong> has been successfully received.</p>
<p><a href="catalog.php">← Continue Shopping</a></p>
</body>
</html>
