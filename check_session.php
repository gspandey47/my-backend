<?php
session_start(); // Start session at the top

// Check if both session email and session Id exist
if (!isset($_SESSION['email']) || !isset($_SESSION['Id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Session email or Id not found"
    ]);
    exit();
}

echo json_encode([
    "success" => true,
    "session_id" => session_id(), // Print current session ID
    "session_email" => $_SESSION['email'],
    "session_Id" => $_SESSION['Id']
]);
?>
