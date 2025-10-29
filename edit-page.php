<?php
session_start();
require 'db.php'; // Your PDO connection

$user = $_SESSION['user'];
$name = htmlspecialchars($user['name']);
$role = $user['role'] ?? 'user';

// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$pageId = $_GET['id'] ?? null;
if (!$pageId || !is_numeric($pageId)) {
    die("❌ Invalid page ID.");
}

// Fetch existing page data
$stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->execute([$pageId]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    die("❌ Page not found.");
}

// Default values from DB
$pageTitle = $page['title'];
$slug = $page['slug'];
$status = $page['status'];
$content_html = $page['content_html'] ?? '';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $pageTitle = trim($_POST['pageTitle'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $status = strtolower($_POST['status'] ?? 'draft');

    $content_html = $_POST['content_html'] ?? '';

    if (empty($pageTitle)) {
        $message = "<div class='alert alert-danger'>Page Title is required.</div>";
    } else {
        // Update in DB
        $updateStmt = $pdo->prepare(
            "UPDATE pages SET title = ?, slug = ?, status = ?, content_html = ?, updated_at = NOW() WHERE id = ?"
        );
        $updateStmt->execute([$pageTitle, $slug, $status, $content_html, $pageId]);

        $message = "<div class='alert alert-success'>✅ Page updated successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="navbar">
  <div><strong>Chandusoft Admin</strong></div>
  <div>
    <span>Welcome <?= htmlspecialchars(ucfirst($role)) ?>!</span>
    <a href="dashboard.php">Dashboard</a>
    
    <!-- Dynamic catalog link based on user role -->
    <?php if ($role === 'admin'): ?>
        <a href="admin/catalog.php">Admin Catalog</a>
          <a href="public/catalog.php">Public Catalog</a>
    <?php elseif ($role === 'editor'): ?>
        <a href="public/catalog.php">Public Catalog</a>
    <?php endif; ?>

    <a href="admin-leads.php">Leads</a>
    <a href="pages.php">Pages</a>
    <a href="logout.php">Logout</a>
  </div>
</div>


<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h5>Edit Page</h5>

        <?= $message ?>

        <form method="post">
            <div class="mb-3">
                <label for="pageTitle" class="form-label">Page Title <span class="text-danger">*</span></label>
                <input type="text" name="pageTitle" id="pageTitle" class="form-control" required value="<?= htmlspecialchars($pageTitle) ?>">
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" value="<?= htmlspecialchars($slug) ?>">
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="Published" <?= $status === 'Published' ? 'selected' : '' ?>>Published</option>
                    <option value="Draft" <?= $status === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Archived" <?= $status === 'Archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="content_html" class="form-label">Content (HTML)</label>
                <textarea name="content_html" id="content_html" rows="10" class="form-control" placeholder="Enter HTML content..."><?= htmlspecialchars($content_html) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Page</button>
            <a href="pages.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 <style>
       body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6fa;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background-color: #2c3e50;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }

    .navbar a {
      color: white;
      margin-left: 15px;
      text-decoration: none;
    }

    .navbar a:hover {
      text-decoration: none;
    }
     </style>