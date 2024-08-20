<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection file
include '../config/db.php';

// Get the data from the request
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$email = $data['email'] ?? null; // Allow email to be null
$password = $data['password'] ?? '';

// Check if username and password are provided
if (empty($username) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
    exit();
}

// Check if the email is provided and is valid if not null
if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit();
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare the SQL statement
$query = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($query);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the statement']);
    exit();
}

// Execute the statement
$result = $stmt->execute([$username, $hashedPassword, $email]);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
} else {
    $errorInfo = $stmt->errorInfo();
    echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $errorInfo[2]]);
}
?>
