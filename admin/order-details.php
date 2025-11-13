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
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 40px;
        }

        h1 {
            color: #2c3e50;
            font-size: 32px;
            text-align: center;
            margin-bottom: 30px;
        }

        .order-details {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 0 auto;
        }

        .order-details p {
            font-size: 16px;
            line-height: 1.8;
            color: #34495e;
            margin-bottom: 12px;
        }

        .order-details p strong {
            color: #2980b9;
        }

        .order-details .order-summary {
            border-top: 2px solid #ecf0f1;
            padding-top: 15px;
            margin-top: 15px;
        }

        /* Button Styles */
        a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #0056b3;
        }

        /* Table Styles (if you want to display items in a table) */
        .order-items-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .order-items-table th,
        .order-items-table td {
            padding: 12px;
            border: 1px solid #ecf0f1;
            text-align: left;
            color: #34495e;
        }

        .order-items-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .order-items-table td {
            background-color: #fff;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .order-details {
                padding: 15px;
                margin: 20px;
            }

            h1 {
                font-size: 28px;
            }
        }

    </style>
</head>
<body>

<h1>Order Details</h1>

<div class="order-details">
    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['id']) ?></p>
    <p><strong>Order Reference:</strong> <?= htmlspecialchars($order['order_ref']) ?></p> <!-- Added order_ref here -->
    <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>Customer Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
    <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
    <p><strong>Customer Address:</strong> <?= htmlspecialchars($order['customer_address']) ?></p>
    
    <!-- Optional: You can add a table to list items ordered -->
    <!-- <div class="order-summary"> -->
    <!-- <h3>Ordered Items</h3> -->
    <!-- <table class="order-items-table"> -->
    <!--     <tr> -->
    <!--         <th>Product</th> -->
    <!--         <th>Quantity</th> -->
    <!--         <th>Price</th> -->
    <!--     </tr> -->
    <!--     <?php foreach ($items as $item): ?> -->
    <!--         <tr> -->
    <!--             <td><?= htmlspecialchars($item['product_name']) ?></td> -->
    <!--             <td><?= htmlspecialchars($item['quantity']) ?></td> -->
    <!--             <td>$<?= number_format($item['price'], 2) ?></td> -->
    <!--         </tr> -->
    <!--     <?php endforeach; ?> -->
    <!-- </table> -->
    <!-- </div> -->

</div>

<p><a href="orders.php">← Back to Orders</a></p>

</body>
</html>
