<?php
session_start();
 
require_once __DIR__ . '/../app/config.php'; // Your PDO setup etc
require_once __DIR__ . '/../app/env.php';
 
loadDotEnv(); // Load .env vars into environment
 
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if ($slug === '') {
    die('Missing or invalid slug.');
}
 
// Fetch product by slug and published status
$stmt = $pdo->prepare("SELECT * FROM catalog_items WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$item) {
    die('Product not found or not published.');
}
 
// Get Turnstile keys from env helper
$TURNSTILE_SITE = env('TURNSTILE_SITE');
$TURNSTILE_SECRET = env('TURNSTILE_SECRET');
 
// Generate CSRF token if not set
if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}
 
$errors = [];
$success = false;
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    $csrfToken = $_POST['_csrf'] ?? '';
 
    // Validate CSRF token
    if (!$csrfToken || !hash_equals($_SESSION['_csrf'], $csrfToken)) {
        $errors[] = 'Invalid CSRF token.';
    }
 
    // Validate form fields
    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($message === '') $errors[] = 'Message is required.';
    if (!$turnstileToken) $errors[] = 'CAPTCHA validation is required.';
 
    // Verify Turnstile CAPTCHA if no errors so far
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
 
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO catalog_enquiries (item_id, name, email, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $item['id'],
                $name,
                $email,
                $message
            ]);
            $success = true;
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
 
// JSON-LD Product schema for SEO
$schema = [
    "@context" => "https://schema.org/",
    "@type" => "Product",
    "name" => $item['title'],
    "image" => (isset($item['image_path']) && $item['image_path']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/' . $item['image_path'] : null,
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
 
        h1 {
            text-align: center;
            margin-top: 40px;
            color: #0056b3;
        }
 
        .product-image {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            display: block;
            margin: 0 auto 20px;
        }
 
        .product-details {
            width: 80%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
 
        .product-details p {
            font-size: 16px;
            line-height: 1.6;
        }
 
        .product-price {
            font-size: 20px;
            color: #007bff;
            font-weight: bold;
        }
 
        .form-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
 
        .form-container h2 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 20px;
        }
 
        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
 
        .form-container label {
            font-size: 16px;
            font-weight: bold;
        }
 
        .form-container input,
        .form-container textarea {
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
 
        .form-container button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
 
        .form-container button:hover {
            background-color: #0056b3;
        }
 
        .error-list {
            color: red;
            list-style: none;
            padding-left: 0;
        }
 
        .error-list li {
            font-size: 14px;
        }
 
        .success-message {
            color: green;
            font-size: 16px;
        }
 
        .cf-turnstile {
            margin-top: 10px;
        }
 
        .back-to-catalog {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }
 
        .back-to-catalog a {
            color: #007bff;
            text-decoration: none;
        }
 
        .back-to-catalog a:hover {
            text-decoration: underline;
        }
 
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .product-details {
                width: 90%;
            }
 
            .product-image {
                max-height: 300px;
            }
 
            .form-container {
                width: 90%;
            }
 
            .form-container input,
            .form-container textarea {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="product-details">
        <h1><?= htmlspecialchars($item['title']) ?></h1>
 
        <?php if (!empty($item['image_path'])): ?>
            <picture>
                <!-- WebP Image (if available) -->
                <?php if (!empty($item['webp_image_path'])): ?>
                    <source srcset="../public/<?= htmlspecialchars($item['webp_image_path']) ?>" type="image/webp">
                <?php endif; ?>
 
                <!-- Fallback for browsers that do not support WebP -->
                <img src="../public/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="product-image" loading="lazy" width="400" height="400">
            </picture>
        <?php endif; ?>
 
        <p><?= nl2br(htmlspecialchars($item['short_desc'])) ?></p>
        <p class="product-price"><strong>Price:</strong> $<?= number_format($item['price'], 2) ?></p>
    </div>
 
    <div class="form-container">
        <h2>Send an Enquiry</h2>
 
        <?php if ($success): ?>
            <p class="success-message">Thank you for your enquiry. We will get back to you soon.</p>
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
                <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
 
                <label>Email:</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
 
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
        <p><a href="catalog.php">Back to Catalog</a></p>
    </div>
</body>
</html>
 