<?php 
require_once __DIR__ . '/../app/config.php';

// Pagination settings
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base query and params
$sql = "SELECT * FROM catalog_items WHERE status = 'published'";
$params = [];

if ($search !== '') {
    $sql .= " AND (title LIKE :search OR slug LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

// Count total items for pagination
$countSql = "SELECT COUNT(*) FROM catalog_items WHERE status = 'published'";
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
    <title>Catalog - Published Items</title>
    <style>
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill,minmax(200px,1fr));
            gap: 1rem;
        }
        .catalog-item {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }
        .catalog-item img {
            max-width: 100%;
            max-height: 150px;
            object-fit: contain;
            margin-bottom: 8px;
        }
        .pagination a, .pagination strong {
            margin: 0 5px;
            text-decoration: none;
        }
        .pagination strong {
            font-weight: bold;
        }
        form.search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Catalog - Published Items</h1>

    <form method="get" action="catalog.php" class="search-form">
        <input type="text" name="search" placeholder="Search by title or slug" value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Search</button>
    </form>

    <div class="catalog-grid">
        <?php if (count($items) === 0): ?>
            <p>No items found.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="catalog-item">
                    <?php if (!empty($item['image_path'])): ?>
                        <a href="catalog-item.php?slug=<?= urlencode($item['slug']) ?>">
                            <img src="../public/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy" width="200" height="150">
                        </a>
                    <?php else: ?>
                        <div style="height: 150px; line-height: 150px; background: #eee;">No image</div>
                    <?php endif; ?>
                    <h3><a href="catalog-item.php?slug=<?= urlencode($item['slug']) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
                    <p>Price: $<?= number_format($item['price'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

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
</body>
</html>
