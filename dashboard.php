<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$name = htmlspecialchars($user['name']);
$role = $user['role'] ?? 'user';

// Fetch stats
$totalLeads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$publishedPages = $pdo->query("SELECT COUNT(*) FROM pages WHERE status = 'published'")->fetchColumn();
$draftPages = $pdo->query("SELECT COUNT(*) FROM pages WHERE status = 'draft'")->fetchColumn();

// Last 5 leads
$stmt = $pdo->query("SELECT name, email, message, created_at FROM leads ORDER BY id DESC LIMIT 5");
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
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
      text-decoration: underline;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
    }

    h1, h2, h3 {
      margin-top: 0;
      color: #2c3e50;
    }

    .stats {
      margin-bottom: 20px;
      list-style: none;
      padding: 0;
    }

    .stats li {
      margin-bottom: 6px;
      color: #444;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #3498db;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

<div class="navbar">
  <div><strong>Chandusoft Admin</strong></div>
  <div>
    <span>Welcome <?= htmlspecialchars(ucfirst($role)) ?>!</span>
    <a href="dashboard.php">Dashboard</a>
    <a href="admin-leads.php">Leads</a>
    <a href="pages.php">Pages</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <h1>Dashboard</h1>
  <p>Hello, <?= $name ?> 👋</p>

  <ul class="stats">
    <li>✅ Total leads: <?= $totalLeads ?></li>
    <li>📄 Pages published: <?= $publishedPages ?></li>
    <li>📝 Pages draft: <?= $draftPages ?></li>
  </ul>

  <h3>Last 5 Leads</h3>
  <table>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Message</th>
      <th>Created</th>
     <th>IP</th>

    </tr>
    <?php foreach ($leads as $lead): ?>
      <tr>
        <td><?= htmlspecialchars($lead['name']) ?></td>
        <td><?= htmlspecialchars($lead['email']) ?></td>
        <td><?= htmlspecialchars($lead['message']) ?></td>
        <td><?= htmlspecialchars($lead['created_at']) ?></td>
        <td><?= isset($lead['ip']) ? htmlspecialchars($lead['ip']) : '' ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>
