<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';

// Reset password
$response = [
    'status' => 'error',
    'message' => 'Password reset failed'
];

if ($username && $email) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Implement password reset process (e.g., send email with reset link)
        $response = [
            'status' => 'success',
            'message' => 'Password reset link sent'
        ];
    }
}

echo json_encode($response);
?>
