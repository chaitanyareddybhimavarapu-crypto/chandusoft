<?php
// Start session and connect to DB
session_start();

// Check login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$name = htmlspecialchars($user['name']);
$role = $user['role'] ?? 'user';

// Database (PDO)
$host = 'localhost';
$db   = 'chandusoft';
$userDB = 'root';
$passDB = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $userDB, $passDB, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Handle Form Submit
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $content_html = $_POST['content_html'] ?? '';

    if ($title === '') {
        $error = '⚠️ Title is required.';
    } else {
        // Auto-generate slug if empty
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        }

        $updated_at = date('Y-m-d H:i:s');

        try {
            $stmt = $pdo->prepare("INSERT INTO pages (title, slug, status, content_html, updated_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $status, $content_html, $updated_at]);

            header("Location: pages.php?created=1");
            exit;
        } catch (PDOException $e) {
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Page</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6fa;
            margin: 0;
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

        .container {
            max-width: 700px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
        }

        h1 {
            margin-top: 0;
            color: #2c3e50;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        textarea {
            font-family: monospace;
            resize: vertical;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 18px;
            background-color: #3498db;
            color: white;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>Chandusoft Admin</strong></div>
    <div>
        <span>Welcome <?= ucfirst($role) ?>!</span>
        <a href="dashboard.php">Dashboard</a>
        <a href="pages.php">Pages</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Create New Page</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="create.php">
        <label for="title">Page Title *</label>
        <input type="text" name="title" id="title" required>

        <label for="slug">Slug (optional)</label>
        <input type="text" name="slug" id="slug" placeholder="leave blank to auto-generate">

        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
        </select>

        <label for="content_html">Page Content (HTML)</label>
        <textarea name="content_html" id="content_html" rows="10" placeholder="Enter HTML content here..."></textarea>

        <button type="submit" class="btn">Create Page</button>
    </form>

    <a href="pages.php" class="back-link">← Back to Pages</a>
</div>

</body>
</html>
