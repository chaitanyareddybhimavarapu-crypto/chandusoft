<?php
session_start();

// Check admin login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['flash'] = "Please log in first.";
    header("Location: admin-login.php");
    exit;
}

// DB Connection
$host = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'chandusoft';

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search logic
$searchTerm = '';
$whereClause = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
    $safeTerm = $conn->real_escape_string($searchTerm);
    $whereClause = "WHERE name LIKE '%$safeTerm%' OR email LIKE '%$safeTerm%'";
}

$sql = "SELECT name, email, message, created_at, ip FROM leads $whereClause ORDER BY id DESC";
$result = $conn->query($sql);
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
        <span class="welcome">Welcome Editor!</span>
        <a href="dashboard.php">Dashboard</a>
        <a href="adminleads.php">Leads</a>
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
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['ip'] ?? '') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No leads found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
