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
        .product-image {
            max-width: 400px;
            max-height: 400px;
            object-fit: contain;
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin-bottom: 8px;
        }
        form input, form textarea {
            width: 100%;
            max-width: 400px;
            padding: 8px;
            margin-bottom: 15px;
        }
        .error-list {
            color: red;
            list-style: none;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>
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
    <p><strong>Price:</strong> $<?= number_format($item['price'], 2) ?></p>

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

            <label>
                Name:
                <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </label>

            <label>
                Email:
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </label>

            <label>
                Message:
                <textarea name="message" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </label>

            <?php if ($TURNSTILE_SITE): ?>
                <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars($TURNSTILE_SITE) ?>"></div>
            <?php else: ?>
                <p><em>CAPTCHA not configured.</em></p>
            <?php endif; ?>

            <button type="submit">Send Enquiry</button>
        </form>
    <?php endif; ?>

    <p><a href="catalog.php">Back to Catalog</a></p>
</body>
</html>
