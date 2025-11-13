<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_SESSION['email'])) {
  header("Location: dashboard.php");
  exit();
}


$email = "";
if (isset($_SESSION['login_email'])) {
  $email = $_SESSION['login_email'];
  unset($_SESSION['login_email']);
}
$error = "";
if (isset($_SESSION['login_error'])) {
  $error = $_SESSION['login_error'];
  unset($_SESSION['login_error']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "*Please enter both email and password.";
    $_SESSION['login_email'] = $email;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  } else {
    include 'db.php';
    $connectDB = connectDB();
    $stmt = $connectDB->prepare("SELECT id, firstName, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
      $stmt->bind_result($id, $firstName, $hashedPassword);
      $stmt->fetch();

      if (password_verify($password, $hashedPassword)) {
        $_SESSION['email'] = $email;
        $_SESSION['id'] = $id;
        $_SESSION['firstName'] = $firstName;
        header("Location: dashboard.php");
        exit();
      } else {
        $_SESSION['login_error'] = "*Invalid password.";
        $_SESSION['login_email'] = $email;
      }
    } else {
      $_SESSION['login_error'] = "*No account found with that email.";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
          <h2>Welcome Back</h2>
          <p>Login to manage your secure transactions</p>
        </div>
        <form method="POST" action="index.php">
          <div class="inputs">
            <div class="email">
              <input placeholder="Email" name="email" value="<?php echo htmlspecialchars($email); ?>" required />
            </div>
            <div class="password">
              <input type="password" placeholder="Password" name="password" required />
              <span class="error"><?= $error ?></span>
            </div>
            <div class="ForgotPass">
              <a href="#" onclick="openModal()">Forgot Password?</a>

            </div>
            <div class="Button">
              <button type="submit">Login</button>
            </div>
            <div class="paragraph">
              <p>
                Do not have an account?
                <a href="register.php">Register here</a>
              </p>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div id="forgotModal" class="modal">
      <div class="modal-content">
        <h2>Reset Password</h2>
        <p>Enter your email to reset your password</p>
        <form method="POST" action="forgotpassword.php">
          <input type="email" name="forgotEmail" placeholder="Email" required />
          <div class="Button">
            <button type="submit">Submit</button>
            <button class="close-btn" onclick="closeModal()">Close</button>
          </div>
        </form>

      </div>
    </div>
  </div>
  <script src="script.js"></script>
  <script>
    window.addEventListener('pageshow', function (event) {
      if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
        window.location.reload();
      }
    });
  </script>
</body>

</html>