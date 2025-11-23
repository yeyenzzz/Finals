<?php
session_start();
header('Content-Type: application/json');
include 'db.php'; // Make sure this file has the connectDB() function

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$connectDB = connectDB();
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$user_id = $_SESSION['id'];
$updateFields = [];
$params = [];
$types = '';

// Prepare query dynamically based on input
foreach ($input as $key => $value) {
    $updateFields[] = "$key = ?";
    $params[] = $value;
    $types .= 's';
}

$stmt = $connectDB->prepare("UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?");
$types .= 'i';
$params[] = $user_id;

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    // Update session variables
    foreach ($input as $k => $v) {
        $_SESSION[$k] = $v;
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
?>