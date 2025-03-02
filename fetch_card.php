<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include "db_connection.php"; // Ensure this includes your database connection

if (!isset($_SESSION['email'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$email = $_SESSION['email'];

$query = "SELECT Id, name, email, mobile, image FROM approve_emp WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $employee]);
} else {
    echo json_encode(["success" => false, "message" => "No employee found"]);
}

$stmt->close();
$conn->close();
?>
