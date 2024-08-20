<?php
use \Firebase\JWT\JWT;

function authenticate() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader);

        $key = "your_secret_key"; // Ganti dengan kunci rahasia Anda
        try {
            $decoded = JWT::decode($token, $key, array('HS256'));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
    return null;
}
?>
