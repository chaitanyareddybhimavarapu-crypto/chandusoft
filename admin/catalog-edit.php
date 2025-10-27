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
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
 
        h1 {
            text-align: center;
            color: #0056b3;
            margin: 20px 0;
        }
 
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
 
        textarea {
            resize: vertical;
        }
 
        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
 
        button:hover {
            background-color: #0056b3;
        }
 
        .error-list {
            color: red;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
 
        .error-list li {
            margin: 5px 0;
        }
 
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            font-size: 16px;
            text-decoration: none;
        }
 
        .back-link:hover {
            text-decoration: underline;
        }
 
        .current-image {
            max-width: 200px;
            display: block;
            margin-bottom: 10px;
        }
 
        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
 
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
 
    <div class="container">
        <h1>Edit Catalog Item</h1>
 
        <!-- Display errors if there are any -->
        <?php if ($errors): ?>
            <div class="error-list">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
 
        <!-- Catalog Item Form -->
        <form method="post" enctype="multipart/form-data" action="catalog-edit.php?id=<?= $id ?>">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
            </div>
 
            <div class="form-group">
                <label for="slug">Slug:</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" required placeholder="lowercase letters, numbers, hyphens only">
            </div>
 
            <div class="form-group">
                <label for="price">Price:</label>
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
                <label for="current-image">Current Image:</label>
                <?php if ($currentImage): ?>
                    <img src="../public/<?= htmlspecialchars($currentImage) ?>" alt="Current Image" class="current-image">
                <?php else: ?>
                    No image uploaded.
                <?php endif; ?>
            </div>
 
            <div class="form-group">
                <label for="image">Replace Image (optional):</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
 
            <button type="submit">Save Changes</button>
        </form>
 
        <p><a href="catalog.php" class="back-link">Back to catalog list</a></p>
    </div>
 
</body>
</html>