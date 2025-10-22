<?php
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

// Fetch archived items with pagination
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
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
    </style>
</head>
<body>
    <h1>Archived Catalog Items</h1>

    <a href="catalog.php">Back to Published Items</a> | 
    <a href="catalog-new.php">Add New Item</a>

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
                            <a href="catalog-restore.php?id=<?= $item['id'] ?>" onclick="return confirm('Restore this item?')">Restore</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div>
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
</body>
</html>
