<?php
// proxy.php - Place this in the same directory as your login page
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Yii2 API endpoint
$apiUrl = 'https://itrust-tech.id/web/mobile/login';

// Get POST data
$postData = $_POST;

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to true in production with proper cert
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Set headers
$headers = [
    'Accept: application/json',
    'Content-Type: multipart/form-data',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// Set response headers
header('Content-Type: application/json');
http_response_code($httpCode);

// Return the API response
echo $response;

// Log for debugging (optional)
error_log("Proxy request to $apiUrl - Status: $httpCode - Data: " . json_encode($postData));
?>