<?php
require_once '../config/db.php';
require_once '../vendor/autoload.php'; // Autoload Composer

use \Firebase\JWT\JWT;

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!$authHeader) {
    echo json_encode(['status' => 'error', 'message' => 'Token not provided']);
    exit();
}

$token = str_replace('Bearer ', '', $authHeader);

try {
    $decoded = JWT::decode($token, 'your-secret-key', ['HS256']);
    echo json_encode(['status' => 'success', 'message' => 'Token is valid']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
}
