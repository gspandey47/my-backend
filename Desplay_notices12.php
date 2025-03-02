<?php
session_start();

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

// Allow dynamic origin but still keep security
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'db_connection.php';

// Check if session already has employee_id
if (!isset($_SESSION['Id'])) {
    echo json_encode(["success" => false, "error" => "Session not set"]);
    exit();
}

$employeeId = $_SESSION['Id']; // Get employee ID from session

// Fetch Private Notices (Only for the logged-in employee)
$privateQuery = "SELECT * FROM employee_notice WHERE employeeId = '$employeeId'";
$privateResult = mysqli_query($conn, $privateQuery);
$privateNotices = mysqli_fetch_all($privateResult, MYSQLI_ASSOC);

// Fetch Public Notices
$publicQuery = "SELECT * FROM public_notice";
$publicResult = mysqli_query($conn, $publicQuery);
$publicNotices = mysqli_fetch_all($publicResult, MYSQLI_ASSOC);

$response = [
    "success" => true,
    "private_notices" => $privateNotices,
    "public_notices" => $publicNotices
];

echo json_encode($response);
?>
