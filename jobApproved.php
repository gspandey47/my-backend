<?php
include 'db_connection.php'; // Ensure this file is correctly configured

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Decode the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["Id"])) {
    $id = $data["Id"];

    // Fetch employee data from the original table (assumed to be "emp_db")
    $query = "SELECT * FROM emp_db WHERE Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();

        // Prepare the INSERT query for the approve_emp table using your table structure
        $insertQuery = "INSERT INTO approve_emp 
            (Id, name, email, mobile, date, age, gender, adhaar, pan, account, dob, address, city, nominee, password, image, resume) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        // The type string below corresponds to:
        // Id (i), name (s), email (s), mobile (i), date (s), age (i), gender (s), 
        // adhaar (s), pan (s), account (i), dob (s), address (s), city (s), nominee (s), 
        // password (s), image (s), resume (s)
        $types = "issisisssisssssss";  // 17 characters for 17 columns

        $insertStmt->bind_param(
            $types,
            $employee['Id'],
            $employee['name'],
            $employee['email'],
            $employee['mobile'],
            $employee['date'],
            $employee['age'],
            $employee['gender'],
            $employee['adhaar'],
            $employee['pan'],
            $employee['account'],
            $employee['dob'],
            $employee['address'],
            $employee['city'],
            $employee['nominee'],
            $employee['password'],
            $employee['image'],
            $employee['resume']
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Employee approved and moved."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error inserting employee: " . $insertStmt->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Employee not found."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
