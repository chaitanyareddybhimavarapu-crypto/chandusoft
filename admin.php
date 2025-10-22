<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = trim($_POST['site_name']);
    $logo = $settings['site_logo'];

    if (!empty($_FILES['site_logo']['name'])) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['site_logo']['name']);
        $target = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target)) {
            $logo = $fileName;
        } else {
            $error = "Logo upload failed.";
        }
    }

    $stmt = $pdo->prepare("UPDATE site_settings SET site_name = ?, site_logo = ? WHERE id = 1");
    $stmt->execute([$siteName, $logo]);

    header("Location: admin.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Site Settings</title>
</head>
<body>
    <h2>Update Site Name & Logo</h2>

    <?php if (!empty($_GET['success'])): ?>
        <p style="color:green;">Updated successfully!</p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Site Name:</label>
        <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" required>

        <label>Site Logo:</label>
        <?php if (!empty($settings['site_logo']) && file_exists('uploads/' . $settings['site_logo'])): ?>
            <img src="uploads/<?= $settings['site_logo'] ?>" height="60"><br>
        <?php endif; ?>
        <input type="file" name="site_logo" accept="image/*">

        <br><br>
        <button type="submit">Save</button>
    </form>
</body>
</html>
