<?php
session_start(); // ✅ Required for role-based navbar

// Check login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'] ?? 'user'; // default role

require_once __DIR__ . '/../app/config.php';

// Pagination variables
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Count total archived items
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM catalog_items WHERE status = 'archived'");
$stmtCount->execute();
$totalItems = $stmtCount->fetchColumn();

$totalPages = ceil($totalItems / $perPage);
$offset = ($page - 1) * $perPage;

// Fetch archived items
$stmt = $pdo->prepare("SELECT * FROM catalog_items WHERE status = 'archived' ORDER BY updated_at DESC LIMIT :offset, :perPage");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Archived Catalog Items</title>

<style>
/* ---------- NAVBAR STYLE (Same as catalog.php) ---------- */
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
    font-size: 16px;
}
.navbar a.active {
    color: #007bff;
    text-decoration: underline;
    font-weight: bold;
}
.navbar a:hover {
    text-decoration: underline;
}

/* ------- Your original CSS (unchanged) ------- */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
    color: #333;
}
h1 {
    text-align: center;
    color: #0056b3;
    margin: 30px 0;
}
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.actions {
    margin-bottom: 20px;
    text-align: right;
}
.actions a {
    margin-right: 10px;
    font-size: 16px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
    font-size: 16px;
}
th {
    background-color: #007bff;
    color: white;
}
td {
    background-color: #fafafa;
}
td a {
    color: #28a745;
    text-decoration: none;
    padding: 6px 12px;
    border: 1px solid #28a745;
    border-radius: 4px;
}
td a:hover {
    background-color: #28a745;
    color: white;
}
.pagination {
    text-align: center;
    margin-top: 20px;
}
.pagination a {
    margin: 0 5px;
    padding: 8px 16px;
    text-decoration: none;
    border: 1px solid #007bff;
    color: #007bff;
    border-radius: 4px;
}
.pagination a:hover {
    background-color: #007bff;
    color: white;
}
.pagination strong {
    padding: 8px 16px;
    border: 1px solid #007bff;
    background-color: #007bff;
    color: white;
}
@media (max-width: 768px) {
    .container { width: 95%; padding: 15px; }
    h1 { font-size: 24px; }
    table { font-size: 14px; }
}
</style>
</head>
<body>

<!-- 🔥 ROLE-BASED NAVBAR (Same as catalog.php) -->
<div class="navbar">
    <div><strong>Chandusoft Admin</strong></div>

    <div>
        <span>Welcome <?= htmlspecialchars(ucfirst($role)) ?>!</span>

        <a href="/dashboard.php"
           class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
           Dashboard
        </a>

        <?php if ($role === 'admin'): ?>
            <a href="/admin/catalog.php"
               class="<?= (strpos($_SERVER['REQUEST_URI'], 'catalog') !== false) ? 'active' : '' ?>">
               Admin Catalog
            </a>

            <a href="/public/catalog.php"
               class="<?= strpos($_SERVER['REQUEST_URI'], '/public/') !== false ? 'active' : '' ?>">
               Public Catalog
            </a>

            <a href="/admin/orders.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>">
               Orders
            </a>

        <?php elseif ($role === 'editor'): ?>
            <a href="/public/catalog.php"
               class="<?= strpos($_SERVER['REQUEST_URI'], '/public/') !== false ? 'active' : '' ?>">
               Public Catalog
            </a>
        <?php endif; ?>

        <a href="/admin-leads.php"
           class="<?= basename($_SERVER['PHP_SELF']) === 'admin-leads.php' ? 'active' : '' ?>">
           Leads
        </a>

        <a href="/pages.php"
           class="<?= basename($_SERVER['PHP_SELF']) === 'pages.php' ? 'active' : '' ?>">
           Pages
        </a>

        <a href="/logout.php"
           class="<?= basename($_SERVER['PHP_SELF']) === 'logout.php' ? 'active' : '' ?>">
           Logout
        </a>
    </div>
</div>
<!-- END NAVBAR -->

<div class="container">
    <h1>Archived Catalog Items</h1>

    <div class="actions">
        <a href="catalog.php">Back to Published Items</a> |
        <a href="catalog-new.php">Add New Item</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Slug</th>
                <th>Title</th>
                <th>Price</th>
                <th>Archived At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($items) === 0): ?>
                <tr><td colspan="6">No archived items.</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['slug']) ?></td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td><?= htmlspecialchars($item['updated_at']) ?></td>
                        <td>
                            <a href="catalog-restore.php?id=<?= $item['id'] ?>" onclick="return confirm('Restore this item?')">
                                Restore
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            Pages:
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p === $page): ?>
                    <strong><?= $p ?></strong>
                <?php else: ?>
                    <a href="?page=<?= $p ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
