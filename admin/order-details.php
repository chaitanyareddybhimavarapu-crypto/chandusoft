<?php
session_start();
$_SESSION['role'] = 'admin'; // 🔥 Temporary: allow access for testing
require_once __DIR__ . '/../app/config.php';

// Ensure the order ID is passed as a GET parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid order ID');
}

$orderId = (int)$_GET['id'];

// Fetch order details by ID
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Order not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 40px; }
        h1 { color: #333; }
        .order-details { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .order-details p { font-size: 16px; }
    </style>
</head>
<body>

<h1>Order Details</h1>

<div class="order-details">
    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['id']) ?></p>
    <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>Customer Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
    <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
    <p><strong>Customer Address:</strong> <?= htmlspecialchars($order['customer_address']) ?></p>
</div>

<p><a href="orders.php">← Back to Orders</a></p>

</body>
</html>
