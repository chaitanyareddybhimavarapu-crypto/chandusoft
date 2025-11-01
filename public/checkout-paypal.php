<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Load products from DB
$ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
$stmt = $pdo->query("SELECT id, title, price FROM catalog_items WHERE id IN ($ids) AND status='published'");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create PayPal Payer object
$payer = new Payer();
$payer->setPaymentMethod('paypal');

// Prepare items for PayPal
$items = [];
$total = 0;
foreach ($products as $p) {
    $qty = $_SESSION['cart'][$p['id']];
    $item = new Item();
    $item->setName($p['title'])
         ->setCurrency('USD')
         ->setQuantity($qty)
         ->setPrice($p['price']);
    $items[] = $item;
    $total += $p['price'] * $qty;
}
$itemList = new ItemList();
$itemList->setItems($items);

// Define the payment amount
$amount = new Amount();
$amount->setCurrency("USD")->setTotal($total);

// Create a transaction
$transaction = new Transaction();
$transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Chandusoft Catalog Order");

// Define the return and cancel URLs
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl("http://localhost/public/paypal-success.php")
             ->setCancelUrl("http://localhost/public/cart.php");

// Create the payment object
$payment = new Payment();
$payment->setIntent('sale')
        ->setPayer($payer)
        ->setTransactions([$transaction])
        ->setRedirectUrls($redirectUrls);

try {
    // Execute the payment creation
    $payment->create($paypal); // Assuming $paypal is your configured PayPal API context

    // Save the order ID in the session before redirecting
    $_SESSION['order_id'] = $pdo->lastInsertId();

    // Redirect to PayPal for approval
    header("Location: " . $payment->getApprovalLink());
    exit;

} catch (Exception $e) {
    die($e->getMessage());
}
?>
