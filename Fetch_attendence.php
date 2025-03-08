<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "db_connection.php"; // Ensure this file contains the correct database connection

$sql = "SELECT type, location, DateTime, session_id, session_email FROM attendence ORDER BY DateTime DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode(["message" => "No records found"]);
}

mysqli_close($conn);
?>
