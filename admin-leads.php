<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['flash'] = "Please log in first.";
    header("Location: login.php");
    exit;
}

// Get user info
$user = $_SESSION['user'];
$role = $user['role'];
$searchTerm = '';
$whereClause = '';

// Search logic
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
    $safeTerm = $pdo->quote('%' . $searchTerm . '%');
    $whereClause = "WHERE name LIKE $safeTerm OR email LIKE $safeTerm";
}

// Prepare SQL query
$sql = "SELECT name, email, message, created_at, ip FROM leads $whereClause ORDER BY id DESC";
$stmt = $pdo->query($sql);
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leads - Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f5f7fa;
        }

        .navbar {
            background: #2c3e50;
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        form.search-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        form.search-form input[type="text"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form.search-form button {
            padding: 8px 15px;
            background: #2980b9;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table thead {
            background-color: #3498db;
            color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .welcome {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Chandusoft Admin</div>
    <div class="menu">
        <span class="welcome">Welcome <?= htmlspecialchars(ucfirst($role)) ?>!</span>
        <a href="dashboard.php">Dashboard</a>
        <a href="admin-leads.php">Leads</a>
        <a href="pages.php">Pages</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Leads</h2>

    <form method="get" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search name or email" />
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Created</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($leads)): ?>
                <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><?= htmlspecialchars($lead['name']) ?></td>
                        <td><?= htmlspecialchars($lead['email']) ?></td>
                        <td><?= htmlspecialchars($lead['message']) ?></td>
                        <td><?= htmlspecialchars($lead['created_at']) ?></td>
                        <td><?= htmlspecialchars($lead['ip'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No leads found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
