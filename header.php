<?php
require 'db.php';

$stmt = $pdo->query("SELECT title, slug FROM pages WHERE LOWER(status) = 'published' ORDER BY updated_at DESC");
$publishedPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<header>
    <div class="logo">
        <a href="index.php">
            <img src="/images/logo.jpg" title="Chandusoft Technologies" width="400" height="70">
        </a>
    </div>
    <nav>
        <a href="index.php"><button><b>Home</b></button></a>
        <a href="about.php"><button><b>About</b></button></a>
        <a href="services.php"><button><b>Services</b></button></a>
        <a href="contact.php"><button><b>Contact</b></button></a>

        <!-- Published Pages from Admin -->
        <?php foreach ($publishedPages as $page): ?>
            <a href="page.php?slug=<?= urlencode($page['slug']) ?>">
                <button><b><?= htmlspecialchars($page['title']) ?></b></button>
            </a>
        <?php endforeach; ?>
    </nav>
</header>
