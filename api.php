<?php
// Allow CORS for local development
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Get raw POST data from React
$data = json_decode(file_get_contents("php://input"), true);

// Check if data is received properly
if (!empty($data) && isset($data['name']) && isset($data['email']) && isset($data['password'])) {
    $response = array("message" => "PHP and React connected successfully!", "received_data" => $data);
} else {
    $response = array("message" => "Missing data from React", "received_data" => $data);
}

// Return response as JSON
echo json_encode($response);
?>
