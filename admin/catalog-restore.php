<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php'; // ✅ For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Function to send Mailpit notification
function sendMailpitNotification($subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = '127.0.0.1'; // Mailpit SMTP host
        $mail->Port = 1025;        // Mailpit default port
        $mail->SMTPAuth = false;

        $mail->setFrom('no-reply@example.com', 'Catalog App');
        $mail->addAddress('admin@example.com');

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Mailpit Error: {$mail->ErrorInfo}");
    }
}

// ✅ Restore logic
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Fetch item before restoring (for email details)
    $stmt = $pdo->prepare("SELECT title, slug, status FROM catalog_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $stmt = $pdo->prepare("UPDATE catalog_items SET status = 'published', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);

        // ✅ Send Mailpit email notification
        $subject = "Catalog Item Restored - {$item['title']}";
        $body = "A catalog item has been restored successfully:\n\n" .
                "🆔 ID: {$id}\n" .
                "📘 Title: {$item['title']}\n" .
                "🔗 Slug: {$item['slug']}\n" .
                "✅ New Status: Published\n" .
                "⏰ Restored At: " . date('Y-m-d H:i:s');

        sendMailpitNotification($subject, $body);
    }
}

header('Location: catalog-archived.php');
exit;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Restore Item - Admin</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #0056b3;
            margin-bottom: 20px;
        }

        .message {
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message a {
            color: #007bff;
            text-decoration: none;
        }

        .message a:hover {
            text-decoration: underline;
        }

        .button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .back-link {
            margin-top: 20px;
            display: block;
            color: #0056b3;
            font-size: 16px;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Item Restored</h1>
        <div class="message">
            <p>The item has been successfully restored and its status is now set to <strong>Published</strong>.</p>
            <p><a href="catalog-archived.php" class="button">Go back to Archived Items</a></p>
        </div>
        <a href="catalog.php" class="back-link">Return to Catalog</a>
    </div>

</body>
</html>
