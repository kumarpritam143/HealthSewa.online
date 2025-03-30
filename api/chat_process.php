<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? strtolower($_POST['message']) : '';
    
    // Simple response logic
    $response = '';
    if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
        $response = "Hello! How can I help you today?";
    }
    elseif (strpos($message, 'appointment') !== false) {
        $response = "You can book an appointment by going to the Appointments section in the sidebar menu.";
    }
    elseif (strpos($message, 'prescription') !== false) {
        $response = "You can view your prescriptions in the Prescriptions section.";
    }
    elseif (strpos($message, 'medical history') !== false) {
        $response = "Your medical history is available in the Medical History section.";
    }
    else {
        $response = "I'm sorry, I couldn't understand that. Could you please rephrase your question?";
    }

    echo json_encode(['response' => $response]);
    exit();
}
