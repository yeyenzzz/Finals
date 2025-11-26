<?php
session_start();
include 'db.php';

$connectDB = connectDB();

// Ensure user is logged in and form submitted
if (!isset($_SESSION['email']) || !isset($_POST['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Collect POST data
$user_id = intval($_POST['user_id']);
$name = trim($_POST['name']);
$id_number = trim($_POST['id_number']);
$dob = trim($_POST['dob']);
$address = trim($_POST['address']);

// Handle file upload
$targetDir = "uploads/valid_ids/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$fileName = basename($_FILES["valid_id_image"]["name"]);
$fileTmp = $_FILES["valid_id_image"]["tmp_name"];
$fileSize = $_FILES["valid_id_image"]["size"];

// Validate file type and size
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
$fileType = mime_content_type($fileTmp);

if (!in_array($fileType, $allowedTypes)) {
    die("Invalid file type. Only JPG and PNG are allowed.");
}

if ($fileSize > 5 * 1024 * 1024) { // 5MB max
    die("File too large. Maximum size is 5MB.");
}

// Rename file uniquely
$newFileName = "ID_" . $user_id . "_" . time() . "." . pathinfo($fileName, PATHINFO_EXTENSION);
$targetFile = $targetDir . $newFileName;

// Move uploaded file
if (!move_uploaded_file($fileTmp, $targetFile)) {
    die("Failed to upload file.");
}

// Insert into usersvalidID table
$stmt = $connectDB->prepare("
    INSERT INTO usersvalidID (user_id, full_name, id_number, dob, address, id_image)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("isssss", $user_id, $name, $id_number, $dob, $address, $newFileName);

if ($stmt->execute()) {
    // Set user as pending verification
    $update = $connectDB->prepare("UPDATE users SET is_verified = 0 WHERE id = ?");
    $update->bind_param("i", $user_id);
    $update->execute();
    $update->close();

    // Redirect with success flag
    header("Location: dashboard.php?verify=success");
    exit();
} else {
    echo "Database error: " . $stmt->error;
}

$stmt->close();
$connectDB->close();
?>