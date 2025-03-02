<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// header("Access-Control-Allow-Origin: http://localhost:5174"); // Allow React frontend
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");
// header("Access-Control-Allow-Credentials: true");

session_start();

// Get dynamic origin for CORS
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");



if (!isset($_SESSION['Id'])) {
    echo json_encode(["success" => false, "message" => "Session Expired. Please log in again."]);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "final_project"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Fetch rejected shifts for the logged-in user
$employee_id = $_SESSION['Id'];
$sql = "SELECT * FROM rejectshift WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$rejectShifts = [];
while ($row = $result->fetch_assoc()) {
    $rejectShifts[] = $row;
}

echo json_encode(["success" => true, "data" => $rejectShifts]);
exit();
?>
