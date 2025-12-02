<?php
// Example admin credentials
$firstName = "Limuel";
$lastName = "Maala";
$email = "23-62769@g.batstate-u.edu.ph";
$password = "superpogi123";

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Database connection
$conn = new mysqli("localhost", "root", "", "onlinebankingdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO admin (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

if ($stmt->execute()) {
    echo "Admin created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>