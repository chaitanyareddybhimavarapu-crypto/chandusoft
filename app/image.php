<?php
// app/image.php

function resizeImage($source_path, $destination_path, $max_width = 800, $max_height = 600) {
    list($width, $height, $type) = getimagesize($source_path);
    
    // Calculate the aspect ratio
    $ratio = $width / $height;
    
    if ($width > $max_width || $height > $max_height) {
        if ($ratio > 1) {
            // Landscape
            $new_width = $max_width;
            $new_height = $max_width / $ratio;
        } else {
            // Portrait
            $new_height = $max_height;
            $new_width = $max_height * $ratio;
        }

        $src_image = imagecreatefromstring(file_get_contents($source_path));
        $dst_image = imagecreatetruecolor($new_width, $new_height);
        
        // Preserve transparency for PNG or GIF images
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($dst_image, false);
            imagesavealpha($dst_image, true);
        }
        
        imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        // Save the resized image
        imagejpeg($dst_image, $destination_path, 90); // Save as JPEG with 90 quality
        imagedestroy($src_image);
        imagedestroy($dst_image);
    } else {
        // If image doesn't need resizing, just copy it
        copy($source_path, $destination_path);
    }
}

// Function to convert image to WebP format
function convertToWebP($source_path, $destination_path) {
    list($width, $height, $type) = getimagesize($source_path);

    if ($type == IMAGETYPE_JPEG) {
        $image = imagecreatefromjpeg($source_path);
    } elseif ($type == IMAGETYPE_PNG) {
        $image = imagecreatefrompng($source_path);
    } elseif ($type == IMAGETYPE_GIF) {
        $image = imagecreatefromgif($source_path);
    } else {
        return false; // Unsupported type
    }

    // Save the image as WebP
    imagewebp($image, $destination_path, 80); // Save as WebP with 80 quality
    imagedestroy($image);
    return true;
}

// Example usage:
$source_image = 'uploads/2025/10/example.jpg'; // Path to the uploaded image
$resized_image = 'uploads/2025/10/resized_example.jpg'; // Path to save resized image
$webp_image = 'uploads/2025/10/example.webp'; // Path to save WebP image

// Resize the image
resizeImage($source_image, $resized_image);

// Convert to WebP format
convertToWebP($source_image, $webp_image);

echo "Image processed: $resized_image and $webp_image";
?>
