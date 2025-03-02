<?php
session_start();

// ✅ Dynamically Allow Origin Based on Request
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); // ✅ Allow Dynamic Origin
} else {
    header("Access-Control-Allow-Origin: http://localhost"); // ✅ Default for localhost
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

// ✅ Database connection
$conn = new mysqli("localhost", "root", "", "final_project");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// ✅ Check if session Id exists
if (!isset($_SESSION['Id'])) {
    die(json_encode(["success" => false, "error" => "Session Id not found"]));
}

$employee_id = $_SESSION['Id'];

// ✅ Fetch data from approved_shift table
$sql = "SELECT employee_id, shift, reason, shift_date, additional_notes, request_date, status FROM approved_shift WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$shifts = [];
while ($row = $result->fetch_assoc()) {
    $shifts[] = $row;
}

// ✅ Return JSON response
echo json_encode(["success" => true, "shifts" => $shifts]);

$stmt->close();
$conn->close();
?>
