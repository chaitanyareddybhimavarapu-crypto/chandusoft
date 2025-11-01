<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/upload.php';
require_once __DIR__ . '/../app/logger.php';
require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$title = '';
$slug = '';
$price = '';
$short_desc = '';
$status = 'published';
$image_path = '';

// --- Function to send email to Mailpit ---
function sendMailpitNotification($subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = '127.0.0.1'; // Mailpit host
        $mail->Port = 1025;        // Mailpit SMTP port
        $mail->SMTPAuth = false;

        $mail->setFrom('no-reply@example.com', 'Catalog App');
        $mail->addAddress('admin@example.com'); // Mailpit inbox

        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        log_error("Mailer Error: {$mail->ErrorInfo}");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $status = $_POST['status'] ?? 'published';

    // --- VALIDATION ---
    if ($title === '') $errors[] = 'Title is required.';
    if ($slug === '') $errors[] = 'Slug is required.';
    elseif (!preg_match('/^[a-z0-9\-]+$/', $slug)) $errors[] = 'Slug can only contain lowercase letters, numbers, and hyphens.';
    if ($price === '' || !is_numeric($price) || $price < 0) $errors[] = 'Price must be a non-negative number.';

    // --- IMAGE UPLOAD ---
    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadImage($_FILES['image']);
        if ($uploaded === false) {
            $errorMsg = 'Invalid image upload. Allowed: jpg, png, gif, webp. Max size 2MB.';
            $errors[] = $errorMsg;
            logCatalogAction("Image upload failed for new item '{$title}' (slug: {$slug}). Reason: {$errorMsg}");

            // ✅ Send Mailpit email for image upload failure
            sendMailpitNotification(
                'Catalog Image Upload Failed',
                "Image upload failed for catalog item:\nTitle: $title\nSlug: $slug\nReason: $errorMsg\nTime: ".date('Y-m-d H:i:s')
            );
        } else {
            $image_path = $uploaded;
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO catalog_items (slug, title, price, short_desc, image_path, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$slug, $title, $price, $short_desc, $image_path, $status]);

            // ✅ Log success
            logCatalogAction("New catalog item created: Title='{$title}', Slug='{$slug}', Price={$price}, Status='{$status}'");

            // ✅ Send Mailpit email for successful creation
            sendMailpitNotification(
                'New Catalog Item Created',
                "A new catalog item has been created:\nTitle: $title\nSlug: $slug\nPrice: $price\nStatus: $status\nTime: ".date('Y-m-d H:i:s')
            );

            header('Location: catalog.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMsg = 'Slug already exists. Please use a unique one.';
                $errors[] = $errorMsg;
                logCatalogAction("Catalog item creation failed - duplicate slug '{$slug}'.");

                sendMailpitNotification(
                    'Catalog Item Creation Failed - Duplicate Slug',
                    "Attempted to create a catalog item with duplicate slug: $slug\nTitle: $title\nTime: ".date('Y-m-d H:i:s')
                );
            } else {
                $errorMsg = 'Database error: ' . $e->getMessage();
                $errors[] = $errorMsg;
                log_error("Database error during catalog-new: " . $e->getMessage());
                logCatalogAction("Database error while creating '{$title}': " . $e->getMessage());

                sendMailpitNotification(
                    'Catalog Item Creation Failed - DB Error',
                    "Database error while creating catalog item:\nTitle: $title\nSlug: $slug\nError: {$e->getMessage()}\nTime: ".date('Y-m-d H:i:s')
                );
            }
        }
    } else {
        // ✅ Log validation failures
        logCatalogAction("Catalog item creation failed for '{$title}' due to validation errors: " . implode('; ', $errors));

        sendMailpitNotification(
            'Catalog Item Creation Failed - Validation Errors',
            "Validation errors occurred for catalog item:\nTitle: $title\nSlug: $slug\nErrors: ".implode('; ', $errors)."\nTime: ".date('Y-m-d H:i:s')
        );
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Catalog Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #0056b3;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-list {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create New Catalog Item</h1>

    <?php if ($errors): ?>
        <div class="error-list">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug:</label>
            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" placeholder="e.g., blue-tshirt" required>
        </div>

        <div class="form-group">
            <label for="price">Price ($):</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($price) ?>" step="0.01" min="0" required>
        </div>

        <div class="form-group">
            <label for="short_desc">Short Description:</label>
            <textarea id="short_desc" name="short_desc" rows="4"><?= htmlspecialchars($short_desc) ?></textarea>
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Upload Image (optional):</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit">Create Item</button>
    </form>

    <a href="catalog.php" class="back-link">Back to Catalog List</a>
</div>

</body>
</html>
