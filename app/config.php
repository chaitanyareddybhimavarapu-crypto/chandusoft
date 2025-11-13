<?php
// app/config.php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require_once __DIR__ . '/../vendor/autoload.php';
 
use Dotenv\Dotenv;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
 
// ✅ Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
 
// ✅ Database connection
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? 'chandusoft';
$DB_USER = $_ENV['DB_USER'] ?? 'root';
$DB_PASS = $_ENV['DB_PASS'] ?? '';
 
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
 
// ✅ PayPal configuration
$PAYPAL_CLIENT_ID = $_ENV['PAYPAL_CLIENT_ID'] ?? '';
$PAYPAL_SECRET    = $_ENV['PAYPAL_SECRET'] ?? '';
 
$paypal = new ApiContext(
    new OAuthTokenCredential($PAYPAL_CLIENT_ID, $PAYPAL_SECRET)
);
$paypal->setConfig([
    'mode' => ($_ENV['APP_ENV'] ?? 'local') === 'production' ? 'live' : 'sandbox',
    'log.LogEnabled' => true,
    'log.FileName' => __DIR__ . '/paypal.log',
    'log.LogLevel' => 'DEBUG'
]);
 
// ✅ Stripe configuration
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
$stripePublishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
$stripeWebhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? ''; // ✅ add this line
 
// ✅ Mail configuration (example)
$mailConfig = [
    'host' => $_ENV['MAIL_HOST'] ?? 'smtp.example.com',
    'port' => $_ENV['MAIL_PORT'] ?? 587,
    'user' => $_ENV['MAIL_USER'] ?? '',
    'pass' => $_ENV['MAIL_PASS'] ?? ''
];
 
// ✅ Cloudflare Turnstile
$TURNSTILE_SITE   = $_ENV['TURNSTILE_SITE'] ?? '';
$TURNSTILE_SECRET = $_ENV['TURNSTILE_SECRET'] ?? '';