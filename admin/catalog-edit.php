<?php
session_start();

// Ensure user logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Fetch role from session
$user = $_SESSION['user'];
$role = $user['role'] ?? 'user'; // default role

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/upload.php';
require_once __DIR__ . '/../app/logger.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Mailpit Function
function sendMailpitNotification($subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = '127.0.0.1';
        $mail->Port = 1025;
        $mail->SMTPAuth = false;
        $mail->setFrom('no-reply@example.com', 'Catalog App');
        $mail->addAddress('admin@example.com');
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
    } catch (Exception $e) {
        log_error("Mailpit Send Error: {$mail->ErrorInfo}");
    }
}

// LOAD ITEM
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die('Invalid item ID.');

$stmt = $pdo->prepare("SELECT * FROM catalog_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) die('Item not found.');

$errors = [];
$title = $item['title'];
$slug = $item['slug'];
$price = $item['price'];
$short_desc = $item['short_desc'];
$status = $item['status'];
$currentImage = $item['image_path'];

// FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $status = $_POST['status'] ?? 'published';

    if ($title === '') $errors[] = 'Title is required.';
    if ($slug === '') $errors[] = 'Slug is required.';
    elseif (!preg_match('/^[a-z0-9\-]+$/', $slug))
        $errors[] = 'Slug can only contain lowercase letters, numbers, and hyphens.';
    if (!is_numeric($price) || $price < 0)
        $errors[] = 'Price must be a non-negative number.';

    $uploaded = null;
    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadImage($_FILES['image']);
        if ($uploaded === false) {
            $errorMsg = 'Invalid image upload. Allowed: jpg, png, gif, webp. Max size 2MB.';
            $errors[] = $errorMsg;
            logCatalogAction("Image upload failed for '{$title}' ID {$id}");
            sendMailpitNotification("Catalog Edit - Image Upload Failed", "Item ID: $id\nError: $errorMsg");
        }
    }

    if (empty($errors)) {
        try {
            if ($uploaded !== null) {
                $sql = "UPDATE catalog_items 
                        SET slug=?, title=?, price=?, short_desc=?, image_path=?, status=?, updated_at=NOW() 
                        WHERE id=?";
                $params = [$slug, $title, $price, $short_desc, $uploaded, $status, $id];
            } else {
                $sql = "UPDATE catalog_items 
                        SET slug=?, title=?, price=?, short_desc=?, status=?, updated_at=NOW() 
                        WHERE id=?";
                $params = [$slug, $title, $price, $short_desc, $status, $id];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            logCatalogAction("Item Updated: ID=$id Title='$title'");
            sendMailpitNotification("Catalog Item Updated", "ID: $id\nTitle: $title");

            header('Location: catalog.php');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Slug already exists.";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Catalog Item</title>

<style>
body { font-family: Arial; margin: 0; background: #f2f2f2; }
.navbar a { color: white; margin-left: 20px; text-decoration: none; padding-bottom: 5px; }
.navbar a.active {
    color: #007bff;        /* blue text */
    text-decoration: underline;
    font-weight: bold;
}

.container { background: white; width: 600px; margin: 30px auto; padding: 25px; border-radius: 8px; }
input, textarea, select { width: 100%; padding: 8px; margin: 8px 0; }
button { padding: 10px 20px; background: #027cf7ff; color: white; border: none; cursor: pointer; }
.error-list { background: #ffcccc; padding: 10px; border-left: 4px solid red; }
</style>

</head>
<body>

<!-- ROLE-BASED NAVBAR -->
<div class="navbar" style="background:#2c3e50;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;color:white;">
    <div><strong>Chandusoft Admin</strong></div>

    <div>
        <span>Welcome <?= htmlspecialchars(ucfirst($role)) ?>!</span>

        <a href="/dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':'' ?>">Dashboard</a>

        <?php if ($role === 'admin'): ?>
            <a href="/admin/catalog.php"
               class="<?= strpos($_SERVER['REQUEST_URI'],'catalog')!==false ? 'active' : '' ?>">
               Admin Catalog
            </a>
            <a href="/public/catalog.php"
               class="<?= strpos($_SERVER['REQUEST_URI'],'/public/')!==false ? 'active' : '' ?>">
               Public Catalog
            </a>
            <a href="/admin/orders.php"
               class="<?= basename($_SERVER['PHP_SELF'])==='orders.php'?'active':'' ?>">
               Orders
            </a>
        <?php elseif ($role === 'editor'): ?>
            <a href="/public/catalog.php"
               class="<?= strpos($_SERVER['REQUEST_URI'],'/public/')!==false ? 'active' : '' ?>">
               Public Catalog
            </a>
        <?php endif; ?>

        <a href="/admin-leads.php" class="<?= basename($_SERVER['PHP_SELF'])==='admin-leads.php'?'active':'' ?>">Leads</a>
        <a href="/pages.php" class="<?= basename($_SERVER['PHP_SELF'])==='pages.php'?'active':'' ?>">Pages</a>
        <a href="/logout.php" class="<?= basename($_SERVER['PHP_SELF'])==='logout.php'?'active':'' ?>">Logout</a>
    </div>
</div>
<!-- END NAV -->

<div class="container">
    <h1>Edit Catalog Item</h1>

    <?php if ($errors): ?>
        <div class="error-list">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="catalog-edit.php?id=<?= $id ?>">

        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>">

        <label>Slug</label>
        <input type="text" name="slug" value="<?= htmlspecialchars($slug) ?>">

        <label>Price</label>
        <input type="number" name="price" value="<?= htmlspecialchars($price) ?>" step="0.01">

        <label>Short Description</label>
        <textarea name="short_desc"><?= htmlspecialchars($short_desc) ?></textarea>

        <label>Status</label>
        <select name="status">
            <option value="published" <?= $status==='published'?'selected':'' ?>>Published</option>
            <option value="draft" <?= $status==='draft'?'selected':'' ?>>Draft</option>
        </select>

        <label>Current Image:</label><br>
        <?php if ($currentImage): ?>
            <img class="thumb" src="../public/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                <?php else: ?>
        <?php endif; ?>

        <label>Upload New Image</label>
        <input type="file" name="image">

        <br><br>
        <button type="submit">Update Item</button>
    </form>
</div>

</body>
</html>
