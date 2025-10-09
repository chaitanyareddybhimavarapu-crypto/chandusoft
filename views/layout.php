<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header Include -->
    <?php include("header.php"); ?>

    <div class="container">
        <!-- Content Specific to Each Page -->
        <?php include($viewFile); ?>
    </div>

    <!-- Footer Include -->
    <?php include("footer.php"); ?>

</body>
</html>
