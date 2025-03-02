<?php
session_start(); // Start the session

// Handle CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    header("Access-Control-Allow-Origin: http://localhost:5174");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle Preflight Request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "final_project");

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "error" => "Database connection failed: " . $conn->connect_error
    ]));
}

// Debugging: Check which session variables are set
error_log("Session variables: " . print_r($_SESSION, true));

// Get data from the frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid input data",
        "receivedData" => "No data received from frontend",
        "sessionVariables" => $_SESSION // Include session variables in the response
    ]);
    exit();
}

// Debugging: Log received data
error_log("Received data: " . print_r($data, true));

// Extract attendance data
$type = isset($data["type"]) ? $data["type"] : null; // 'in' or 'out'
$location = isset($data["location"]) ? $data["location"] : null; // Full address from OpenCage
$timestamp = isset($data["timestamp"]) ? $data["timestamp"] : null; // Timestamp from frontend

// Validate required fields
if (empty($type) || empty($location) || empty($timestamp)) {
    echo json_encode([
        "success" => false,
        "error" => "Missing required fields",
        "receivedData" => [
            "type" => $type,
            "location" => $location,
            "timestamp" => $timestamp
        ],
        "sessionVariables" => $_SESSION // Include session variables in the response
    ]);
    exit();
}

// Insert attendance data into the database
$sql = "INSERT INTO attendence (type, location, timestamp) 
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode([
        "success" => false,
        "error" => "Prepare failed: " . $conn->error,
        "sessionVariables" => $_SESSION // Include session variables in the response
    ]);
    exit();
}

$stmt->bind_param("sss", $type, $location, $timestamp);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Attendance marked successfully",
        "insertedData" => [
            "type" => $type,
            "location" => $location,
            "timestamp" => $timestamp
        ],
        "sessionVariables" => $_SESSION // Include session variables in the response
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode([
        "success" => false,
        "error" => "Error marking attendance: " . $stmt->error,
        "receivedData" => [
            "type" => $type,
            "location" => $location,
            "timestamp" => $timestamp
        ],
        "sessionVariables" => $_SESSION // Include session variables in the response
    ]);
}

$stmt->close();
$conn->close();
?>