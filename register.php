<?php
session_start();
require 'db.php'; // PDO connection
require 'vendor/autoload.php'; // PHPMailer
require 'app/logger.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$inputName = '';
$inputEmail = '';

// Function to send email to Mailpit
function sendMailpitNotification($subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = '127.0.0.1'; // Mailpit host
        $mail->Port = 1025;        // Mailpit SMTP port
        $mail->SMTPAuth = false;   // No auth needed for Mailpit

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
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('⛔ Security token invalid.');
    }

    $inputName = trim($_POST['name'] ?? '');
    $inputEmail = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $role = 'editor'; // Automatically assign 'editor' role to all new users

    // Validation
    if ($inputName === '') {
        $error = 'Name is required.';
        sendMailpitNotification('Registration Error', "Error: Name is empty.\nTime: ".date('Y-m-d H:i:s'));
    } elseif ($inputEmail === '' || !filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Valid email is required.';
        sendMailpitNotification('Registration Error', "Error: Invalid email ($inputEmail).\nTime: ".date('Y-m-d H:i:s'));
    } elseif ($password === '') {
        $error = 'Password is required.';
        sendMailpitNotification('Registration Error', "Error: Password empty for email: $inputEmail\nTime: ".date('Y-m-d H:i:s'));
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
        sendMailpitNotification('Registration Error', "Error: Password mismatch for email: $inputEmail\nTime: ".date('Y-m-d H:i:s'));
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$inputEmail]);

            if ($stmt->fetch()) {
                $error = 'Email already registered.';
                sendMailpitNotification('Registration Error', "Error: Email already exists ($inputEmail)\nTime: ".date('Y-m-d H:i:s'));
            } else {
                // Insert user
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$inputName, $inputEmail, $hash, $role]);

                $_SESSION['flash'] = '✅ Registration successful! Please login.';

                // Send success email
                sendMailpitNotification(
                    'New Registration',
                    "New user registered:\nName: $inputName\nEmail: $inputEmail\nRole: $role\nTime: ".date('Y-m-d H:i:s')
                );

                header('Location: login.php');
                exit;
            }
        } catch (Exception $e) {
            log_error("❌ DB error during registration for $inputEmail: " . $e->getMessage());
            $error = 'An unexpected error occurred. Please try again.';
            sendMailpitNotification(
                'Registration DB Error',
                "Database error during registration for $inputEmail\nError: ".$e->getMessage()."\nTime: ".date('Y-m-d H:i:s')
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
  <title>Register</title>
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

    .register-card {
      background: #fff;
      padding: 2rem;
      width: 100%;
      max-width: 400px;
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
      margin-bottom: 1rem;
    }

    label {
      display: block;
      margin-bottom: 0.4rem;
      color: #444;
      font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.6rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
    }

    .register-btn {
      width: 100%;
      background-color: #28a745;
      color: white;
      padding: 0.7rem;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }

    .register-btn:hover {
      background-color: #218838;
    }

    .error {
      color: red;
      margin-bottom: 1rem;
      text-align: center;
    }

    .login-link {
      margin-top: 1rem;
      text-align: center;
    }

    .login-link a {
      color: #007bff;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="register-card">
    <h2>Register</h2>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="register.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($inputName) ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($inputEmail) ?>" required autocomplete="email">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required autocomplete="new-password">
      </div>

      <div class="form-group">
        <label for="password_confirm">Confirm Password</label>
        <input type="password" name="password_confirm" id="password_confirm" required autocomplete="new-password">
      </div>

      <button type="submit" class="register-btn">Register</button>
    </form>

    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
  </div>

</body>
</html>
