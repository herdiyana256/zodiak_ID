<?php
require_once '../config/db.php'; // Pastikan ini mengatur koneksi ke database
require_once '../vendor/autoload.php'; // Autoload Composer

use \Firebase\JWT\JWT;

// Ambil data dari request body
$data = json_decode(file_get_contents("php://input"));

// Validasi Input
if (!isset($data->username) || !isset($data->password)) {
    echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
    exit();
}

$usernameOrEmail = trim($data->username);
$password = trim($data->password);

// Validasi Password
if (strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long']);
    exit();
}

// Query untuk mendapatkan user berdasarkan username atau email
$query = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
$stmt = $pdo->prepare($query);
$stmt->execute(['usernameOrEmail' => $usernameOrEmail]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Debugging: Tampilkan hasil query jika perlu
// echo '<pre>' . print_r($user, true) . '</pre>'; // Uncomment this line for debugging

if ($user && password_verify($password, $user['password'])) {
    // Membuat payload untuk JWT
    $payload = [
        'iat' => time(),
        'exp' => time() + 3600, // Token kadaluarsa dalam 1 jam
        'email' => $user['email']
    ];
    $token = JWT::encode($payload, 'your-secret-key');

    echo json_encode(['status' => 'success', 'token' => $token]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid username, email or password']);
}
