<?php
require_once __DIR__ . '/../app/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE catalog_items SET status = 'archived' WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: catalog.php');
exit;
