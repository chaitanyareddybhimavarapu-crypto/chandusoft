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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="/styles.css" />
    
    <style>
        /* Global styles */
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
            margin-top: 30px;
        }
 
        .search-form {
            text-align: center;
            margin-bottom: 20px;
        }
 
        .search-form input[type="text"] {
            padding: 8px;
            font-size: 16px;
            width: 300px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
 
        .search-form button {
            padding: 8px 16px;
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
 
        .search-form button:hover {
            background-color: #0056b3;
        }
 
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin: 0 auto;
            padding: 20px;
            max-width: 1200px;
        }
 
        .catalog-item {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }
 
        .catalog-item:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
 
        .catalog-item img {
            max-width: 100%;
            max-height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }
 
        .catalog-item h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }
 
        .catalog-item p {
            font-size: 16px;
            color: #555;
        }
 
        .catalog-item a {
            text-decoration: none;
            color: #007bff;
        }
 
        .catalog-item a:hover {
            text-decoration: underline;
        }
 
        /* Pagination styles */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
 
        .pagination a, .pagination strong {
            padding: 8px 16px;
            margin: 0 5px;
            border: 1px solid #007bff;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
        }
 
        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }
 
        .pagination strong {
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }
 
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .catalog-grid {
                grid-template-columns: 1fr 1fr;
            }
 
            .catalog-item img {
                max-height: 120px;
            }
 
            .pagination a, .pagination strong {
                padding: 6px 12px;
                font-size: 14px;
            }
 
            .search-form input[type="text"] {
                width: 250px;
            }
 
            .search-form button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div id="header"></div>
    <?php include __DIR__ . '/../header.php'; ?>
    <h1>Catalog - Published Items</h1>
 
    <!-- Search form -->
    <form method="get" action="catalog.php" class="search-form">
        <input type="text" name="search" placeholder="Search by title or slug" value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Search</button>
    </form>
 
    <!-- Catalog Grid -->
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
 
    <!-- Pagination -->
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
     <!-- Footer will be dynamically loaded here -->
  <div id="footer"></div>
  <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>