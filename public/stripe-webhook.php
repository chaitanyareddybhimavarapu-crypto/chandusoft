<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Webhook;

// ⚙️ Set your secret key directly (or replace env() if not using it)
Stripe::setApiKey('sk_test_51SNs3wAhJkYkXNzXsOYQJ2VYGfYIfcXd20lVISFv3Dq4W1eafNWaVQQQFEVUplug1FUx2jY4PDivCDC3bJSL2gX900hbscfEG2');
$endpointSecret = 'whsec_your_webhook_secret_here'; // Replace with your actual Stripe webhook secret

$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
} catch (Exception $e) {
    http_response_code(400);
    exit('Invalid signature: ' . $e->getMessage());
}

// ✅ Handle successful payments
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $orderId = $session->metadata->order_id ?? null;

    if ($orderId) {
        // ✅ Update only the `status` column
        $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
        $stmt->execute([$orderId]);
    }
}

http_response_code(200);
