<?php
include 'db.php';
$connectDB = connectDB();

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    $stmt = $connectDB->prepare("SELECT id FROM users WHERE email = ? AND verification_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        $stmt = $connectDB->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        echo "Email verified. You may now <a href='index.php'>login</a>.";
    } else {
        echo "Invalid verification link.";
    }
}
?>
