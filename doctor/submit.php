<?php
include '../db.php';

$name = $conn->real_escape_string($_POST['name']);
$age = intval($_POST['age']);
$disease = $conn->real_escape_string($_POST['disease']);
$symptoms = $conn->real_escape_string($_POST['symptoms']);
$lat = isset($_POST['lat']) ? floatval($_POST['lat']) : 0;
$lng = isset($_POST['lng']) ? floatval($_POST['lng']) : 0;

if ($lat == 0 || $lng == 0) {
  die("Location not detected.");
}

$stmt = $conn->prepare("INSERT INTO reports (name, age, disease, symptoms, lat, lng) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissdd", $name, $age, $disease, $symptoms, $lat, $lng);
$stmt->execute();
$stmt->close();

header("Location: doctor.php");
?>
