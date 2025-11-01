<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

$paymentId = $_GET['paymentId'] ?? null;
$payerId   = $_GET['PayerID'] ?? null;

if (!$paymentId || !$payerId) {
    die("Payment not approved.");
}

// Get the payment details using the payment ID
try {
    $payment = Payment::get($paymentId, $paypal);

    // Execute the payment
    $execution = new PaymentExecution();
    $execution->setPayerId($payerId);

    // Execute the payment on PayPal
    $result = $payment->execute($execution, $paypal);

    // Update order status to 'paid'
    $stmt = $pdo->prepare("UPDATE orders SET status='paid' WHERE id=?");
    $stmt->execute([$_SESSION['order_id']]);

    // Clean up the session
    unset($_SESSION['cart'], $_SESSION['order_id']);

    // Show success message
    echo "✅ Payment successful! Thank you for your order.";

} catch (Exception $e) {
    die("Payment failed: " . $e->getMessage());
}
?>
