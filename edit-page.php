<?php
session_start();
require 'db.php'; // Your PDO connection

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
    $status = $_POST['status'] ?? 'Draft';
    $content_html = $_POST['content_html'] ?? '';

    if (empty($pageTitle)) {
        $message = "<div class='alert alert-danger'>Page Title is required.</div>";
    } else {
        // Update in DB
        $updateStmt = $pdo->prepare("UPDATE pages SET title = ?, slug = ?, status = ?, content_html = ?, updated_at = NOW() WHERE id = ?");
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
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Chandusoft Admin</a>
        <div>
            <span class="navbar-text text-white me-3">Welcome <?= htmlspecialchars($_SESSION['user']['role']) ?>!</span>
            <a href="dashboard.php" class="text-white me-2">Dashboard</a>
            <a href="admin-leads.php" class="text-white me-2">Leads</a>
            <a href="pages.php" class="text-white me-2">Pages</a>
            <a href="logout.php" class="text-white">Logout</a>
        </div>
    </div>
</nav>

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
                <label for="content_html" class="form-label">Page Content (HTML)</label>
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
