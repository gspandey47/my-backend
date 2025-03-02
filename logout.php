<?php
session_start();

// Allow dynamic frontend origin (instead of "*")
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header("Access-Control-Allow-Credentials: true"); // Required for sending cookies
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow necessary methods
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight (OPTIONS) request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Destroy session and logout user
session_unset();
session_destroy();

echo json_encode(["success" => true, "message" => "Logged out successfully"]);
?>
