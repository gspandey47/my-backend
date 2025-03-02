<?php
session_start();

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

// ✅ Database connection
$conn = new mysqli("localhost", "root", "", "final_project");

if ($conn->connect_error) {
    die(json_encode(array("error" => "Connection failed: " . $conn->connect_error)));
}

// ✅ Check if session email exists
if (!isset($_SESSION['email'])) {
    die(json_encode(array("success" => false, "error" => "Session not found")));
}

$email = $_SESSION['email'];

// ✅ Fetch employee details where email matches `approve_emp` table
$sql = "SELECT Id, name, email, mobile, date, age, gender, adhaar, pan, account, dob, address, city, nominee, image FROM approve_emp WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();

    // ✅ Storing `Id` in session variable
    $_SESSION['Id'] = $employee['Id'];

    echo json_encode(array("success" => true, "employee" => $employee));
} else {
    echo json_encode(array("success" => false, "error" => "No employee found"));
}

$stmt->close();
$conn->close();
?>
