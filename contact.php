<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = ""; // default Laragon has no password
$dbname = "chandusoft";

// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to get user IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Handle AJAX POST submission (from fetch)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'], $_POST['email'], $_POST['message'])) {
    // Sanitize input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $ip = getUserIP();

    // Basic validation
    if (
        !empty($name) &&
        preg_match("/^[A-Za-z\s]+$/", $name) &&
        !empty($email) &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        !empty($message)
    ) {
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert lead into database
        $stmt = $conn->prepare("INSERT INTO leads (name, email, message, ip) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $message, $ip);

            if ($stmt->execute()) {

                // Prepare the email body once
                $emailBody = "
                    <h3>New Contact Form Submission</h3>
                    <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
                    <p><strong>IP Address:</strong> {$ip}</p>
                    <p><strong>Received at:</strong> " . date('Y-m-d H:i:s') . "</p>
                ";

                // ============ MAILPIT SEND ============
                $mail1 = new PHPMailer(true);
                try {
                    $mail1->isSMTP();
                    $mail1->Host = '127.0.0.1';
                    $mail1->Port = 1025;
                    $mail1->SMTPAuth = false;
                    $mail1->SMTPSecure = false;

                    $mail1->setFrom('no-reply@chandusoft.test', 'Chandusoft Contact Form');
                    $mail1->addAddress('admin@chandusoft.test', 'Admin');
                    $mail1->isHTML(true);
                    $mail1->Subject = '📩 New Contact Form Submission (Local)';
                    $mail1->Body = $emailBody;
                    $mail1->AltBody = strip_tags($emailBody);
                    $mail1->send();
                } catch (Exception $e) {
                    file_put_contents(__DIR__ . '/storage/logs/mailpit-error.log', "[" . date("Y-m-d H:i:s") . "] Mailpit error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }

                // ============ GMAIL SEND ============
                $mail2 = new PHPMailer(true);
                try {
                    $mail2->isSMTP();
                    $mail2->Host = 'smtp.gmail.com';
                    $mail2->SMTPAuth = true;
                    $mail2->Username = 'cstltest4@gmail.com'; // Your Gmail
                    $mail2->Password = 'vwrs cubq qpqg wfcg';  // App password
                    $mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail2->Port = 587;

                    $mail2->setFrom('cstltest4@gmail.com', 'Chandusoft Contact Form');
                    $mail2->addAddress('chaitanyareddy.bhimavarapu@chandusoft.com', 'Admin');
                    $mail2->isHTML(true);
                    $mail2->Subject = '📩 New Contact Form Submission (Gmail)';
                    $mail2->Body = $emailBody;
                    $mail2->AltBody = strip_tags($emailBody);
                    $mail2->send();
                } catch (Exception $e) {
                    file_put_contents(__DIR__ . '/storage/logs/gmail-error.log', "[" . date("Y-m-d H:i:s") . "] Gmail error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }

                echo "success";
            } else {
                echo "error";
            }

            $stmt->close();
        } else {
            echo "error";
        }

        $conn->close();
    } else {
        echo "error";
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Chandusoft - Contact</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>
<body>
  <!-- Header -->
  <div id="header"></div>
  <?php include("header.php"); ?>

  <main>
    <h2>Contact Us</h2>
    <form id="contactForm" class="contact-form" action="contact.php" method="post" novalidate>
      <!-- Name -->
      <label for="name">Your Name</label>
      <input type="text" id="name" name="name" placeholder="Enter your name" required />
      <span class="error-message" id="nameError"></span>

      <!-- Email -->
      <label for="email">Your Email</label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required />
      <span class="error-message" id="emailError"></span>

      <!-- Message -->
      <label for="message">Your Message</label>
      <textarea id="message" name="message" rows="5" placeholder="Type your message here..." required></textarea>
      <span class="error-message" id="messageError"></span>

      <!-- Submit Button -->
      <button type="submit" id="submitBtn" disabled>Send Message</button>
    </form>
  </main>

  <!-- Footer -->
  <div id="footer"></div>
  <?php include("footer.php"); ?>

  <!-- Validation & Submit Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('contactForm');
      const nameInput = document.getElementById('name');
      const emailInput = document.getElementById('email');
      const messageInput = document.getElementById('message');
      const submitBtn = document.getElementById('submitBtn');

      const nameError = document.getElementById('nameError');
      const emailError = document.getElementById('emailError');
      const messageError = document.getElementById('messageError');

      const successMessage = document.createElement("div");
      successMessage.id = "successMessage";
      successMessage.style.color = "green";
      successMessage.style.fontWeight = "bold";
      successMessage.style.marginTop = "15px";
      successMessage.style.display = "none";
      successMessage.textContent = "✅ Successfully sent!";
      form.insertAdjacentElement("afterend", successMessage);

      const errorMessage = document.createElement("div");
      errorMessage.id = "errorMessage";
      errorMessage.style.color = "red";
      errorMessage.style.fontWeight = "bold";
      errorMessage.style.marginTop = "15px";
      errorMessage.style.display = "none";
      errorMessage.textContent = "❌ Something went wrong. Please check your input.";
      form.insertAdjacentElement("afterend", errorMessage);

      function validateName() {
        const name = nameInput.value.trim();
        if (name === "") {
          nameError.textContent = "Name is required.";
          return false;
        } else if (!/^[A-Za-z\s]+$/.test(name)) {
          nameError.textContent = "Only letters and spaces allowed.";
          return false;
        } else {
          nameError.textContent = "";
          return true;
        }
      }

      function validateEmail() {
        const email = emailInput.value.trim();
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,6}$/i;
        if (email === "") {
          emailError.textContent = "Email is required.";
          return false;
        } else if (!emailPattern.test(email)) {
          emailError.textContent = "Invalid email format.";
          return false;
        } else {
          emailError.textContent = "";
          return true;
        }
      }

      function validateMessage() {
        const message = messageInput.value.trim();
        if (message === "") {
          messageError.textContent = "Message cannot be empty.";
          return false;
        } else {
          messageError.textContent = "";
          return true;
        }
      }

      function validateForm() {
        const isNameValid = validateName();
        const isEmailValid = validateEmail();
        const isMessageValid = validateMessage();
        submitBtn.disabled = !(isNameValid && isEmailValid && isMessageValid);
      }

      // Real-time validation
      nameInput.addEventListener('input', () => { validateName(); validateForm(); });
      emailInput.addEventListener('input', () => { validateEmail(); validateForm(); });
      messageInput.addEventListener('input', () => { validateMessage(); validateForm(); });

      // Handle form submission
      form.addEventListener('submit', (e) => {
        e.preventDefault();

        if (!submitBtn.disabled) {
          const formData = new FormData(form);

          fetch("contact.php", {
            method: "POST",
            body: formData
          })
          .then(response => response.text())
          .then(result => {
            if (result.trim() === "success") {
              errorMessage.style.display = "none";
              successMessage.style.display = "block";
              form.reset();
              submitBtn.disabled = true;
              setTimeout(() => successMessage.style.display = "none", 10000);
            } else {
              successMessage.style.display = "none";
              errorMessage.style.display = "block";
              setTimeout(() => errorMessage.style.display = "none", 10000);
            }
          })
          .catch(() => {
            successMessage.style.display = "none";
            errorMessage.style.display = "block";
          });
        }
      });
    });
  </script>
</body>
</html>
