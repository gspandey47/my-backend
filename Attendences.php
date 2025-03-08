<?php
session_start();
include 'db_connection.php'; // Include database connection

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); // ✅ Allow Dynamic Origin
} else {
    header("Access-Control-Allow-Origin: http://localhost:5174"); // ✅ Replace with your frontend URL
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // ✅ Allow POST requests
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// ✅ Handle Preflight Request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['type'], $data['location'], $data['sessionEmail'], $data['sessionId'])) {
    echo json_encode(["error" => "Invalid input data"]);
    exit();
}

// Sanitize input
$type = mysqli_real_escape_string($conn, $data['type']);
$location = mysqli_real_escape_string($conn, $data['location']);
$sessionEmail = mysqli_real_escape_string($conn, $data['sessionEmail']);
$sessionId = intval($data['sessionId']); // Convert to integer

// Insert data into attendence table
$sql = "INSERT INTO attendence (type, location, session_email, session_id, DateTime) 
        VALUES ('$type', '$location', '$sessionEmail', $sessionId, NOW())";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["success" => "Attendance recorded successfully"]);
} else {
    echo json_encode(["error" => "Failed to insert data: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
