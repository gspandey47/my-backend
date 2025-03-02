<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn){
    echo "Connection Successful";
}
else{
    echo "Connection Failed";
}
?>