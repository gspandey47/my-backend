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
include "db_connection.php"; // Include your database connection file

// Check if the session ID is set
if (!isset($_SESSION['Id'])) {
    echo json_encode(["message" => "Session ID not found. Please log in."]);
    exit;
}

// Get the session_id from the session
$session_id = $_SESSION['Id'];

// Fetch attendance data for the given session_id
$sql = "SELECT type, location, DateTime, session_id, session_email FROM attendence WHERE session_id = ?  ORDER BY DateTime DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $session_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode(["message" => "No attendance records found for this session ID"]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>