<?php
require 'db.php';

// Step 1: Get the slug from the URL
$slug = $_GET['slug'] ?? null;
if (!$slug) {
    http_response_code(400);
    die("❌ No page slug provided.");
}

// Step 2: Fetch the page from the database
$stmt = $pdo->prepare("
    SELECT id, title, slug, status, updated_at, content_html 
    FROM pages 
    WHERE slug = ? AND LOWER(status) = 'published' 
    LIMIT 1
");
$stmt->execute([$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

// Step 3: Handle not found
if (!$page) {
    http_response_code(404);
    die("❌ Page not found or unpublished.");
}

// Optional: Uncomment to debug content
// echo '<pre>'; print_r($page); echo '</pre>'; exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page['title']) ?> | Chandusoft</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2c3e50;
        }

        .page-content {
            margin-top: 20px;
            line-height: 1.6;
            color: #333;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1><?= htmlspecialchars($page['title']) ?></h1>

    <div class="page-content">
        <?= !empty($page['content_html']) 
            ? $page['content_html'] 
            : "<p><em>
Chandusoft is a well-established company with over 15 years of experience in delivering IT and BPO solutions. We have a team of more than 200 skilled professionals operating from multiple locations. One of our key strengths is 24/7 operations, which allows us to support clients across different time zones. We place a strong emphasis on data integrity and security, which has helped us earn long-term trust from our partners. Our core service areas include Software Development, Medical Process Services, and E-Commerce Solutions, all backed by a commitment to quality and process excellence.</em></p>" ?>
    </div>
</div>

</body>
</html>
