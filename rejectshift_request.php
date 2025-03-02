<?php
// Allow requests from any origin (Fix CORS issue)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "final_project";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['employee_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request. Employee ID is required."]);
    exit;
}

$employee_id = $conn->real_escape_string($data['employee_id']);
$rejectMessage = "Sorry, I will not change your shift because of some problems. Please meet me in call/personally.";

$insertQuery = "INSERT INTO rejectshift (employee_id, status, message) VALUES ('$employee_id', 'Rejected', '$rejectMessage')";

if ($conn->query($insertQuery) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Shift request rejected and stored in the database."]);
} else {
    echo json_encode(["status" => "error", "message" => "Rejection failed: " . $conn->error]);
}

$conn->close();
?>
