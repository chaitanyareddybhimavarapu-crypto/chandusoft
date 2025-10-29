<?php
require 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid page ID.');
}

$pageId = $_GET['id'];

// Archive the page (assuming 'status' is 'Archived')
$stmt = $pdo->prepare("UPDATE pages SET status = 'Archived' WHERE id = ?");
$stmt->execute([$pageId]);

// Redirect back to the pages list or some other page after archiving
header('Location: pages.php');
exit;
?>
