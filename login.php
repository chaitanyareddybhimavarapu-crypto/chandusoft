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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-card {
      background: #fff;
      padding: 2rem;
      width: 100%;
      max-width: 360px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      color: #333;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    label {
      display: block;
      margin-bottom: 0.4rem;
      color: #444;
      font-weight: 500;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.6rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
    }

    .login-btn {
      width: 100%;
      background-color: #007bff;
      color: white;
      padding: 0.7rem;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }

    .login-btn:hover {
      background-color: #0056b3;
    }

    .error {
      color: red;
      margin-bottom: 1rem;
      text-align: center;
    }
    .register-link {
      margin-top: 1rem;
      text-align: center;
    }

    .register-link a {
      color: #28a745;
      text-decoration: none;
      font-weight: 500;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

  </style>
</head>
<body>

  <div class="login-card">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($inputEmail) ?>" required autocomplete="email">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required autocomplete="current-password">
      </div>

      <button type="submit" class="login-btn">Login</button>
    </form>

    <div class="register-link">
      <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>

  </div>

</body>
</html>
