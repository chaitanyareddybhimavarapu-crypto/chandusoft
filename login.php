<?php
session_start();
require 'db.php';
require 'app/logger.php'; // ✅ Logging
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$inputEmail = '';

// Function to send email to Mailpit
function sendMailpitNotification($subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = '127.0.0.1';  // Mailpit host
        $mail->Port = 1025;         // Mailpit SMTP port
        $mail->SMTPAuth = false;    // No auth needed for Mailpit

        $mail->setFrom('no-reply@example.com', 'Your App');
        $mail->addAddress('admin@example.com'); // Mailpit inbox

        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        log_error("Mailer Error: {$mail->ErrorInfo}");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!$postedToken || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        log_error("Invalid CSRF token for login attempt.");
        die('⛔ Security token invalid.');
    }

    $inputEmail = trim($_POST['email'] ?? '');
    $inputPassword = $_POST['password'] ?? '';

    if ($inputEmail === '') {
        $error = 'Email is required.';
        sendMailpitNotification('Login Error', "Login error: Email is empty.\nTime: ".date('Y-m-d H:i:s'));
    } elseif (!filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email.';
        sendMailpitNotification('Login Error', "Login error: Invalid email format ($inputEmail).\nTime: ".date('Y-m-d H:i:s'));
    } elseif ($inputPassword === '') {
        $error = 'Password is required.';
        sendMailpitNotification('Login Error', "Login error: Password empty for email: $inputEmail\nTime: ".date('Y-m-d H:i:s'));
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$inputEmail]);
            $user = $stmt->fetch();

            if ($user && password_verify($inputPassword, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                ];
                $_SESSION['flash'] = '✅ Login successful!';

                // Send success email
                sendMailpitNotification(
                    'Login Success',
                    "User '{$user['email']}' logged in as '{$user['role']}'\nTime: ".date('Y-m-d H:i:s')
                );

                header('Location: dashboard.php');
                exit;
            } else {
                log_error("⚠️ Failed login attempt for email: $inputEmail");
                $error = 'Invalid email or password.';

                // Send failed login email
                sendMailpitNotification(
                    'Failed Login Attempt',
                    "Failed login attempt for email: $inputEmail\nTime: ".date('Y-m-d H:i:s')
                );
            }
        } catch (Exception $e) {
            log_error("❌ DB error during login for $inputEmail: " . $e->getMessage());
            $error = 'An unexpected error occurred. Please try again.';

            sendMailpitNotification(
                'Login DB Error',
                "Database error for login attempt with email: $inputEmail\nError: ".$e->getMessage()."\nTime: ".date('Y-m-d H:i:s')
            );
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Chandusoft</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="/styles.css" />
  <link rel="stylesheet" href="styles.css" />

  <style>
    /* CLEAN PROFESSIONAL LOGIN CARD STYLING */

body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background-color: #f4f6fa;
}

/* LOGIN CARD */
main {
    width: 100%;
    max-width: 420px;
    margin: 60px auto;
    background: #fff;
    padding: 28px 32px;
    border-radius: 12px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
    text-align: left; /* 👈 THIS FIXES LEFT ALIGN */
}

main h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #007bff;
    font-size: 1.8rem;
}

/* LABEL LEFT SIDE ALWAYS */
label {
    font-weight: 600;
    margin-top: 14px;
    display: block;
    color: #333;
    text-align: left;  /* 👈 FORCES LEFT SIDE */
}

/* INPUT LEFT WITH EQUAL SPACING */
input {
    width: 100%;
    padding: 11px;
    margin-top: 6px;
    border: 1px solid #c8cdd3;
    border-radius: 6px;
    background: #fafafa;
    font-size: 1rem;
    transition: 0.25s;
}

input:focus {
    border-color: #007bff;
    background: #fff;
    box-shadow: 0 0 6px rgba(0,123,255,0.25);
    outline: none;
}

/* BUTTON */
.login-btn {
    width: 100%;
    padding: 12px;
    margin-top: 22px;
    background: #007bff;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 1.1rem;
    cursor: pointer;
    font-weight: 600;
    transition: 0.2s;
}

.login-btn:hover {
    background: #0056b3;
}

.error {
    text-align: center;
    color: #ff3d3d;
    margin: 10px 0;
    font-weight: 600;
}

.register-link {
    text-align: center;
    margin-top: 18px;
}

.register-link a {
    color: #28a745;
    text-decoration: none;
    font-weight: 600;
}

  </style>
</head>

<body>

  <!-- Header -->
  <?php include "header.php"; ?>

  <main>
    <h2>Login</h2>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <label for="email">Email Address</label>
      <input type="email" name="email" id="email" required value="<?= htmlspecialchars($inputEmail) ?>">

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>

      <button type="submit" class="login-btn">Login</button>
    </form>

    <div class="register-link">
      <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
  </main>

  <!-- Footer -->
  <?php include "footer.php"; ?>

</body>
</html>