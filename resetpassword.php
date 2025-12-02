<?php
session_start();
include 'db.php';
$conn = connectDB();

// STOP if no token provided
if (!isset($_GET['token'])) {
    die("Invalid reset link.");
}

$token = $_GET['token'];

// Validate token
$stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired reset link.");
}

$row = $result->fetch_assoc();
$email = $row['email'];
$expires_at = strtotime($row['expires_at']);

if (time() > $expires_at) {
    die("This reset link has expired.");
}

$message = "";

// Password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm)) {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm) {
        $message = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        // Update password
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed, $email);
        $update->execute();

        // Remove token
        $del = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $del->bind_param("s", $token);
        $del->execute();

        // Success message
        $message = "Password updated successfully. Redirecting to login...";

        // Auto redirect
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000);
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eTapPay - Reset Password</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .message {
            margin-top: 10px;
            padding: 10px;
            background: #ffe3e3;
            color: #d10000;
            border-radius: 5px;
            text-align: center;
        }

        .message.success {
            background: #d4f8d4;
            color: #0e730e;
        }
    </style>
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
            <div class="container">

                <div class="header">
                    <div class="logo">
                        <img src="images/logo.png" alt="" class="logo">
                        <h3>eTapPay</h3>
                    </div>
                    <h2>Reset Your Password</h2>
                    <p>Please enter your new password below.</p>
                </div>

                <!-- Success / Error Message -->
                <?php if (!empty($message)): ?>
                    <p class="message <?= strpos($message, 'successfully') !== false ? 'success' : '' ?>">
                        <?= $message ?>
                    </p>
                <?php endif; ?>

                <!-- Reset Password Form -->
                <form method="POST">
                    <div class="password">
                        <input type="password" name="new_password" placeholder="New Password" required>
                    </div>

                    <div class="password">
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                    </div>

                    <div class="Button">
                        <button type="submit">Update Password</button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</body>

</html>