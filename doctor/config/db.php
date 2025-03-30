<?php
$conn = mysqli_connect("localhost", "root", "", "medilink");
if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
