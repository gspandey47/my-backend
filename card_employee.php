<?php
/*
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8"); 

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch the latest logged-in user
$sql = "SELECT `email` FROM `logedin` ORDER BY `Id` DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $loggedInEmail = $row["email"];

    // Fetch employee details from emp_db based on the logged-in email
    $empSql =  "SELECT `Id`, `name`, `email`, `mobile`,`image` FROM `emp_db` WHERE email = ?";
    $stmt = $conn->prepare($empSql);
    $stmt->bind_param("s", $loggedInEmail);
    $stmt->execute();
    $empResult = $stmt->get_result();

    if ($empResult->num_rows > 0) {
        $employeeData = $empResult->fetch_assoc();
        echo json_encode($employeeData);
    } else {
        echo json_encode(["error" => "No matching employee found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "No logged-in user found"]);
}

$conn->close();
?>
*/

// ✅ Include session_start() at the top

session_start(); // ✅ Start the session

// ✅ Handle CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
} else {
    header("Access-Control-Allow-Origin: http://localhost:5174"); // ✅ Replace with your frontend URL
}

header("Content-Type: application/json; charset=UTF-8");

// ✅ Handle Preflight Request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch the latest logged-in user
$sql = "SELECT `email` FROM `logedin` ORDER BY `Id` DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $loggedInEmail = $row["email"];

    // Fetch employee details from emp_db based on the logged-in email
    $empSql = "SELECT `Id`, `name`, `email`, `mobile`, `image` FROM `emp_db` WHERE email = ?";
    $stmt = $conn->prepare($empSql);
    $stmt->bind_param("s", $loggedInEmail);
    $stmt->execute();
    $empResult = $stmt->get_result();

    if ($empResult->num_rows > 0) {
        $employeeData = $empResult->fetch_assoc();

        // ✅ Storing retrieved ID in session
        $_SESSION['Id'] = $employeeData['Id'];

        echo json_encode(["success" => true, "employee" => $employeeData]);
    } else {
        echo json_encode(["success" => false, "error" => "No matching employee found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "No logged-in user found"]);
}

$conn->close();
?>