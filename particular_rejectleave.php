<?php
session_start();

// Get dynamic origin for CORS
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database connection
$conn = new mysqli("localhost", "root", "", "final_project");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database Connection Failed"]));
}

// Ensure user is logged in
if (!isset($_SESSION['Id'])) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit;
}

$employee_id = $_SESSION['Id'];
$sql = "SELECT employee_id, status, message, RejectDtae FROM rejectleaves WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$rejectedLeaves = [];
while ($row = $result->fetch_assoc()) {
    $rejectedLeaves[] = $row;
}

// Send JSON response
echo json_encode(["success" => true, "rejectedLeaves" => $rejectedLeaves]);

$stmt->close();
$conn->close();
?>
