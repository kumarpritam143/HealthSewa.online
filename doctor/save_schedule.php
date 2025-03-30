<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add error logging
    error_log("Save Schedule Request: " . print_r($_POST, true));
    
    $doctor_id = $_SESSION['user_id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if(!$doctor_id || !$day || !$start_time || !$end_time) {
        die(json_encode([
            'success' => false, 
            'message' => 'Missing required data',
            'debug' => [
                'session' => $_SESSION,
                'post' => $_POST,
                'doctor_id' => $doctor_id
            ]
        ]));
    }

    // Validate times
    if(strtotime($end_time) <= strtotime($start_time)) {
        die(json_encode([
            'success' => false, 
            'message' => 'End time must be after start time',
            'debug' => [
                'session' => $_SESSION,
                'post' => $_POST,
                'doctor_id' => $doctor_id
            ]
        ]));
    }

    // Log the query and values
    $query = "INSERT INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
    error_log("Query: " . $query);
    error_log("Values: doctor_id=$doctor_id, day=$day, start=$start_time, end=$end_time");

    try {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $doctor_id, $day, $start_time, $end_time);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'debug' => [
                    'session' => $_SESSION,
                    'post' => $_POST,
                    'doctor_id' => $doctor_id,
                    'affected_rows' => $stmt->affected_rows,
                    'insert_id' => $stmt->insert_id
                ]
            ]);
        } else {
            throw new Exception("No rows affected");
        }
    } catch (Exception $e) {
        error_log("Schedule Save Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'debug' => [
                'session' => $_SESSION,
                'post' => $_POST,
                'doctor_id' => $doctor_id,
                'mysql_error' => $conn->error
            ]
        ]);
    }
}
