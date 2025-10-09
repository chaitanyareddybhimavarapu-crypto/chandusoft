<?php
session_start();
require 'includes/db.php'; // Include database connection

// Fetch the slug from the URL (e.g., ?page=about-us)
$pageSlug = $_GET['page'] ?? '';

// Default page is home if no page is specified
if ($pageSlug === '') {
    $pageSlug = 'home';
}

// Fetch the page from the database
$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'Published'");
$stmt->execute([$pageSlug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

// If page exists, load the page, else show 404
if ($page) {
    // Sanitize title and slug
    $pageTitle = htmlspecialchars($page['title']);
    $pageContent = $page['content_html'];  // Content is trusted HTML
    include("views/page.php"); // Include page view
} else {
    include("views/404.php"); // If page not found, show 404
}
