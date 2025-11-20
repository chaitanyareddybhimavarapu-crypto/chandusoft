<?php
// Start session to fetch session variables
session_start();

// Make sure the user is logged in and the role is set
if (!isset($_SESSION['user'])) {
    // If the user is not logged in, redirect them to the login page
    header('Location: login.php');
    exit;
}

// Fetch the role from session
$user = $_SESSION['user'];
$role = $user['role'] ?? 'user';  // Default to 'user' if role is not set

require_once __DIR__ . '/../app/config.php';

// Pagination settings
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base query and params
$sql = "SELECT * FROM catalog_items WHERE status != 'archived'";
$params = [];

if ($search !== '') {
    $sql .= " AND (title LIKE :search OR slug LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

// Count total items for pagination
$countSql = "SELECT COUNT(*) FROM catalog_items WHERE status != 'archived'";
if ($search !== '') {
    $countSql .= " AND (title LIKE :search OR slug LIKE :search)";
}

$stmtCount = $pdo->prepare($countSql);
if ($search !== '') {
    $stmtCount->bindValue(':search', '%' . $search . '%');
}
$stmtCount->execute();
$totalItems = $stmtCount->fetchColumn();

$totalPages = ceil($totalItems / $perPage);
$offset = ($page - 1) * $perPage;

$sql .= " ORDER BY created_at DESC LIMIT :offset, :perPage";

$stmt = $pdo->prepare($sql);
if ($search !== '') {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Catalog - List</title>
    <style>
        /* Navbar CSS */
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
        .navbar a.active {
    color: #007bff;  /* Blue color */
    text-decoration: underline;  /* Underline the active link */
    font-weight: bold;  /* Optional: Makes the active link bold */
}



        .navbar a:hover {
            text-decoration: underline;
        }

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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Form and search bar */
        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        form input[type="text"] {
            padding: 10px;
            width: 75%;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        form button {
            padding: 10px 20px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #00408d;
        }

        /* Actions Section */
        .actions {
            margin-bottom: 20px;
            text-align: right;
        }

        .actions a {
            margin-right: 10px;
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            color: #495057;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        td img.thumb {
            max-width: 80px;
            max-height: 60px;
            border-radius: 4px;
            object-fit: cover;
        }

        /* Pagination */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #0056b3;
            color: white;
        }

        .pagination strong {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                flex-direction: column;
                align-items: stretch;
            }

            form input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }

            .container {
                padding: 10px;
            }

            table th, table td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
    <div><strong>Chandusoft Admin</strong></div>
    <div>
        <span>Welcome <?= htmlspecialchars(ucfirst($role)) ?>!</span>
        <a href="/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>

        <?php if ($role === 'admin'): ?>
            <a href="/admin/catalog.php" class="<?= basename($_SERVER['PHP_SELF']) === 'catalog.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'active' : '' ?>">Admin Catalog</a>
            <a href="/public/catalog.php" class="<?= basename($_SERVER['PHP_SELF']) === 'catalog.php' && strpos($_SERVER['REQUEST_URI'], '/public/') !== false ? 'active' : '' ?>">Public Catalog</a>
            <a href="/admin/orders.php" class="<?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>">Orders</a>
        <?php elseif ($role === 'editor'): ?>
            <a href="/public/catalog.php" class="<?= basename($_SERVER['PHP_SELF']) === 'catalog.php' && strpos($_SERVER['REQUEST_URI'], '/public/') !== false ? 'active' : '' ?>">Public Catalog</a>
        <?php endif; ?>

        <a href="/admin-leads.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin-leads.php' ? 'active' : '' ?>">Leads</a>
        <a href="/pages.php" class="<?= basename($_SERVER['PHP_SELF']) === 'pages.php' ? 'active' : '' ?>">Pages</a>
        <a href="/logout.php" class="<?= basename($_SERVER['PHP_SELF']) === 'logout.php' ? 'active' : '' ?>">Logout</a>
    </div>
</div>



    <div class="container">
        <h1>Catalog Items</h1>

        <div class="actions">
            <!-- Archived Items Button -->
            <a href="catalog-archived.php" class="action-link">View Archived Items</a> |
            <a href="catalog-new.php" class="action-link">Add New Item</a>
        </div>

        <form method="get" action="catalog.php">
            <input type="text" name="search" placeholder="Search by title or slug" value="<?= htmlspecialchars($search) ?>" />
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Slug</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($items) === 0): ?>
                    <tr><td colspan="8">No items found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id']) ?></td>
                            <td>
                                <?php if (!empty($item['image_path'])): ?>
                                    <img class="thumb" src="../public/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                <?php else: ?>
                                    No image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['slug']) ?></td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td><?= htmlspecialchars($item['status']) ?></td>
                            <td><?= htmlspecialchars($item['created_at']) ?></td>
                            <td>
                                <a class="action-link" href="catalog-edit.php?id=<?= $item['id'] ?>">Edit</a> |
                                <a class="action-link" href="catalog-delete.php?id=<?= $item['id'] ?>" onclick="return confirm('Archive this item?')">Archive</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                Pages:
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p === $page): ?>
                        <strong><?= $p ?></strong>
                    <?php else: ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>