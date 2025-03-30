<?php
$host = "localhost";       
$user = "u453713527_healthsewa";            
$password = "Healthsewa@1744";            
$database = "u453713527_healthsewa"; 

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
