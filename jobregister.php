<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic information
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $applicationDate = $_POST['applicationDate'];
    
    // Personal details
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $adhaar = $_POST['adhaar'];
    $pan = $_POST['pan'];
    
    // Account information
    $account = $_POST['account'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $nominee = $_POST['nominee'];

    // File Upload Handling
    $imagePath = "";
    $resumePath = "";

    if (isset($_FILES['image']) && $_FILES['image']['name'] !== "") {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $imagePath = "uploads/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    if (isset($_FILES['resume']) && $_FILES['resume']['name'] !== "") {
        $resumeName = time() . "_" . basename($_FILES['resume']['name']);
        $resumePath = "uploads/" . $resumeName;
        move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath);
    }

    // Insert data into database
    $sql = "INSERT INTO emp_db (
                name, email, mobile, date, age, gender, adhaar, pan, account, dob, address, city, nominee, image, resume 
                
            ) VALUES (
                '$name', '$email', '$mobile', '$applicationDate', 
                '$age', '$gender', '$adhaar', '$pan', 
                '$account', '$dob', '$address', '$city', '$nominee', 
                '$imagePath', '$resumePath'
            )";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Registration successful!"]);
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>