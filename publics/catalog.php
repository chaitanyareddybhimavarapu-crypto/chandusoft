<?php
require_once __DIR__ . '/../app/config.php';

$stmt = $pdo->prepare("SELECT id, slug, title, price, image_path FROM catalog_items WHERE status = 'published' ORDER BY created_at DESC");
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Catalog</title>
    <style>
        .grid { display: flex; flex-wrap: wrap; gap: 20px; }
        .item { width: 200px; border: 1px solid #ccc; padding: 10px; border-radius: 6px; text-align: center; }
        .item img { max-width: 100%; max-height: 120px; object-fit: contain; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Catalog</h1>
    <div class="grid">
        <?php foreach ($items as $item): ?>
            <div class="item">
                <?php if ($item['image_path']): ?>
                    <a href="catalog-item.php?slug=<?= urlencode($item['slug']) ?>">
                        <img src="../public/uploads/<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                    </a>
                <?php endif; ?>
                <h2><a href="catalog-item.php?slug=<?= urlencode($item['slug']) ?>"><?= htmlspecialchars($item['title']) ?></a></h2>
                <p>$<?= number_format($item['price'], 2) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
