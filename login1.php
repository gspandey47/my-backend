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
$usernameOrPassword = isset($data["usernameOrPassword"]) ? trim($data["usernameOrPassword"]) : "";

if (!empty($email) && !empty($usernameOrPassword)) {
    // ✅ Debugging: Print received email and usernameOrPassword
    error_log("Received email: $email, usernameOrPassword: $usernameOrPassword");

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

        // ✅ Check if usernameOrPassword matches either username or password
        $usernameMatch = ($usernameOrPassword === $user['username']); // Check username match
        $passwordMatch = password_verify($usernameOrPassword, $user['password']); // Check password match

        if ($usernameMatch || $passwordMatch) {
            // ✅ Store email in session
            $_SESSION['email'] = $email;
            session_write_close(); // Save session

            // ✅ Insert login details into logedin table
            $insert_sql = "INSERT INTO logedin (email, username, password) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                die(json_encode(array("error" => "Insert SQL Error: " . $conn->error)));
            }

            $insert_stmt->bind_param("sss", $email, $user['username'], $user['password']);
            $insert_stmt->execute();
            $insert_stmt->close();

            // ✅ Return success response for regular user
            echo json_encode(array(
                "success" => true,
                "message" => "Login successful",
                "role" => "user",
                "session_email" => $_SESSION['email'] // Debugging: Check session email
            ));
            exit();
        } else {
            error_log("Username or password verification failed for register table"); // Debugging
        }
    }

    // ✅ Check if user exists in admin table (for admin users)
    $sql = "SELECT * FROM admin WHERE email = ? AND (username = ? OR password = ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die(json_encode(array("error" => "SQL Error: " . $conn->error)));
    }

    $stmt->bind_param("sss", $email, $usernameOrPassword, $usernameOrPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        error_log("Admin found: " . print_r($admin, true)); // Debugging

        // ✅ Store email in session
        $_SESSION['email'] = $email;
        session_write_close(); // Save session

        // ✅ Insert login details into logedin table
        $insert_sql = "INSERT INTO logedin (email, username, password) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if (!$insert_stmt) {
            die(json_encode(array("error" => "Insert SQL Error: " . $conn->error)));
        }

        $insert_stmt->bind_param("sss", $email, $admin['username'], $admin['password']);
        $insert_stmt->execute();
        $insert_stmt->close();

        // ✅ Return success response for admin user
        echo json_encode(array(
            "success" => true,
            "message" => "Login successful",
            "role" => "admin",
            "session_email" => $_SESSION['email'] // Debugging: Check session email
        ));
        exit();
    } else {
        error_log("Admin not found or password mismatch"); // Debugging
    }

    // ✅ If no match found
    echo json_encode(array("success" => false, "error" => "Invalid email, username, or password"));
    $stmt->close();
} else {
    echo json_encode(array("error" => "Invalid input"));
}

$conn->close();
?>