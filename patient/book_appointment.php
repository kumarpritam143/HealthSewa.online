<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$page_name = 'appointment';
$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '';

// Fetch doctor details
$stmt = $conn->prepare("SELECT * FROM doctor WHERE id = ?");
$stmt->bind_param("s", $doctor_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();

if(!$doctor) {
    header("Location: patient.php");
    exit();
}

// Handle appointment booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $date = $_POST['appointment_date'];
        $time = $_POST['appointment_time'];
        $reason = $_POST['reason'];
        $patient_id = $_SESSION['user_id'];
        
        // First verify if patient exists
        $check_patient = $conn->prepare("SELECT id FROM patient WHERE id = ?");
        $check_patient->bind_param("s", $patient_id);
        $check_patient->execute();
        $patient_result = $check_patient->get_result();
        
        if($patient_result->num_rows === 0) {
            throw new Exception("Invalid patient ID");
        }
        
        $stmt = $conn->prepare("INSERT INTO appointment (doctor_id, patient_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("sssss", $doctor_id, $patient_id, $date, $time, $reason);
        
        if($stmt->execute()) {
            $success = "Appointment requested successfully!";
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        $error = "Error booking appointment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .booking-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .selected-doctor {
            padding: 15px;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 5px;
            color: #444;
        }
        .btn-submit {
            background: #2c71d3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background: #1b5bb4;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>
            
            <div class="booking-form">
                <h3>Book Appointment</h3>
                
                <?php if(isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="selected-doctor">
                    <h4><?php echo htmlspecialchars($doctor['name']); ?></h4>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                    <p><strong>Availability:</strong> <?php echo htmlspecialchars($doctor['availability']); ?></p>
                </div>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Appointment Date</label>
                        <input type="date" name="appointment_date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Preferred Time</label>
                        <input type="time" name="appointment_time" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason for Visit</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-calendar-check"></i> 
                        Confirm Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
