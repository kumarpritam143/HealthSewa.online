<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$appointment_id = $_GET['appointment_id'] ?? '';
$patient_id = $_GET['patient_id'] ?? '';

// Fetch prescription details
$query = "SELECT p.*, pat.name as patient_name, pat.age 
          FROM prescription p 
          JOIN patient pat ON p.patient_id = pat.id 
          WHERE p.appointment_id = ? AND p.patient_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $appointment_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$prescription = $result->fetch_assoc();

if (!$prescription) {
    die("No prescription found for this appointment.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .report-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #0061f2, #00a3ff);
            color: white;
            border-radius: 8px;
        }
        .report-header h1 {
            margin: 0;
            font-size: 28px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .report-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            transition: transform 0.2s;
        }
        .report-section:hover {
            transform: translateX(5px);
        }
        .report-section h3 {
            color: #0061f2;
            margin-bottom: 15px;
            font-size: 20px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }
        .report-section p {
            margin: 10px 0;
            line-height: 1.6;
        }
        .report-section strong {
            color: #2c3e50;
        }
        .btn-back {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        @media print {
            .btn-back {
                display: none;
            }
            .report-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1>Medical Report</h1>
            <p>MediLink Healthcare</p>
        </div>

        <div class="report-section">
            <h3>Patient Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($prescription['patient_name']); ?></p>
            <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($prescription['patient_id']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($prescription['age']); ?> years</p>
        </div>

        <div class="report-section">
            <h3>Vital Signs</h3>
            <p><strong>Blood Pressure:</strong> <?php echo htmlspecialchars($prescription['blood_pressure']); ?> mm/Hg</p>
            <p><strong>Temperature:</strong> <?php echo htmlspecialchars($prescription['temperature']); ?> °F</p>
        </div>

        <div class="report-section">
            <h3>Medical Details</h3>
            <p><strong>Symptoms:</strong><br><?php echo nl2br(htmlspecialchars($prescription['symptoms'])); ?></p>
            <p><strong>Diagnosis:</strong><br><?php echo nl2br(htmlspecialchars($prescription['diagnosis'])); ?></p>
            <p><strong>Medications:</strong><br><?php echo nl2br(htmlspecialchars($prescription['medications'])); ?></p>
            <p><strong>Additional Notes:</strong><br><?php echo nl2br(htmlspecialchars($prescription['notes'])); ?></p>
        </div>

        <a href="patients.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Patients
        </a>
    </div>
</body>
</html>
