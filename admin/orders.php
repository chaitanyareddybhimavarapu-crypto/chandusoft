<?php
session_start();

// ----------------------------------------------------------
// Get role from session (admin/editor). Default = Admin
// ----------------------------------------------------------
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'admin';

require_once __DIR__ . '/../app/config.php';

// ----------------------------------------------------------
// Search & Filter
// ----------------------------------------------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

// ----------------------------------------------------------
// Pagination
// ----------------------------------------------------------
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ----------------------------------------------------------
// Build SQL
// ----------------------------------------------------------
$sql = "FROM orders o WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (o.customer_email LIKE :search 
               OR o.customer_name LIKE :search 
               OR o.order_ref LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($statusFilter !== '') {
    $sql .= " AND o.status = :status";
    $params[':status'] = $statusFilter;
}

// ----------------------------------------------------------
// Count total
// ----------------------------------------------------------
$countStmt = $pdo->prepare("SELECT COUNT(*) " . $sql);
$countStmt->execute($params);
$totalOrders = $countStmt->fetchColumn();
$totalPages = ceil($totalOrders / $limit);

// ----------------------------------------------------------
// Fetch Orders
// ----------------------------------------------------------
$sqlOrders = "
    SELECT o.id, o.customer_name, o.customer_email,
           o.total_amount, o.status, o.created_at,
           o.payment_gateway, o.order_ref
    $sql
    ORDER BY o.created_at DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sqlOrders);

foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statuses = ['pending', 'paid', 'fulfilled', 'cancelled', 'failed'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 40px; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: #fff; }
        th, td { padding: 10px 14px; border: 1px solid #ddd; text-align: left; }
        th { background: #0555e9ff; color: white; }
        tr:hover { background-color: #f9f9f9; }
        .filter-bar { margin-bottom: 20px; display: flex; gap: 10px; }
        input[type="text"], select { padding: 7px; font-size: 14px; }
        button { padding: 7px 15px; cursor: pointer; }

        .badge { padding: 4px 8px; border-radius: 4px; color: #fff; font-size: 12px; }
        .badge.pending { background: #f0ad4e; }
        .badge.paid { background: #5cb85c; }
        .badge.fulfilled { background: #0275d8; }
        .badge.cancelled { background: #999; }
        .badge.failed { background: #f50808ff; }

        .view-btn {
            padding: 5px 10px;
            background-color: #0275d8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .view-btn:hover { background-color: #025aa5; }

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
    text-decoration: underline;
    font-weight: bold;
    color: #007bff;
}

        .navbar a:hover { text-decoration: underline; }

        .pagination a {
            display: inline-block;
            padding: 6px 10px;
            margin: 2px;
            background: #eee;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }
        .pagination a.active { background: #333; color: #fff; }
    </style>
</head>
<body>

<!-- Navbar -->
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
               class="<?= basename($_SERVER['PHP_SELF']) === 'catalog.php' ? 'active' : '' ?>">
               Admin Catalog
            </a>

            <a href="/public/catalog.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'catalog.php' && strpos($_SERVER['REQUEST_URI'], '/public/') !== false ? 'active' : '' ?>">
               Public Catalog
            </a>

            <a href="/admin/orders.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>">
               Orders
            </a>
        <?php elseif ($role === 'editor'): ?>
            <a href="/public/catalog.php"
               class="<?= basename($_SERVER['PHP_SELF']) === 'catalog.php' ? 'active' : '' ?>">
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


<h1>🧾 Orders</h1>

<form method="get" class="filter-bar">
    <input type="text" name="search" placeholder="Search by email or customer name"
           value="<?= htmlspecialchars($search) ?>">

    <select name="status">
        <option value="">All Statuses</option>
        <?php foreach ($statuses as $status): ?>
            <option value="<?= $status ?>" <?= ($status == $statusFilter) ? 'selected' : '' ?>>
                <?= ucfirst($status) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filter</button>
</form>

<table>
    <thead>
        <tr>
            <th>Customer Name</th>
            <th>Customer Email</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Payment Gateway</th>
            <th>Order Reference</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php if ($orders): ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['customer_email']) ?></td>
                    <td>$<?= number_format($order['total_amount'], 2) ?></td>

                    <td>
                        <span class="badge <?= $order['status'] ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td><?= htmlspecialchars($order['payment_gateway']) ?></td>
                    <td><?= htmlspecialchars($order['order_ref']) ?></td>

                    <td>
                        <a href="order-details.php?id=<?= $order['id'] ?>" class="view-btn">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8">No orders found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?php if ($totalPages > 1): ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>"
               class="<?= ($i == $page) ? 'active' : '' ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>
    <?php endif; ?>
</div>

</body>
</html>
