<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['appointment_id'])) {
    $stmt = $conn->prepare("SELECT * FROM prescriptions WHERE appointment_id = ?");
    $stmt->bind_param("s", $_GET['appointment_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $prescription = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode(['prescription' => $prescription]);
}
?>
