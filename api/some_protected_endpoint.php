<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'middleware.php';

$auth = authenticate();
if ($auth === null) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Lanjutkan dengan logika endpoint jika token valid
echo json_encode(['status' => 'success', 'message' => 'Access granted']);
?>
