<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

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

// Fetch data from shift_requests table based on employee_id
$query = "SELECT * FROM leave_requests WHERE employee_id = '$employee_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Insert the approved shift request into the approved_shift table
    $insertQuery = "INSERT INTO approved_leaves (employee_id, fromDate, toDate, leaveType, reason,file,comments,LeaveDate,status)
                    VALUES ('{$row['employee_id']}', '{$row['fromDate']}',
                     '{$row['toDate']}', '{$row['leaveType']}', '{$row['reason']}', '{$row['file']}', '{$row['comments']}' ,
                     '{$row['LeaveDate']}','Approved')";


// $insertQuery = "INSERT INTO approved_shift (employee_id, shift, reason, shift_date, additional_notes, request_date, status) 
// VALUES ('{$row['employee_id']}', '{$row['shift']}', '{$row['reason']}', '{$row['shift_date']}', '{$row['additional_notes']}', '{$row['request_date']}', 'Approved')";


    if ($conn->query($insertQuery) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Shift request approved successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Approval failed: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No matching shift request found."]);
}

$conn->close();
?>
