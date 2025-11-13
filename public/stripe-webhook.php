<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';

use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

// ✅ Set Stripe API Key
Stripe::setApiKey($stripeSecretKey);

// ✅ Get raw event body + Stripe signature
$payload = @file_get_contents("php://input");
$sig = $_SERVER["HTTP_STRIPE_SIGNATURE"] ?? '';
$endpointSecret = $stripeWebhookSecret;

// ✅ Log helper
function stripeLog($msg) {
    file_put_contents(__DIR__ . '/../storage/logs/stripe.log', date("Y-m-d H:i:s") . " " . $msg . "\n", FILE_APPEND);
}

try {
    // ✅ Verify Stripe signature
    $event = Webhook::constructEvent($payload, $sig, $endpointSecret);

    if ($event->type === 'checkout.session.completed') {
        
        $session = $event->data->object;
        $orderRef = $session->metadata->order_ref ?? null;
        $txnId = $session->payment_intent ?? null;
        $amountPaid = ($session->amount_total ?? 0) / 100; // Stripe → dollars

        if (!$orderRef) {
            stripeLog("❌ No order_ref in webhook metadata");
            throw new Exception("Missing order_ref");
        }

        // ✅ Fetch order and ensure it is still pending
        $stmt = $pdo->prepare("SELECT id, total_amount, status FROM orders WHERE order_ref=? LIMIT 1");
        $stmt->execute([$orderRef]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            stripeLog("❌ Order not found: $orderRef");
            throw new Exception("Order not found");
        }

        // ✅ Idempotency: ignore if already paid
        if ($order['status'] !== 'pending') {
            stripeLog("⚠️ Duplicate webhook ignored — order already {$order['status']} ($orderRef)");
            http_response_code(200);
            exit;
        }

        // ✅ Validate amount matches DB
        if (abs($order['total_amount'] - $amountPaid) > 0.01) {
            stripeLog("🚨 Amount mismatch! Stripe: $amountPaid vs DB: {$order['total_amount']} | $orderRef");
            throw new Exception("Amount mismatch");
        }

        if ($txnId) {
            // ✅ Mark order as PAID safely
            $stmt = $pdo->prepare("UPDATE orders SET status='paid', txn_id=? WHERE order_ref=? AND status='pending'");
            $stmt->execute([$txnId, $orderRef]);

            stripeLog("✅ Payment success: $orderRef | TXN: $txnId | Amount: $amountPaid");
        } else {
            stripeLog("❌ Missing payment_intent for $orderRef");
        }
    }

    http_response_code(200); // ✅ Required

} catch (SignatureVerificationException $e) {
    stripeLog("❌ Signature error: " . $e->getMessage());
    http_response_code(400);
} catch (Exception $e) {
    stripeLog("❌ Webhook error: " . $e->getMessage());
    http_response_code(400);
}
