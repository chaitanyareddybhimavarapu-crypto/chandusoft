<?php

// app/upload.php
function uploadImage(array $file): string|false {
    // Allowed MIME types
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Max file size: 5MB
    $maxFileSize = 2 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Check if MIME type is allowed
    if (!in_array(mime_content_type($file['tmp_name']), $allowedMimes)) {
        return false;
    }

    // Check if file size exceeds max size
    if ($file['size'] > $maxFileSize) {
        return false;
    }

    // Get the file extension
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ext = strtolower($ext);

    // Create folder structure based on year/month
    $uploadDir = __DIR__ . '/../public/uploads/' . date('Y') . '/' . date('m') . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate random filename
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    // Move the file to the target path
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Return relative path from public folder (for saving in DB)
        return 'uploads/' . date('Y') . '/' . date('m') . '/' . $filename;
    }

    return false;
}
