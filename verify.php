<?php
session_start();
include "db.php";

if (!isset($_GET["token"])) {
    die("Invalid verification request.");
}

$token = $_GET["token"];

// Check session data
if (!isset($_SESSION["pending_registration"])) {
    die("No pending registration found. Verification expired or already completed.");
}

$data = $_SESSION["pending_registration"];

// Compare token
if ($data["token"] !== $token) {
    die("Invalid or expired verification token.");
}

$connectDB = connectDB();

// Insert into DB
$stmt = $connectDB->prepare("
    INSERT INTO users (firstName, lastName, phone_number, email, password, email_verified) 
    VALUES (?, ?, ?, ?, ?, 1)
");

$stmt->bind_param(
    "sssss",
    $data["firstName"],
    $data["lastName"],
    $data["phone_number"],
    $data["email"],
    $data["password"]
);

if ($stmt->execute()) {

    // Remove temporary session data
    unset($_SESSION["pending_registration"]);

    echo "
        <script>
            alert('Your account was successfully verified!');
            window.location.href = 'index.php';
        </script>
    ";
} else {
    echo "Database Error: " . $stmt->error;
}

$stmt->close();
?>