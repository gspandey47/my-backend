<?php

// ✅ CORS Headers (Allow All Origins Securely)
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ✅ Handle Preflight Requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

$response = array("success" => false, "error" => "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employeeId = $_POST["employeeId"];
    $title = $_POST["title"];
    $message = $_POST["message"];

    // Handle file uploads
    $imagePath = null;
    $pdfPath = null;

    if (isset($_FILES["imageFile"]) && $_FILES["imageFile"]["error"] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES["imageFile"]["name"]);
        $imagePath = "uploads/" . uniqid() . "_" . $imageName;
        move_uploaded_file($_FILES["imageFile"]["tmp_name"], $imagePath);
    }

    if (isset($_FILES["pdfFile"]) && $_FILES["pdfFile"]["error"] === UPLOAD_ERR_OK) {
        $pdfName = basename($_FILES["pdfFile"]["name"]);
        $pdfPath = "uploads/" . uniqid() . "_" . $pdfName;
        move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $pdfPath);
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO Employee_notice (employeeId, title, message, image_file, pdf_file) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $employeeId, $title, $message, $imagePath, $pdfPath);

    if ($stmt->execute()) {
        $response["success"] = true;
    } else {
        $response["error"] = "Failed to insert notice into database.";
    }

    $stmt->close();
} else {
    $response["error"] = "Invalid request method.";
}

$conn->close();
echo json_encode($response);
?>