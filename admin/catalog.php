<?php
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
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; vertical-align: middle; }
        form { margin-bottom: 1em; }
        img.thumb { max-width: 80px; max-height: 60px; border-radius: 4px; }
        a.action-link { margin-right: 6px; }
    </style>
</head>
<body>
    <h1>Catalog Items</h1>

    <form method="get" action="catalog.php">
        <input type="text" name="search" placeholder="Search by title or slug" value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Search</button>
    </form>

    <a href="catalog-new.php">Add New Item</a>

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
    <div>
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

</body>
</html>
