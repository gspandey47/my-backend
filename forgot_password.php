<?php
header("Access-Control-Allow-Origin: *"); // Allow all domains to access
header("Content-Type: application/json; charset=UTF-8"); // Set response type to JSON
header("Access-Control-Allow-Methods: POST"); // Allow only POST requests
header("Access-Control-Max-Age: 3600"); // Cache preflight request for 1 hour
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection (older PHP version compatible)
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "final_project"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from request
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$newUsername = $data['username'];
$newPassword = $data['password'];

// Check if email exists in the register table
$sql = "SELECT * FROM register WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Email exists, update username and password
    $updateSql = "UPDATE register SET username = '$newUsername', password = '$newPassword' WHERE email = '$email'";
    if ($conn->query($updateSql) === TRUE) {
        echo json_encode(array("message" => "Username and password updated successfully."));
    } else {
        echo json_encode(array("message" => "Error updating record: " . $conn->error));
    }
} else {
    // Email does not exist
    echo json_encode(array("message" => "Email not found in the database."));
}

$conn->close();
?>