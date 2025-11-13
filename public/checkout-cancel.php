<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Webhook;

// ==============================
// ✅ Handle user-cancelled payment
// ==============================
$orderRef = $_SESSION['order_ref'] ?? null;

if ($orderRef) {
    $stmt = $pdo->prepare("UPDATE orders SET status='failed' WHERE order_ref=?");
    $stmt->execute([$orderRef]);

    // Log cancellation
    $logPath = __DIR__ . '/../storage/logs/stripe.log';
    file_put_contents($logPath,
        date("Y-m-d H:i:s") . " ❌ CANCELLED: $orderRef\n",
        FILE_APPEND
    );

    unset($_SESSION['cart'], $_SESSION['order_id'], $_SESSION['order_ref']);
}

// ==============================
// ✅ Optional: Handle Stripe webhook for payment failure
// ==============================
$webhookSecret = 'whsec_XXXXXXXXXXXXXXXX'; // Replace with your Stripe webhook secret
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = Webhook::constructEvent($payload, $sig_header, $webhookSecret);

    if ($event->type === 'payment_intent.payment_failed') {
        $paymentIntent = $event->data->object;
        $orderRef = $paymentIntent->metadata->order_ref ?? null;
        $txnId = $paymentIntent->id;
        $errorMsg = $paymentIntent->last_payment_error->message ?? 'Unknown error';

        if ($orderRef) {
            // Update order as failed in DB
            $stmt = $pdo->prepare("UPDATE orders SET status='failed', txn_id=? WHERE order_ref=?");
            $stmt->execute([$txnId, $orderRef]);

            // Log the failed payment
            file_put_contents($logPath,
                date("Y-m-d H:i:s") . " ❌ PAYMENT FAILED: $orderRef / $txnId / $errorMsg\n",
                FILE_APPEND
            );
        }
    }

    http_response_code(200);
} catch (\Exception $e) {
    // Ignore if not a Stripe webhook call
    // Just ensures normal cancel page still loads
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Cancelled</title>
<style>
body { font-family: Arial, sans-serif; text-align: center; padding: 60px; background: #f9f9f9; }
h1 { color: #dc3545; }
a { color: #007bff; text-decoration: none; }
</style>
</head>
<body>
    <h1>❌ Payment Cancelled</h1>
    <p>Your payment was cancelled or failed. You can try again later.</p>
    <p><a href="cart.php">← Back to Cart</a></p>
</body>
</html>
