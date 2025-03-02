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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $date = $_POST['Date'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $adhaar = $_POST['Adhaar'];
    $pan = $_POST['pan'];
    $account = $_POST['account'];
    $dob = $_POST['Date'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $nominee = $_POST['Nominee'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // File Upload Handling
    $imagePath = "";
    $resumePath = "";

    if (isset($_FILES['image'])) {
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
    $sql = "INSERT INTO emp_db (name, email, mobile, date, age, gender, adhaar, pan, account, dob, address, city, nominee, password, image, resume)
            VALUES ('$name', '$email', '$mobile', '$date', '$age', '$gender', '$adhaar', '$pan', '$account', '$dob', '$address', '$city', '$nominee', '$password', '$imagePath', '$resumePath')";

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
