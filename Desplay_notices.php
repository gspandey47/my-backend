<?php
session_start();

// Get the Origin from the request headers
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

// Allow dynamic origin but still keep security
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
    echo json_encode(["success" => false, "error" => "Session Expired or Invalid"]);
    exit;
}

$employee_id = $_SESSION['Id'];

// Fetch private notices for the logged-in employee
$privateNoticesQuery = "SELECT * FROM employee_notice WHERE employeeId = ? ORDER BY Id DESC";
$stmt = $conn->prepare($privateNoticesQuery);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch public notices (No condition needed)
$publicNoticesQuery = "SELECT * FROM public_notice ORDER BY notice_id DESC";
$publicNoticesResult = $conn->query($publicNoticesQuery);


$private_notices = [];
while ($row = $result->fetch_assoc()) {
    $private_notices[] = $row;
}

$public_notices = [];
while ($row = $publicNoticesResult->fetch_assoc()) {
    $public_notices[] = $row;
}
// Send JSON response
echo json_encode([
    "success" => true,
    "public_notices" => $public_notices,
    "private_notices" => $private_notices
]);

$stmt->close();
$conn->close();
?>
