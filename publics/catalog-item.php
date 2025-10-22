<?php
require_once __DIR__ . '/../app/config.php';

// Get slug parameter
$slug = $_GET['slug'] ?? '';

if (!$slug) {
    die('Invalid product.');
}

$stmt = $pdo->prepare("SELECT * FROM catalog_items WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Product not found.');
}

$errors = [];
$success = '';

// Handle enquiry form POST with Turnstile verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $token = $_POST['cf-turnstile-response'] ?? '';

    if (!$name || !$email || !$message) {
        $errors[] = 'All fields are required.';
    }

    // Turnstile server-side verification
    $secret = getenv('TURNSTILE_SECRET');
    $response = file_get_contents("https://challenges.cloudflare.com/turnstile/v0/siteverify", false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query([
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ]),
        ],
    ]));

    $result = json_decode($response, true);

    if (!$result['success']) {
        $errors[] = 'Turnstile verification failed.';
    }

    if (empty($errors)) {
        // You can add email sending or DB saving here
        $success = "Thank you for your enquiry. We will get back to you shortly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($product['title']) ?></title>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "<?= htmlspecialchars($product['title']) ?>",
        "image": "<?= htmlspecialchars('../public/uploads/' . $product['image_path']) ?>",
        "description": "<?= htmlspecialchars($product['short_desc']) ?>",
        "sku": "<?= htmlspecialchars($product['slug']) ?>",
        "offers": {
            "@type": "Offer",
            "priceCurrency": "USD",
            "price": "<?= number_format($product['price'], 2, '.', '') ?>",
            "availability": "https://schema.org/InStock"
        }
    }
    </script>

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
    <h1><?= htmlspecialchars($product['title']) ?></h1>
    <img src="../public/uploads/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" style="max-width: 400px; max-height: 400px; object-fit: contain;">
    <p><?= nl2br(htmlspecialchars($product['short_desc'])) ?></p>
    <p><strong>Price: </strong>$<?= number_format($product['price'], 2) ?></p>

    <?php if ($errors): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post" action="catalog-item.php?slug=<?= urlencode($slug) ?>">
        <label>Your Name:<br />
            <input type="text" name="name" required>
        </label><br /><br />
        <label>Your Email:<br />
            <input type="email" name="email" required>
        </label><br /><br />
        <label>Message:<br />
            <textarea name="message" required></textarea>
        </label><br /><br />

        <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars(getenv('TURNSTILE_SITE')) ?>"></div><br />

        <button type="submit">Send Enquiry</button>
    </form>
</body>
</html>
