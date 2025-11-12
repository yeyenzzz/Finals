<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Redirect if already logged in
if (isset($_SESSION['email'])) {
  header("Location: dashboard.php");
  exit();
}

// Initialize variables
$firstName = "";
$lastName = "";
$email = "";
$password = "";
$confirmPassword = "";

$nameError = "";
$emailError = "";
$passwordError = "";
$confirmPasswordError = "";

$error = false;

// Include database connection
include 'db.php';
$connectDB = connectDB();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $firstName = trim($_POST['firstName']);
  $lastName = trim($_POST['lastName']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];

  // Validate names
  if (empty($firstName) || empty($lastName)) {
    $error = true;
    $nameError = "*First and last name are required.";
  }

  // Validate email
  if (empty($email)) {
    $error = true;
    $emailError = "*Email is required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = true;
    $emailError = "*Invalid email format.";
  } else {
    // Check if email already exists
    $stmt = $connectDB->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $error = true;
      $emailError = "*Email is already registered.";
    }
    $stmt->close();
  }

  // Validate password
  if (empty($password)) {
    $error = true;
    $passwordError = "*Password is required.";
  } elseif (strlen($password) < 6) {
    $error = true;
    $passwordError = "*Password must be at least 6 characters.";
  }

  // Validate confirm password
  if ($confirmPassword !== $password) {
    $error = true;
    $confirmPasswordError = "*Passwords do not match.";
  }

  // If no errors, insert user
  if (!$error) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $verificationToken = bin2hex(random_bytes(16));

    $stmt = $connectDB->prepare("INSERT INTO users (firstName, lastName, email, password, verification_token) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $verificationToken);

    if ($stmt->execute()) {

      $verificationLink = "http://localhost/Finals-main/verify.php?email=$email&token=$verificationToken";

      $mail = new PHPMailer(true);


      $mail->isSMTP();
      $mail->Host = "smtp.gmail.com";
      $mail->SMTPAuth = true;
      $mail->Username = "etappay@gmail.com";
      $mail->Password = "nuzu piax cjsy gohx";
      $mail->SMTPSecure = "tls"; // TLS/SSL
      $mail->Port = 587;

      // Sender + recipient
      $mail->setFrom('etappay@gmail.com', 'eTapPay');
      $mail->addAddress($email, $firstName);

      // Email contents
      $mail->isHTML(true);
      $mail->Subject = "Verify Your EtapPay Account";
      $mail->Body = "
              <p>Hi <strong>$firstName</strong>,</p>
              <p>Please verify your account by clicking the link below:</p>
              <p><a href='$verificationLink'>$verificationLink</a></p>
              <br>
              <p>Thank you!</p>
          ";

      $mail->AltBody = "Hi $firstName, Please confirm: $verificationLink";

      $mail->send();

      echo "<script>
              alert('Account created! Please check your email to verify.');
              window.location.href = 'index.php';
          </script>";

      exit();

    } else {
      echo "Error: " . $stmt->error;
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>eTapPay</title>
  <link rel="stylesheet" href="style.css" />

</head>

<body>
  <div class="page">
    <div class="left">
      <div class="quote">
        <h1>Empowering your</h1>
        <h1>digital banking</h1>
        <h1>experience</h1>
        <p>Secure. Seamless. Smart</p>
        <div class="img">
          <img class="card" src="images/card.png" alt="" />
          <img class="atm" src="images/atm.png" alt="" />
        </div>
      </div>
    </div>
    <div class="wrap">
      <div class="container">
        <div class="header">
          <div class="logo">
            <img src="images/logo.png" alt="" class="logo">
            <h3>eTapPay</h3>
          </div>
          <h2>Create Your Account</h2>
          <p>Start your journey with secure and fast transactions</p>
        </div>
        <form method="POST" action="register.php">
          <div class="inputs">
            <div class="name">
              <input placeholder="First Name" name="firstName" value="<?= $firstName ?>" required />
              <input type="text" placeholder="Last Name" name="lastName" value="<?= $lastName ?>" required />
            </div>
            <div class="email">
              <input type="email" placeholder="Email" name="email" value="<?= $email ?>" required />
            </div>
            <div class="password">
              <input type="password" placeholder="Password" name="password" required />
              <input type="password" placeholder="Confirm Password" name="confirmPassword" required />
              <span class="error"><?= $nameError ?></span>
              <span class="error"><?= $emailError ?></span>
              <span class="error"><?= $passwordError ?></span>
              <span class="error"><?= $confirmPasswordError ?></span>
            </div>
            <div class="TermsConditions">
              <input type="checkbox" class="checkbox" required>
              <label>I agree to the <a href="#" onclick="openModal2()">Terms & Conditions</a></label>
            </div>
            <div class="Button">
              <button type="submit">Register</button>
            </div>
            <div class="paragraph">
              <p>Already have an account? <a href="index.php">Login here</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div id="TermsModal" class="modal">
      <div class="modal-content">
        <h2>Terms & Conditions</h2>
        <div class="scrollable">
          <p>
            Last Updated: November 4, 2025<br><br>
            Welcome to EtapPay. By using this website, you agree to follow and be bound by the following Terms and
            Conditions. Please read them carefully before using our service.<br><br>
            1. Acceptance of Terms<br><br>
            By registering for or using the EtapPay website, you accept these Terms and Conditions and agree to comply
            with them. If you do not agree, please do not use the website.<br><br>
            2. Eligibility<br><br>
            To use our online banking service, you must:<br>
            -Be at least 18 years old or have parental consent.<br>
            -Have an active account with EtapPay.<br>
            -Provide true and complete information during registration.<br><br>
            3. User Responsibilities<br><br>
            You agree to:<br>
            -Keep your username, password, and other login details private and secure.<br>
            -Inform us immediately if you suspect unauthorized access to your account.<br>
            -Use the online banking system for lawful and legitimate purposes only.<br>
            -You are responsible for all activities that occur under your account.<br><br>
            4. Access and Availability<br><br>
            We aim to keep our online banking service available 24/7. However, EtapPay does not guarantee continuous or
            error-free access. The website may be temporarily unavailable due to maintenance, updates, or technical
            issues.<br><br>
            5. Transactions<br><br>
            All transactions made through this online banking system are final once confirmed. Please review all details
            carefully before completing any transaction. EtapPay is not responsible for losses caused by incorrect
            information entered by users.<br><br>
            6. Fees and Charges<br><br>
            Some services or transactions may include fees. Users will be informed of any applicable charges before
            confirming their transaction.<br><br>
            7. Security<br><br>
            We use security measures to protect user information. However, you understand that no online system is
            completely secure. You agree to use the service at your own risk and to protect your login
            credentials.<br><br>
            8. Prohibited Activities<br><br>
            You must not:<br>
            -Attempt to gain unauthorized access to the system.<br>
            -Use the platform for illegal or fraudulent activities.<br>
            -Interfere with the security or operation of the website.<br><br>
            9. Limitation of Liability<br><br>
            EtapPay will not be responsible for:<br>
            -Losses resulting from unauthorized access caused by user negligence.<br>
            -Service interruptions, delays, or errors.<br>
            -Any indirect or accidental damages.<br><br>
            10. Privacy<br><br>
            Your personal information will be handled according to our Privacy Policy. By using this website, you agree
            to the collection and use of your data as described in that policy.<br><br>
            11. Changes to Terms<br><br>
            EtapPay reserves the right to update these Terms and Conditions at any time. Changes will take effect once
            posted on this website. Continued use of the service means you accept the updated terms.<br><br>
            12. Termination<br><br>
            We may suspend or terminate your account if you violate these Terms or if suspicious or illegal activity is
            detected.<br><br>
            13. Governing Law<br><br>
            These Terms are governed by the laws of the Republic of the Philippines. Any disputes will be handled by the
            appropriate courts within the Philippines.<br><br>
            14. Contact Information<br><br>
            If you have any questions or concerns, please contact us at:<br>
            EtapPay<br>
            Email: support@EtapPay.com<br>
            Phone: +63 912 345 6789<br>
            Address: Manila, Philippines<br>
          </p>
        </div>
        <button class="close-btn" onclick="closeModal2()">Close</button>
      </div>
    </div>
  </div>
  </div>
  <script src="script.js"></script>
  <script>
    // Prevent showing page after logout using JS (bfcache)
    window.addEventListener('pageshow', function (event) {
      if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
        // Reload page to force session check
        window.location.reload();
      }
    });
  </script>
</body>

</html>