<?php
require 'config.php';

// Fetch published pages for navbar from the database
$stmt = $pdo->query("SELECT title, slug FROM pages WHERE LOWER(status) = 'published' ORDER BY updated_at DESC");
$publishedPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<header>
    <div class="logo">
        <a href="index.php">
            <?php if (!empty($settings['site_logo']) && file_exists('uploads/' . $settings['site_logo'])): ?>
                <img src="uploads/<?= htmlspecialchars($settings['site_logo']) ?>" alt="<?= htmlspecialchars($settings['site_name']) ?> Logo" height="70">
            <?php else: ?>
                <h1><?= htmlspecialchars($settings['site_name']) ?></h1>
            <?php endif; ?>
        </a>
    </div>

    <nav>
        <!-- Static Navigation Links -->
        <a href="index.php"><button><b>Home</b></button></a>
        <a href="about.php"><button><b>About</b></button></a>
        <a href="services.php"><button><b>Services</b></button></a>
        <a href="contact.php"><button><b>Contact</b></button></a>

        <!-- Dynamically Generated Published Pages from Admin -->
        <?php if (!empty($publishedPages)): ?>
            <?php foreach ($publishedPages as $page): ?>
                <a href="/<?= htmlspecialchars($page['slug']) ?>">
                    <button><b><?= htmlspecialchars($page['title']) ?></b></button>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </nav>
</header>
