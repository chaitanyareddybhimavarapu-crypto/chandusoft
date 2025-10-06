<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get role from session
$user = $_SESSION['user'];
$role = $user['role'];

// Fetch pages
$stmt = $pdo->query("SELECT * FROM pages ORDER BY updated_at DESC");
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pages</title>
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
    font-weight: 500;
  }

  .navbar a:hover {
    text-decoration: underline;
  }

  .container {
    max-width: 1100px;
    margin: 30px auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  h2 {
    color: #2c3e50;
    margin-bottom: 20px;
  }

  .create-btn {
    display: inline-block;
    background-color: #28a745;
    color: white;
    padding: 10px 16px;
    margin-bottom: 20px;
    border: none;
    border-radius: 4px;
    font-size: 15px;
    cursor: pointer;
    text-decoration: none;
  }

  .create-btn:hover {
    background-color: #218838;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  table thead {
    background-color: #3498db;
    color: white;
  }

  th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
    font-size: 14px;
  }

  tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  .btn {
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    margin-right: 6px;
    text-decoration: none;
  }

  .btn-edit {
    background-color: #007bff;
    color: white;
  }

  .btn-edit:hover {
    background-color: #0056b3;
  }

  .btn-archive {
    background-color: orange;
    color: white;
  }

  .btn-archive:hover {
    background-color: #e69500;
  }

  .btn-delete {
    background-color: red;
    color: white;
  }

  .btn-delete:hover {
    background-color: #c00000;
  }

  @media (max-width: 768px) {
    .container {
      padding: 20px;
    }

    th, td {
      font-size: 13px;
      padding: 10px;
    }

    .btn {
      font-size: 13px;
      padding: 5px 8px;
    }

    .create-btn {
      font-size: 14px;
      padding: 8px 12px;
    }
  }
</style>

</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">Chandusoft Admin</div>
    <div class="menu">
        <span>Welcome <?= htmlspecialchars(ucfirst($user['role'])) ?>!</span>
        <a href="dashboard.php">Dashboard</a>
        <a href="admin-leads.php">Leads</a>
        <a href="pages.php">Pages</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Pages</h2>
    <a href="create-page.php" class="btn btn-edit">+ Create New Page</a>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
                <tr>
                    <td><?= htmlspecialchars($page['title']) ?></td>
                    <td><?= htmlspecialchars($page['slug']) ?></td>
                    <td><?= htmlspecialchars($page['status']) ?></td>
                    <td><?= htmlspecialchars($page['updated_at']) ?></td>
                    <td>
                        <a class="btn btn-edit" href="edit-page.php?id=<?= $page['id'] ?>">Edit</a>

                        <?php if ($role === 'admin'): ?>
                            <a class="btn btn-archive" href="archive-page.php?id=<?= $page['id'] ?>">Archive</a>
                            <a class="btn btn-delete" href="delete-page.php?id=<?= $page['id'] ?>"
                               onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
