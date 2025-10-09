<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Filters
$statusFilter = $_GET['status'] ?? 'all';
$searchTerm = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Initialize counts array
$statusCounts = [
    'all' => 0,
    'Published' => 0,
    'Draft' => 0,
    'Archived' => 0
];

// Get counts per status (case-insensitive)
$countSql = "SELECT LOWER(status) AS status, COUNT(*) AS cnt FROM pages GROUP BY LOWER(status)";
$countStmt = $pdo->query($countSql);
while ($row = $countStmt->fetch(PDO::FETCH_ASSOC)) {
    $key = ucfirst(strtolower($row['status']));
    if (isset($statusCounts[$key])) {
        $statusCounts[$key] = $row['cnt'];
    }
}

// Get total count for 'all'
$totalCountStmt = $pdo->query("SELECT COUNT(*) FROM pages");
$statusCounts['all'] = $totalCountStmt->fetchColumn();

// Build WHERE clause
$where = "1=1";
$params = [];

if (strtolower($statusFilter) !== 'all') {
    $where .= " AND LOWER(status) = ?";
    $params[] = strtolower($statusFilter);
}

if ($searchTerm !== '') {
    $where .= " AND (title LIKE ? OR slug LIKE ?)";
    $searchValue = '%' . $searchTerm . '%';
    $params[] = $searchValue;
    $params[] = $searchValue;
}

// Count total pages (for pagination)
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE $where");
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch filtered and paginated pages
$sql = "SELECT * FROM pages WHERE $where ORDER BY updated_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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
        .top-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filters a {
            margin-right: 10px;
            padding: 6px 12px;
            text-decoration: none;
            color: #444;
            font-weight: 500;
            border-radius: 4px;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        .filters a:hover {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        .filters a.active {
            background-color: #3498db;
            color: white;
            border-color: #2980b9;
        }
        form.search-form {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        form.search-form input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form.search-form button {
            padding: 8px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
        }
        .create-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 15px;
        }
        .create-btn:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-edit { background-color: #007bff; color: white; }
        .btn-edit:hover { background-color: #0056b3; }
        .btn-archive { background-color: orange; color: white; }
        .btn-archive:hover { background-color: #e69500; }
        .btn-delete { background-color: red; color: white; }
        .btn-delete:hover { background-color: #c00000; }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 0 4px;
            text-decoration: none;
            background: #eee;
            color: #333;
            border-radius: 4px;
        }
        .pagination a.active {
            background: #3498db;
            color: white;
        }
        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            form.search-form {
                margin-top: 10px;
                width: 100%;
            }
            form.search-form input[type="text"] {
                flex: 1;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Chandusoft Admin</div>
    <div class="menu">
        <span>Welcome <?= htmlspecialchars(ucfirst($role)) ?>!</span>
        <a href="dashboard.php">Dashboard</a>
        <a href="admin-leads.php">Leads</a>
        <a href="pages.php">Pages</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Pages</h2>

    <div class="top-bar">
        <div class="filters">
            <?php
            $statuses = [
                'all' => 'All',
                'Published' => 'Published',
                'Draft' => 'Draft',
                'Archived' => 'Archived'
            ];
            foreach ($statuses as $key => $label):
                $active = (strtolower($statusFilter) === strtolower($key)) ? 'active' : '';
                $count = $statusCounts[$key] ?? 0;
                $url = "pages.php?status=" . urlencode($key) . ($searchTerm ? "&search=" . urlencode($searchTerm) : '');
                echo "<a class='$active' href='$url'>{$label} ({$count})</a>";
            endforeach;
            ?>
        </div>

        <form method="get" class="search-form">
            <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search by title or slug" />
            <button type="submit">Search</button>
        </form>

        <a href="create.php" class="create-btn">+ Create New Page</a>
    </div>

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
        <?php if ($pages): ?>
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
                            <a class="btn btn-delete" href="delete-page.php?id=<?= $page['id'] ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No pages found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <?php
            $baseUrl = "pages.php?status=" . urlencode($statusFilter);
            if ($searchTerm) {
                $baseUrl .= "&search=" . urlencode($searchTerm);
            }

            if ($page > 1) {
                $page = 1;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = max(1, intval($_GET['page']));
}
            }

            for ($i = 1; $i <= $totalPages; $i++) {
                $activeClass = ($i == $page) ? 'active' : '';
                echo '<a class="' . $activeClass . '" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a>';
            }

            if ($page < $totalPages) {
                echo '<a href="' . $baseUrl . '&page=' . ($page + 1) . '">Next &raquo;</a>';
            }
            ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>