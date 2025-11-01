<?php
session_start();

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/env.php';
require_once __DIR__ . '/../vendor/autoload.php'; // ✅ PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

loadDotEnv(); // Load environment variables

// ✅ Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ✅ Add to Cart handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    // Redirect to cart page
    header("Location: cart.php");
    exit;
}

// ✅ Mailpit email sender function
function sendMailpitEmail($subject, $body)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = '127.0.0.1'; // Mailpit SMTP
        $mail->Port = 1025;
        $mail->SMTPAuth = false;

        $mail->setFrom('no-reply@chandusoft.test', 'Chandusoft Catalog');
        $mail->addAddress('admin@chandusoft.test'); // Mailpit inbox recipient

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Mailpit send error: " . $mail->ErrorInfo);
    }
}

// ✅ Get product by slug
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if ($slug === '') {
    die('Missing or invalid slug.');
}

$stmt = $pdo->prepare("SELECT * FROM catalog_items WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die('Product not found or not published.');
}

$TURNSTILE_SITE = env('TURNSTILE_SITE');
$TURNSTILE_SECRET = env('TURNSTILE_SECRET');

// ✅ CSRF token
if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = false;

// ✅ Handle enquiry form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    $csrfToken = $_POST['_csrf'] ?? '';

    // CSRF validation
    if (!$csrfToken || !hash_equals($_SESSION['_csrf'], $csrfToken)) {
        $errors[] = 'Invalid CSRF token.';
    }

    // Field validation
    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($message === '') $errors[] = 'Message is required.';
    if (!$turnstileToken) $errors[] = 'CAPTCHA validation is required.';

    // ✅ Verify Turnstile CAPTCHA
    if (empty($errors)) {
        $response = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query([
                    'secret' => $TURNSTILE_SECRET,
                    'response' => $turnstileToken,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ]),
            ],
        ]));
        $json = json_decode($response, true);
        if (empty($json['success'])) {
            $errors[] = 'CAPTCHA verification failed.';
        }
    }

    // ✅ Save and email
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO catalog_enquiries (item_id, name, email, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$item['id'], $name, $email, $message]);
            $success = true;

            // Send email to Mailpit
            $subject = "New Enquiry for {$item['title']}";
            $body = "A new enquiry was submitted for your catalog item:\n\n" .
                    "🆔 Item ID: {$item['id']}\n" .
                    "📘 Title: {$item['title']}\n" .
                    "👤 Name: {$name}\n" .
                    "📧 Email: {$email}\n\n" .
                    "💬 Message:\n{$message}\n\n" .
                    "⏰ Sent at: " . date('Y-m-d H:i:s');

            sendMailpitEmail($subject, $body);

            // Reset CSRF token
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// ✅ JSON-LD SEO schema
$schema = [
    "@context" => "https://schema.org/",
    "@type" => "Product",
    "name" => $item['title'],
    "image" => (!empty($item['image_path']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/' . $item['image_path'] : null),
    "description" => $item['short_desc'],
    "offers" => [
        "@type" => "Offer",
        "priceCurrency" => "USD",
        "price" => $item['price'],
        "availability" => "https://schema.org/InStock",
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($item['title']) ?></title>

    <script type="application/ld+json">
        <?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; margin: 0; padding: 0; }
        h1 { text-align: center; margin-top: 40px; color: #0056b3; }
        .product-details, .form-container {
            width: 80%; max-width: 800px; margin: 20px auto; padding: 20px; background: #fff;
            border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-image { max-width: 100%; max-height: 400px; display: block; margin: 0 auto 20px; object-fit: contain; }
        .product-price { font-size: 20px; color: #007bff; font-weight: bold; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #0056b3; }
        .error-list { color: red; list-style: none; padding: 0; }
        .success-message { color: green; font-weight: bold; }
        .back-to-catalog { text-align: center; margin-top: 20px; }
        .back-to-catalog a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="product-details">
        <h1><?= htmlspecialchars($item['title']) ?></h1>
        <?php if (!empty($item['image_path'])): ?>
            <img src="../public/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="product-image">
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars($item['short_desc'])) ?></p>
        <p class="product-price"><strong>Price:</strong> $<?= number_format($item['price'], 2) ?></p>

        <!-- 🛒 Add to Cart Form -->
        <form method="post" action="">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']) ?>">
            <label>Quantity:</label>
            <input type="number" name="quantity" value="1" min="1" style="width: 70px;">
            <button type="submit" name="add_to_cart">🛒 Add to Cart</button>
        </form>
    </div>

    <div class="form-container">
        <h2>Send an Enquiry</h2>

        <?php if ($success): ?>
            <p class="success-message">✅ Thank you for your enquiry. We'll contact you soon.</p>
        <?php else: ?>
            <?php if ($errors): ?>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="catalog-item.php?slug=<?= urlencode($slug) ?>">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">

                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

                <label>Message:</label>
                <textarea name="message" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

                <?php if ($TURNSTILE_SITE): ?>
                    <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($TURNSTILE_SITE) ?>"></div>
                <?php else: ?>
                    <p><em>CAPTCHA not configured.</em></p>
                <?php endif; ?>

                <button type="submit">Send Enquiry</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="back-to-catalog">
        <a href="catalog.php">← Back to Catalog</a> |
        <a href="cart.php">🛒 View Cart</a>
    </div>
</body>
</html>
