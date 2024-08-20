<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$new_password = $data['new_password'] ?? '';

// Update password
$response = [
    'status' => 'error',
    'message' => 'Password update failed'
];

if ($username && $new_password) {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", md5($new_password), $username); // md5 untuk contoh, gunakan hashing yang lebih aman
    if ($stmt->execute()) {
        $response = [
            'status' => 'success',
            'message' => 'Password updated successfully'
        ];
    }
}

echo json_encode($response);
?>
