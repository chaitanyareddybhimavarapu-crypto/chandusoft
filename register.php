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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Chandusoft</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <link rel="stylesheet" href="styles.css">

  <style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background-color: #f4f6fa;
}

/* CENTER CONTAINER */
main {
    width: 100%;
    max-width: 450px;
    margin: 60px auto;
    background: #ffffff;
    padding: 32px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    text-align: left;  /* 👈 THIS MAKES EVERYTHING LEFT SIDE */
}

/* TITLE */
main h2 {
    text-align: center;
    font-size: 1.8rem;
    color: #007bff;
    font-weight: 600;
    margin-bottom: 22px;
}

/* LABELS (full left alignment) */
label {
    font-weight: 600;
    margin-top: 18px;
    display: block;
    color: #222;
    text-align: left; /* 👈 Forcing left alignment */
    font-size: 0.95rem;
}

/* INPUT FIELDS */
input {
    width: 100%;
    padding: 12px;
    margin-top: 6px;
    border: 1px solid #cfd4da;
    border-radius: 6px;
    font-size: 1rem;
    background-color: #fafafa;
    transition: 0.25s;
}

input:focus {
    border-color: #007bff;
    background: #fff;
    box-shadow: 0 0 6px rgba(0,123,255,0.25);
    outline: none;
}

/* REGISTER BUTTON */
.register-btn {
    width: 100%;
    padding: 12px;
    margin-top: 22px;
    border: none;
    background-color: #28a745;
    color: white;
    font-size: 1.1rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.25s;
}

.register-btn:hover {
    background-color: #218838;
    box-shadow: 0 4px 10px rgba(40,167,69,0.2);
}

/* ERROR MESSAGE */
.error {
    color: #ff3030;
    text-align: center;
    margin-top: 10px;
    margin-bottom: -10px;
    font-weight: 600;
}

/* LOGIN LINK SECTION */
.login-link {
    text-align: center;
    margin-top: 20px;
    font-size: 0.95rem;
}

.login-link a {
    color: #007bff;
    text-decoration: none;
    font-weight: 600;
}

.login-link a:hover {
    text-decoration: underline;
}

/* MOBILE VIEW */
@media (max-width: 480px) {
    main {
        margin: 25px;
        padding: 22px;
    }
}


</style>

</head>

<body>

  <!-- Header -->
  <?php include "header.php"; ?>

  <main>
    <h2>Create Your Account</h2>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="register.php">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <label for="name">Full Name</label>
      <input type="text" name="name" id="name" required value="<?= htmlspecialchars($inputName) ?>">

      <label for="email">Email Address</label>
      <input type="email" name="email" id="email" required value="<?= htmlspecialchars($inputEmail) ?>">

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>

      <label for="password_confirm">Confirm Password</label>
      <input type="password" name="password_confirm" id="password_confirm" required>

      <button type="submit" class="register-btn">Register</button>
    </form>

    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
  </main>

  <!-- Footer -->
  <?php include "footer.php"; ?>

</body>
</html>
