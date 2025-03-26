<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "final_project");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Base URL for uploads
$uploadPath = "http://localhost/my-backend/uploads/";

$query = "SELECT * FROM approve_emp  ORDER BY Eid DESC"; // Fetch all approve employees
$result = $conn->query($query);

if (!$result) {
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

// Check if table is empty
if ($result->num_rows == 0) {
    die(json_encode(["error" => "No employees found"]));
}

$employees = [];

// Loop through all employees
while ($row = $result->fetch_assoc()) {
    // Ensure full image & resume URLs
    $row["image"] = (!empty($row["image"])) ? $uploadPath . basename($row["image"]) : null;
    $row["resume"] = (!empty($row["resume"])) ? $uploadPath . basename($row["resume"]) : null;

    $employees[] = $row; // Store in array
}

// Return all employees as JSON
echo json_encode($employees);
$conn->close();
?>
