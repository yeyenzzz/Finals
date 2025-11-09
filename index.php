<?php
session_start();

// if (isset($_SESSION['email'])) {
//     header("Location: dashboard.php");
//     exit();
// }

if (isset(false['email'])) {
  header("Location: dashboard.php");
  exit();
}

$email = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
    $error = "*Please enter both email and password.";
  } else {
    include 'db.php';
    $connectDB = connectDB();
    $statement = $connectDB->prepare("SELECT id, password FROM users WHERE email = ?");
    $statement->bind_param("s", $email);
    $statement->execute();
    $statement->store_result();

    if ($statement->num_rows === 1) {
      $statement->bind_result($id, $hashedPassword);
      $statement->fetch();

      if (password_verify($password, $hashedPassword)) {
        $_SESSION['email'] = $email;
        $_SESSION['id'] = $id;
        header("Location: dashboard.php");
        exit();
      } else {
        $error = "*Invalid password.";
      }
    } else {
      $error = "*No account found with that email.";
    }
    $statement->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Online Banking</title>
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
          <h2>Welcome Back</h2>
          <p>Login to manage your secure transactions</p>
        </div>
        <form method="POST" action="index.php">
          <div class="inputs">
            <div class="email">
              <input placeholder="Email" name="email" value="<?= $email ?>" required />
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
</body>

</html>