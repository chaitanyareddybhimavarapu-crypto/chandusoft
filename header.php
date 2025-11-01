<?php
require 'config.php';

// Detect current page filename (e.g. index.php, about.php)
$current_page = basename($_SERVER['PHP_SELF']);

// Detect current slug for dynamic pages (e.g. /privacy-policy)
$current_slug = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Fetch published pages for navbar from the database
$stmt = $pdo->query("SELECT title, slug FROM pages WHERE LOWER(status) = 'published' ORDER BY updated_at DESC");
$publishedPages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if we are currently viewing a dynamic (admin) page
$is_dynamic_page = false;
if (!empty($publishedPages)) {
    foreach ($publishedPages as $page) {
        if ($current_slug === $page['slug']) {
            $is_dynamic_page = true;
            break;
        }
    }
}
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
        <a href="index.php" class="<?= (!$is_dynamic_page && $current_page == 'index.php') ? 'active' : '' ?>"><button><b>Home</b></button></a>
        <a href="about.php" class="<?= (!$is_dynamic_page && $current_page == 'about.php') ? 'active' : '' ?>"><button><b>About</b></button></a>
        <a href="services.php" class="<?= (!$is_dynamic_page && $current_page == 'services.php') ? 'active' : '' ?>"><button><b>Services</b></button></a>
        <a href="contact.php" class="<?= (!$is_dynamic_page && $current_page == 'contact.php') ? 'active' : '' ?>"><button><b>Contact</b></button></a>
        <a href="public/catalog.php" class="<?= (!$is_dynamic_page && $current_page == 'public/catalog.php') ? 'active' : '' ?>"><button><b>Public Catalog</b></button></a>
        <a href="register.php" class="<?= (!$is_dynamic_page && $current_page == 'register.php') ? 'active' : '' ?>"><button><b>Login/Register</b></button></a>

        <!-- Dynamically Generated Published Pages -->
        <?php if (!empty($publishedPages)): ?>
            <?php foreach ($publishedPages as $page): ?>
                <a href="/<?= htmlspecialchars($page['slug']) ?>"
                   class="<?= ($current_slug === $page['slug']) ? 'active' : '' ?>">
                    <button><b><?= htmlspecialchars($page['title']) ?></b></button>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </nav>
</header>
