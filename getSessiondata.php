<?php
session_start(); // Start the session

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); // ✅ Allow Dynamic Origin
} else {
    header("Access-Control-Allow-Origin: http://localhost:5174"); // ✅ Replace with your frontend URL
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// ✅ Handle Preflight Request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Check if session data exists
if (isset($_SESSION['email']) && isset($_SESSION['Id'])) {
    echo json_encode([
        "email" => $_SESSION['email'],
        "Id" => $_SESSION['Id'],
    ]);
} else {
    echo json_encode([
        "error" => "Session data not found.",
    ]);
}
?>