<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
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

$sql = "SELECT * FROM rejectshift";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    echo json_encode($requests);
} else {
    echo json_encode([]);
}

$conn->close();
?>
