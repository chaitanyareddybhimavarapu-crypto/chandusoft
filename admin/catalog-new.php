<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/upload.php';  // Ensure this is included for the uploadImage function

$errors = [];
$title = '';
$slug = '';
$price = '';
$short_desc = '';
$status = 'published';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $status = $_POST['status'] ?? 'published';

    // Basic validation checks
    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if ($slug === '') {
        $errors[] = 'Slug is required.';
    }
    if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
        $errors[] = 'Slug can only contain lowercase letters, numbers, and hyphens.';
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a non-negative number.';
    }

    // Image upload handling
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        // Call the uploadImage function with the $_FILES array
        $uploaded = uploadImage($_FILES['image']);
        if ($uploaded === false) {
            $errors[] = 'Invalid image upload. Allowed types: jpg, png, gif, webp. Max size 2MB.';
        } else {
            $image_path = $uploaded;  // Store the uploaded image path
        }
    } else {
        $errors[] = 'Product image is required.';
    }

    // If no validation errors, proceed to insert into the database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO catalog_items (slug, title, price, short_desc, image_path, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$slug, $title, $price, $short_desc, $image_path, $status]);

            header('Location: catalog.php');  // Redirect to catalog after successful insert
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {  // Duplicate slug error code
                $errors[] = 'Slug already exists. Choose a different one.';
            } else {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New Catalog Item</title>
</head>
<body>
    <h1>Add New Catalog Item</h1>

    <!-- Display errors if there are any -->
    <?php if ($errors): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Catalog Item Form -->
    <form method="post" enctype="multipart/form-data" action="catalog-new.php">
        <label>
            Title:<br />
            <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </label><br /><br />

        <label>
            Slug:<br />
            <input type="text" name="slug" value="<?= htmlspecialchars($slug) ?>" required placeholder="lowercase letters, numbers, hyphens only">
        </label><br /><br />

        <label>
            Price:<br />
            <input type="number" name="price" value="<?= htmlspecialchars($price) ?>" step="0.01" min="0" required>
        </label><br /><br />

        <label>
            Short Description:<br />
            <textarea name="short_desc" rows="4"><?= htmlspecialchars($short_desc) ?></textarea>
        </label><br /><br />

        <label>
            Status:<br />
            <select name="status">
                <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
        </label><br /><br />

        <label>
            Product Image:<br />
            <input type="file" name="image" accept="image/*" required>
        </label><br /><br />

        <button type="submit">Add Item</button>
    </form>

    <p><a href="catalog.php">Back to catalog list</a></p>
</body>
</html>
