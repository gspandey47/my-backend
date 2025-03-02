<?php
// Allow CORS for local development
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Database Connection
$host = "localhost";
$username = "root"; // Default XAMPP user
$password = ""; // Default XAMPP password (empty)
$database = "final_project"; // Change this to your database name

$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

// Get JSON Data from React
$data = json_decode(file_get_contents("php://input"), true);

// Validate Required Fields
if (!empty($data) && isset($data['employeeId']) && isset($data['shift']) && isset($data['reason']) && isset($data['shiftDate']) && isset($data['additionalNotes'])) {
    
    // Sanitize Input Data
    $employeeId = $conn->real_escape_string($data['employeeId']);
    $shift = $conn->real_escape_string($data['shift']);
    $reason = $conn->real_escape_string($data['reason']);
    $shiftDate = $conn->real_escape_string($data['shiftDate']);
    $additionalNotes = $conn->real_escape_string($data['additionalNotes']);

    // Insert Data into shift_requests Table
    $sql = "INSERT INTO shift_requests (employee_id, shift, reason, shift_date, additional_notes) VALUES ('$employeeId', '$shift', '$reason', '$shiftDate', '$additionalNotes')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Shift change request submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data. Please fill all required fields."]);
}

// Close Connection
$conn->close();
?>
