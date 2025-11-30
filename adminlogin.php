<?php
session_start();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirect if already logged in
if (isset($_SESSION['id'])) {
  header("Location: admin.php");
  exit();
}
if (isset($_SESSION['email'])) {
  header("Location: dashboard.php");
  exit();
}

// Initialize variables
$email = $_SESSION['login_email'] ?? "";
unset($_SESSION['login_email']);

$error = $_SESSION['login_error'] ?? "";
unset($_SESSION['login_error']);

// Handle POST login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "*Please enter both email and password.";
    $_SESSION['login_email'] = $email;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }

  include 'db.php';
  $connectDB = connectDB();

  // Prepare statement to prevent SQL injection
  $stmt = $connectDB->prepare("SELECT id, first_name, last_name, password FROM admin WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $first_name, $last_name, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {

      // Store user info in session
      $_SESSION['id'] = $id;
      $_SESSION['first_name'] = $first_name;
      $_SESSION['last_name'] = $last_name;

      header("Location: admin.php");
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
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>eTapPay Admin Login</title>
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
        <div class="img">
          <img class="card" src="images/card.png" alt="Card">
          <img class="atm" src="images/atm.png" alt="ATM">
        </div>
      </div>
    </div>

    <div class="wrap">
      <div class="container" id="loginContainer">
        <div class="header">
          <div class="logo">
            <img src="images/logo.png" alt="Logo" class="logo">
            <h3>eTapPay</h3>
          </div>
          <h2>Welcome Back Admin!</h2>
          <p>Login to manage users</p>
        </div>

        <form method="POST" action="adminlogin.php">
          <div class="inputs">
            <div class="email">
              <input type="email" placeholder="Email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="password">
              <input type="password" placeholder="Password" name="password" required>
            </div>

            <?php if (!empty($error)): ?>
              <div class="error-message">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <div class="Button">
              <button type="submit">Login</button>
            </div>

            <div class="paragraph">
              <p><a href="index.php">Login as user</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Reload page on back/forward navigation to prevent cached login
    window.addEventListener('pageshow', function (event) {
      if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
        window.location.reload();
      }
    });
  </script>
</body>

</html>