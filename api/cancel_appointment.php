<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $patient_id = $_SESSION['user_id'];

    try {
        // Verify appointment belongs to patient
        $stmt = $conn->prepare("SELECT * FROM appointment WHERE id = ? AND patient_id = ?");
        $stmt->bind_param("is", $appointment_id, $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Appointment not found");
        }

        // Delete the appointment
        $stmt = $conn->prepare("DELETE FROM appointment WHERE id = ? AND patient_id = ?");
        $stmt->bind_param("is", $appointment_id, $patient_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Appointment cancelled successfully']);
        } else {
            throw new Exception("Error cancelling appointment");
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}
