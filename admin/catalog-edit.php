<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/upload.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Invalid item ID.');
}

// Fetch existing item
$stmt = $pdo->prepare("SELECT * FROM catalog_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die('Item not found.');
}

$errors = [];
$title = $item['title'];
$slug = $item['slug'];
$price = $item['price'];
$short_desc = $item['short_desc'];
$status = $item['status'];
$currentImage = $item['image_path'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $status = $_POST['status'] ?? 'published';

    // Validation
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

    // Image upload (optional)
    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadImage($_FILES['image']);
        if ($uploaded === false) {
            $errors[] = 'Invalid image upload. Allowed types: jpg, png, gif, webp. Max size 2MB.';
        }
    } else {
        $uploaded = null;
    }

    if (empty($errors)) {
        try {
            if ($uploaded !== null) {
                // Replace image path
                $sql = "UPDATE catalog_items SET slug=?, title=?, price=?, short_desc=?, image_path=?, status=? WHERE id=?";
                $params = [$slug, $title, $price, $short_desc, $uploaded, $status, $id];
            } else {
                $sql = "UPDATE catalog_items SET slug=?, title=?, price=?, short_desc=?, status=? WHERE id=?";
                $params = [$slug, $title, $price, $short_desc, $status, $id];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            header('Location: catalog.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // duplicate slug
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
    <title>Edit Catalog Item</title>
</head>
<body>
    <h1>Edit Catalog Item</h1>

    <?php if ($errors): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="catalog-edit.php?id=<?= $id ?>">
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
            Current Image:<br />
            <?php if ($currentImage): ?>
                <img src="../public/<?= htmlspecialchars($currentImage) ?>" alt="Current Image" style="max-width: 200px; display: block; margin-bottom: 10px;">
            <?php else: ?>
                No image uploaded.
            <?php endif; ?>
        </label><br />

        <label>
            Replace Image (optional):<br />
            <input type="file" name="image" accept="image/*">
        </label><br /><br />

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="catalog.php">Back to catalog list</a></p>
</body>
</html>
