<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = ''; // update with your credentials
$dbName = 'final_project';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    echo json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$errors = [];

$employee_id = isset($_POST['employee_id']) ? trim($_POST['employee_id']) : '';
$from_date   = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
$to_date     = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
$leave_type  = isset($_POST['leave_type']) ? trim($_POST['leave_type']) : '';
$reason      = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$comments    = isset($_POST['comments']) ? trim($_POST['comments']) : '';

if (empty($employee_id))  $errors[] = "Employee ID is required.";
if (empty($from_date))    $errors[] = "From date is required.";
if (empty($to_date))      $errors[] = "To date is required.";
if (empty($leave_type))   $errors[] = "Leave type is required.";
if (empty($reason))       $errors[] = "Reason is required.";
if (empty($comments))     $errors[] = "Comments are required.";

if (!empty($errors)) {
    echo json_encode(['message' => implode(" ", $errors)]);
    exit;
}

$file_name = "";
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;
    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo json_encode(['message' => "Error uploading file."]);
        exit;
    }
}

$stmt = $conn->prepare("INSERT INTO leave_requests (employee_id, fromDate, toDate, leaveType, reason, comments, file) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['message' => "Database error: " . $conn->error]);
    exit;
}

$stmt->bind_param("issssss", $employee_id, $from_date, $to_date, $leave_type, $reason, $comments, $file_name);

if ($stmt->execute()) {
    echo json_encode(['message' => "Leave request submitted successfully."]);
} else {
    echo json_encode(['message' => "Error submitting leave request: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
