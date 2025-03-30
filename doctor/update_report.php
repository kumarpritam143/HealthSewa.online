<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$appointment_id = $_GET['appointment_id'] ?? '';
$patient_id = $_GET['patient_id'] ?? '';

// Fetch existing prescription
$query = "SELECT p.*, pat.name as patient_name, pat.age 
          FROM prescription p 
          JOIN patient pat ON p.patient_id = pat.id 
          WHERE p.appointment_id = ? AND p.patient_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $appointment_id, $patient_id);
$stmt->execute();
$prescription = $stmt->get_result()->fetch_assoc();

if (!$prescription) {
    die("No prescription found for this appointment.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $symptoms = $_POST['symptoms'];
    $diagnosis = $_POST['diagnosis'];
    $blood_pressure = $_POST['blood_pressure'];
    $temperature = $_POST['temperature'];
    $medications = $_POST['medications'];
    $notes = $_POST['notes'];

    $update_query = "UPDATE prescription SET 
                    symptoms = ?,
                    diagnosis = ?,
                    blood_pressure = ?,
                    temperature = ?,
                    medications = ?,
                    notes = ?
                    WHERE appointment_id = ? AND patient_id = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssss", 
        $symptoms, 
        $diagnosis, 
        $blood_pressure, 
        $temperature, 
        $medications, 
        $notes, 
        $appointment_id, 
        $patient_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Report updated successfully!'); window.location.href='view_report.php?appointment_id=" . $appointment_id . "&patient_id=" . $patient_id . "';</script>";
    } else {
        echo "Error updating report: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Medical Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .update-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .update-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #0061f2, #00a3ff);
            color: white;
            border-radius: 8px;
        }
        .update-header h2 {
            margin: 0;
            font-size: 28px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
            outline: none;
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .btn-update {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="update-container">
        <div class="update-header">
            <h2>Update Medical Report</h2>
        </div>
        <form method="POST">
            <div class="form-section">
                <div class="vitals-grid">
                    <div class="form-group">
                        <label><i class="fas fa-heartbeat"></i> Blood Pressure (mm/Hg)</label>
                        <input type="text" name="blood_pressure" value="<?php echo htmlspecialchars($prescription['blood_pressure']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-thermometer-half"></i> Temperature (°F)</label>
                        <input type="text" name="temperature" value="<?php echo htmlspecialchars($prescription['temperature']); ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Symptoms</label>
                <textarea name="symptoms" required><?php echo htmlspecialchars($prescription['symptoms']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Diagnosis</label>
                <textarea name="diagnosis" required><?php echo htmlspecialchars($prescription['diagnosis']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Medications</label>
                <textarea name="medications" required><?php echo htmlspecialchars($prescription['medications']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="notes"><?php echo htmlspecialchars($prescription['notes']); ?></textarea>
            </div>

            <button type="submit" class="btn-update">
                <i class="fas fa-save"></i> Update Report
            </button>
        </form>
    </div>
</body>
</html>
