<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

// ✅ session_id must exist
if (!isset($_GET['session_id'])) {
    header("Location: cart.php");
    exit;
}

$stripeSessionId = $_GET['session_id'];

// ✅ Get Stripe session details
Stripe::setApiKey($stripeSecretKey);

try {
    $session = StripeSession::retrieve($stripeSessionId);
    $paymentStatus = $session->payment_status; // should be 'paid'
    $orderRef = $session->metadata->order_ref;
    $txnId = $session->payment_intent; // ✅ Transaction ID

    // ✅ DEBUG LOGGING - check values
    $logPath = __DIR__ . '/../storage/logs/stripe.log';
    file_put_contents($logPath,
        date("Y-m-d H:i:s") . " 🔍 SESSION: " . $stripeSessionId . "\n",
        FILE_APPEND
    );
    file_put_contents($logPath,
        date("Y-m-d H:i:s") . " 🔍 STATUS: " . ($paymentStatus ?? 'NULL') . "\n",
        FILE_APPEND
    );
    file_put_contents($logPath,
        date("Y-m-d H:i:s") . " 🔍 TXN: " . ($txnId ?? 'NULL') . "\n",
        FILE_APPEND
    );

    if ($paymentStatus === 'paid') {
        if ($txnId) {
            // ✅ Update order to paid + store txn_id
            $stmt = $pdo->prepare("UPDATE orders SET status='paid', txn_id=? WHERE order_ref=?");
            $stmt->execute([$txnId, $orderRef]);

            // ✅ Log successful payment
            file_put_contents($logPath,
                date("Y-m-d H:i:s") . " ✅ PAID: $orderRef / $txnId\n",
                FILE_APPEND
            );
        } else {
            file_put_contents($logPath,
                date("Y-m-d H:i:s") . " ❌ Missing payment_intent for $orderRef\n",
                FILE_APPEND
            );
        }

        // ✅ Fetch order details to display
        $stmtOrder = $pdo->prepare("SELECT * FROM orders WHERE order_ref=? LIMIT 1");
        $stmtOrder->execute([$orderRef]);
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        $stmtItems = $pdo->prepare("
            SELECT oi.quantity, oi.price, c.title
            FROM order_items oi
            JOIN catalog_items c ON oi.product_id = c.id
            WHERE oi.order_id=?
        ");
        $stmtItems->execute([$order['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // ✅ Clear cart after success
        unset($_SESSION['cart'], $_SESSION['order_id'], $_SESSION['order_ref']);

    } else {
        $errorMsg = "Payment not completed yet.";
    }

} catch (Exception $e) {
    $errorMsg = "⚠️ Unable to verify payment: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Success</title>
<style>
    body { font-family: Arial, sans-serif; background: #f8fff8; padding: 40px; text-align: center; }
    .box { background: #fff; padding: 25px; border-radius: 8px; width: 90%; max-width: 600px; margin: auto;
           box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    h1 { color: #28a745; }
    ul { list-style: none; padding: 0; font-size: 16px; }
    li { margin: 6px 0; }
    .btn { display: inline-block; margin-top: 20px; background: #007bff; color: #fff;
           padding: 12px 20px; border-radius: 5px; text-decoration: none; }
    .btn:hover { background: #0056b3; }
</style>
</head>
<body>

<div class="box">
<?php if (isset($errorMsg)): ?>
    <h2 style="color:red;"><?= htmlspecialchars($errorMsg) ?></h2>
<?php else: ?>
    <h1>✅ Payment Successful!</h1>
    <p>Thank you for your order, <strong><?= htmlspecialchars($order['customer_name']) ?></strong>!</p>

    <p><strong>Order Reference:</strong> <?= htmlspecialchars($orderRef) ?></p>
    <p><strong>Transaction ID:</strong> <?= htmlspecialchars($txnId) ?></p> <!-- ✅ ADDED DISPLAY -->

    <p><strong>Total Paid:</strong> $<?= number_format($order['total_amount'], 2) ?></p>

    <h3>Order Items</h3>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?= htmlspecialchars($item['title']) ?> × <?= $item['quantity'] ?> — 
                $<?= number_format($item['price'], 2) ?></li>
        <?php endforeach; ?>
    </ul>

    <a href="https://karry-landlike-homophyly.ngrok-free.dev/public/catalog.php" class="btn">
        🏠 Continue Shopping
    </a>

<?php endif; ?>
</div>

</body>
</html>
