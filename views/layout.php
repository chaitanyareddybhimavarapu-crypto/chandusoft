
<!-- views/layout.php -->
<div class="container">
    <h1><?= htmlspecialchars($page['title']) ?></h1>

    <div class="page-content">
        <!-- Display the raw HTML content -->
        <?= $page['content_html'] ?> <!-- Directly render the HTML content from the database -->
    </div>
</div>


