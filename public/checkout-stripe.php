<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;

// ✅ Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// ✅ Must have pending order created from checkout.php
if (!isset($_SESSION['order_id'], $_SESSION['order_ref'], $_SESSION['user_details'])) {
    header("Location: checkout.php");
    exit;
}

$orderId  = $_SESSION['order_id'];
$orderRef = $_SESSION['order_ref'];

// ✅ Validate order from DB
$stmtOrder = $pdo->prepare("
    SELECT total_amount, status 
    FROM orders 
    WHERE id=? AND order_ref=? 
    LIMIT 1
");
$stmtOrder->execute([$orderId, $orderRef]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Invalid order reference! ❌");
}

if ($order['status'] !== 'pending') {
    die("Order already paid! ✅");
}

$dbTotal = floatval($order['total_amount']);

// ✅ Ensure DB total valid
if ($dbTotal <= 0) {
    die("Invalid order total ❌");
}

try {
    Stripe::setApiKey($stripeSecretKey);

    // ✅ Single consolidated Stripe item for order
    $lineItems = [[
        'price_data' => [
            'currency'     => 'usd',
            'product_data' => ['name' => "Order #$orderRef"],
            'unit_amount'  => intval(round($dbTotal * 100)), // convert to cents
        ],
        'quantity' => 1,
    ]];

    // ✅ Create Stripe Checkout session
    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'customer_email' => $_SESSION['user_details']['email'],
        'success_url' => 'https://karry-landlike-homophyly.ngrok-free.dev/public/checkout-success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => 'https://karry-landlike-homophyly.ngrok-free.dev/public/checkout-cancel.php',
        'metadata' => [
            'order_ref' => $orderRef,
        ]
    ]);

    // ✅ Save Stripe session ID for resuming payment later
    $pdo->prepare("UPDATE orders SET stripe_session_id=? WHERE id=?")
        ->execute([$session->id, $orderId]);

    // ✅ Redirect to Stripe payment page
    header("Location: " . $session->url);
    exit;

} catch (Exception $e) {

    error_log("Stripe Error: " . $e->getMessage());

    die("
        <h1>❌ Payment Setup Error</h1>
        <p>We could not connect to Stripe. Please try again.</p>
        <pre>" . htmlspecialchars($e->getMessage()) . "</pre>
    ");
}
