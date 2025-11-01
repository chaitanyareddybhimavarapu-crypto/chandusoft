<?php
// app/config.php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE); // Suppress deprecated and notice warnings

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables or define directly here
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'chandusoft';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

// PayPal API credentials (sandbox)
$PAYPAL_CLIENT_ID = getenv('PAYPAL_CLIENT_ID') ?: 'ARM375iNx3xH7GY9tDWGqPbIoASrXuLrzMPneG9KnV_1preXUCf2tdIeKF7Alqw3DuhremaHrr5x5JXK'; // Replace with your sandbox client ID
$PAYPAL_SECRET = getenv('PAYPAL_SECRET') ?: 'EHXvqbutdXCuFsl6fPkSPSiOqV-5zBpFGAESii_ACZ8DLEumi8auz8jbJWhbQNnIQ-mhuw73noHbCUnl'; // Replace with your sandbox secret

// Initialize PDO connection for the database
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// PayPal API context setup (for PayPal integration)
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

// Initialize the PayPal API context (for API calls)
$paypal = new ApiContext(
    new OAuthTokenCredential(
        $PAYPAL_CLIENT_ID,  // PayPal Client ID (sandbox)
        $PAYPAL_SECRET      // PayPal Secret (sandbox)
    )
);
$paypal->setConfig([
    'mode' => 'sandbox',  // Ensure you are using sandbox for testing
    'log.LogEnabled' => true,
    'log.FileName' => __DIR__ . '/paypal.log',  // Path to log PayPal API requests
    'log.LogLevel' => 'DEBUG'
]);

?>
