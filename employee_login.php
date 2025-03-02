<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost", "root", "", "final_project");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);
$Id = $data["Id"];
$email = $data["email"];

if (!empty($Id) && !empty($email)) {
    // Check if the user exists in the register table
    $sql = "SELECT * FROM approve_emp WHERE Id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $Id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, insert into logedin table
        // $insertSql = "INSERT INTO logedin (username, email) VALUES (?, ?)";
        // $insertStmt = $conn->prepare($insertSql);
        // $insertStmt->bind_param("ss", $username, $email);

       
            echo json_encode(["success" => true, "message" => "Login successfully"]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to login "]);
        }


    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid input"]);
}

$conn->close();
?>
