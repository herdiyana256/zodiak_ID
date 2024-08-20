<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Daftar tanda zodiak
$zodiacSigns = [
    'aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo',
    'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces'
];

// Ambil data dari body permintaan
$data = json_decode(file_get_contents("php://input"), true);
$sign = isset($data['sign']) ? strtolower(trim($data['sign'])) : ''; // Tanda zodiak
$period = isset($data['period']) ? strtolower(trim($data['period'])) : 'daily'; // Default to 'daily'

// Validasi tanda zodiak
if (!in_array($sign, $zodiacSigns)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid zodiac sign']);
    exit;
}

// API configuration
$baseApiUrl = "https://horoscope-app-api.vercel.app/api/v1/get-horoscope";

// Array untuk menyimpan hasil ramalan
$results = [];

// Tentukan endpoint berdasarkan periode
switch ($period) {
    case 'weekly':
        $apiEndpoint = "/weekly";
        break;
    case 'monthly':
        $apiEndpoint = "/monthly";
        break;
    case 'daily':
    default:
        $apiEndpoint = "/daily";
        break;
}

// Bangun URL API
$apiUrl = "$baseApiUrl$apiEndpoint?sign=$sign";

// Jika periode harian, tambahkan parameter hari
if ($period === 'daily') {
    $day = isset($data['day']) ? strtolower(trim($data['day'])) : 'today';
    $apiUrl .= "&day=$day";
}

// Set up cURL
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    $results[$sign] = ['status' => 'error', 'message' => "cURL Error #:" . $err];
} else {
    // Decode response and check if it contains valid data
    $decodedResponse = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $results[$sign] = $decodedResponse['data'] ?? ['status' => 'error', 'message' => 'No data available'];
    } else {
        $results[$sign] = ['status' => 'error', 'message' => "Invalid JSON response"];
    }
}

// Outputkan hasil sebagai JSON
echo json_encode($results);
?>
