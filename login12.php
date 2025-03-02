<?php
session_start(); // Start the session at the top

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

// ✅ Database Connection (Compatible with PHP 5)
$conn = new mysqli("localhost", "root", "", "final_project");

if ($conn->connect_error) {
    die(json_encode(array("error" => "Connection failed: " . $conn->connect_error)));
}

// ✅ Get JSON Data from Frontend (Compatible with PHP 5)
$data = json_decode(file_get_contents("php://input"), true);
$email = isset($data["email"]) ? trim($data["email"]) : "";
$password = isset($data["password"]) ? trim($data["password"]) : "";

if (!empty($email) && !empty($password)) {
    // ✅ Debugging: Print received email and password
    error_log("Received email: $email, password: $password");

    // ✅ Check if user exists in register table (for regular users)
    $sql = "SELECT * FROM register WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die(json_encode(array("error" => "SQL Error: " . $conn->error)));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        error_log("User found in register table: " . print_r($user, true)); // Debugging

        if (password_verify($password, $user['password'])) {
            // ✅ Insert login details into logedin table
            $insert_sql = "INSERT INTO logedin (email, password) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                die(json_encode(array("error" => "Insert SQL Error: " . $conn->error)));
            }

            $insert_stmt->bind_param("ss", $email, $user['password']);
            $insert_stmt->execute();
            $insert_stmt->close();

            // ✅ Return success response for regular user
            echo json_encode(array(
                "success" => true,
                "message" => "Login successful",
                "role" => "user"
            ));
            exit();
        } else {
            error_log("Password verification failed for register table"); // Debugging
        }
    }

    // ✅ Check if user exists in admin table (for admin users)
    $sql = "SELECT * FROM admin WHERE email = ? AND Password = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die(json_encode(array("error" => "SQL Error: " . $conn->error)));
    }

    $stmt->bind_param("si", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        error_log("Admin found: " . print_r($admin, true)); // Debugging

        // ✅ Insert login details into logedin table
        $insert_sql = "INSERT INTO logedin (email, password) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if (!$insert_stmt) {
            die(json_encode(array("error" => "Insert SQL Error: " . $conn->error)));
        }

        $insert_stmt->bind_param("ss", $email, $password);
        $insert_stmt->execute();
        $insert_stmt->close();

        // ✅ Return success response for admin user
        echo json_encode(array(
            "success" => true,
            "message" => "Login successful",
            "role" => "admin"
        ));
        exit();
    } else {
        error_log("Admin not found or password mismatch"); // Debugging
    }

    // ✅ If no match found
    echo json_encode(array("success" => false, "error" => "Invalid email or password"));
    $stmt->close();
} else {
    echo json_encode(array("error" => "Invalid input"));
}

$conn->close();
?>