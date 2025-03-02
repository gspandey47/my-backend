<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'db_connection.php';

$title = isset($_POST['title']) ? $_POST['title'] : '';
$notice_message = isset($_POST['message']) ? $_POST['message'] : '';

$image_file = "";
$pdf_file = "";

// Handle Image Upload
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_file = "uploads/" . time() . "_" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $image_file);
}

// Handle PDF Upload
if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
    $pdf_file = "uploads/" . time() . "_" . basename($_FILES['pdf']['name']);
    move_uploaded_file($_FILES['pdf']['tmp_name'], $pdf_file);
}

$sql = "INSERT INTO public_notice (title, notice_message, image_file, pdf_file, notice_date) VALUES ('$title', '$notice_message', '$image_file', '$pdf_file', NOW())";

if (mysqli_query($conn, $sql)) {
    echo json_encode(array("success" => true, "message" => "Notice added successfully"));
} else {
    echo json_encode(array("success" => false, "message" => "Failed to add notice"));
}

mysqli_close($conn);
?>
