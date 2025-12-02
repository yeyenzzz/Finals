<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Redirect if already logged in
if (isset($_SESSION['email'])) {
  header("Location: dashboard.php");
  exit();
}
if (isset($_SESSION['id'])) {
  header("Location: admin.php");
  exit();
}

// Database
include 'db.php';
$connectDB = connectDB();

// LOGIN HANDLER
$login_email = $_SESSION['login_email'] ?? '';
unset($_SESSION['login_email']);
$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {

  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "*Please enter both email and password.";
    $_SESSION['login_email'] = $email;
    header("Location: index.php");
    exit();
  }

  $stmt = $connectDB->prepare("SELECT id, firstName, lastName, phone_number, address, date_of_birth, password 
                                 FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $firstName, $lastName, $phone_number, $address, $date_of_birth, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
      $_SESSION['email'] = $email;
      $_SESSION['firstName'] = $firstName;
      $_SESSION['lastName'] = $lastName;
      $_SESSION['phone_number'] = $phone_number;
      $_SESSION['address'] = $address;
      $_SESSION['date_of_birth'] = $date_of_birth;
      header("Location: dashboard.php");
      exit();
    } else {
      $_SESSION['login_error'] = "*Invalid password.";
      $_SESSION['login_email'] = $email;
    }
  } else {
    $_SESSION['login_error'] = "*No account found with that email.";
    $_SESSION['login_email'] = $email;
  }

  $stmt->close();
  header("Location: index.php");
  exit();
}

// FORGOT PASSWORD HANDLER
$forgot_error = '';
$forgot_success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['forgot'])) {

  $forgotEmail = trim($_POST['forgotEmail']);

  // Check if user exists
  $stmt = $connectDB->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $forgotEmail);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 0) {
    $forgot_error = "No account found with that email.";
  } else {
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Save token to password_resets table
    $stmt2 = $connectDB->prepare("INSERT INTO password_resets (email, token, expires_at)
                                      VALUES (?, ?, ?)
                                      ON DUPLICATE KEY UPDATE token=?, expires_at=?");
    $stmt2->bind_param("sssss", $forgotEmail, $token, $expiry, $token, $expiry);
    $stmt2->execute();

    $resetLink = "http://localhost/Finals/resetpassword.php?token=$token";

    // Send email
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = "smtp.gmail.com";
      $mail->SMTPAuth = true;
      $mail->Username = "etappay@gmail.com";
      $mail->Password = "nuzu piax cjsy gohx";
      $mail->SMTPSecure = "tls";
      $mail->Port = 587;

      $mail->setFrom("etappay@gmail.com", "EtapPay");
      $mail->addAddress($forgotEmail);

      $mail->isHTML(true);
      $mail->Subject = "Reset Your Password";
      $mail->Body = "
                <p>You requested a password reset.</p>
                <p>Click below to reset your password:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>This link expires in 1 hour.</p>
            ";

      $mail->send();
      $forgot_success = "Reset link sent to your email!";
    } catch (Exception $e) {
      $forgot_error = "Mailer Error: {$mail->ErrorInfo}";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>eTapPay</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="page">
    <div class="left">
      <div class="quote">
        <h1>Empowering your</h1>
        <h1>digital banking</h1>
        <h1>experience</h1>
        <p>Secure. Seamless. Smart</p>
      </div>
    </div>

    <div class="wrap">
      <div class="container" id="loginContainer">
        <div class="header">
          <div class="logo">
            <img src="images/logo.png" alt="" class="logo">
            <h3>eTapPay</h3>
          </div>
          <h2>Welcome Back</h2>
          <p>Login to manage your secure transactions</p>
        </div>

        <!-- LOGIN FORM -->
        <form method="POST">
          <div class="inputs">
            <div class="email">
              <input placeholder="Email" name="email" value="<?= htmlspecialchars($login_email) ?>" required />
            </div>
            <div class="password">
              <input type="password" placeholder="Password" name="password" required />
              <span class="error"><?= $login_error ?></span>
            </div>
            <div class="ForgotPass">
              <a href="#" onclick="openModal()">Forgot Password?</a>
            </div>
            <div class="Button">
              <button type="submit" name="login">Login</button>
            </div>
            <div class="paragraph">
              <p>Do not have an account? <a href="register.php">Register here</a></p>
            </div>
            <div class="admin_login">
              <a href="adminlogin.php">Admin Login</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- FORGOT PASSWORD MODAL -->
    <div id="forgotModal" class="modal">
      <div class="modal-content">
        <h2>Reset Password</h2>
        <p>Enter your email to reset your password</p>

        <?php if ($forgot_error): ?>
          <p class="error"><?= $forgot_error ?></p>
        <?php elseif ($forgot_success): ?>
          <p class="success"><?= $forgot_success ?></p>
        <?php endif; ?>

        <form method="POST">
          <input type="email" name="forgotEmail" placeholder="Email" required />
          <div class="Button">
            <button type="submit" name="forgot">Submit</button>
            <button type="button" class="close-btn" onclick="closeModal()">Close</button>
          </div>
        </form>
      </div>
    </div>

  </div>

  <script>
    function openModal() {
      document.getElementById("forgotModal").style.display = "flex";
    }
    function closeModal() {
      document.getElementById("forgotModal").style.display = "none";
    }
  </script>

</body>

</html>