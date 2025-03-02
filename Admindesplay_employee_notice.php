<?php


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


$sql = "SELECT employeeId, title, message, image_file, pdf_file, notice_date FROM employee_notice ";
$stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$leaves = [];
while ($row = $result->fetch_assoc()) {
    $leaves[] = $row;
}

// Send JSON response
echo json_encode(["success" => true, "leaves" => $leaves]);

$stmt->close();
$conn->close();
?>
