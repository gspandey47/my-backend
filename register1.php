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
    die(json_encode(["message" => "Database connection failed: " . $conn->connect_error]));
}

// Get JSON Data from React
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data) && isset($data['name']) && isset($data['email']) && isset($data['password'])) {
    $name = $conn->real_escape_string($data['name']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hashing password for security

    // Insert Data into Database
    $sql = "INSERT INTO register (`username`, `email`, `password`) VALUES ('$name', '$email', '$password')";


    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "User registered successfully!"]);
    } else {
        echo json_encode(["message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["message" => "Invalid input data"]);
}

// Close Connection
$conn->close();
?>
