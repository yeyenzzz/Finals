<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

if (isset($input['email']) && !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $input['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email must be @gmail.com']);
    exit();
}
if (isset($input['phone_number']) && !preg_match('/^09\d{9}$/', $input['phone_number'])) {
    echo json_encode(['success' => false, 'message' => 'Phone must start with 09 and be 11 digits']);
    exit();
}

$connectDB = connectDB();
$user_id = $_SESSION['id'];
$updateFields = [];
$params = [];
$types = '';

// Prepare update query
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
    foreach ($input as $k => $v) {
        $_SESSION[$k] = $v;
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
$stmt->close();
?>