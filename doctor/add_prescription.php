<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['appointment_id'])) {
    die("Invalid request.");
}

$appointment_id = $_GET['appointment_id'];
$doctor_id = $_SESSION['user_id'];

// Fetch appointment details
$query = "SELECT * FROM appointment WHERE id = ? AND doctor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $appointment_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    die("Appointment not found.");
}

// Fetch patient details
$patient_query = "SELECT * FROM patient WHERE id = ?";
$stmt = $conn->prepare($patient_query);
$stmt->bind_param("s", $appointment['patient_id']);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug section
    echo "<div style='background: #f4f4f4; padding: 20px; margin: 20px; border: 1px solid #ddd;'>";
    echo "<h3>Debug Information:</h3>";
    
    // Check prescriptions table structure
    echo "<h4>Prescriptions Table Structure:</h4>";
    $table_check = $conn->query("SHOW COLUMNS FROM prescriptions");
    echo "<pre>";
    while($col = $table_check->fetch_assoc()) {
        print_r($col);
    }
    echo "</pre>";
    
    // Show POST data
    echo "<h4>POST Data:</h4>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Show Variables to be inserted
    echo "<h4>Variables for Insert:</h4>";
    echo "Patient ID: " . $appointment['patient_id'] . "<br>";
    echo "Doctor ID: " . $doctor_id . "<br>";
    echo "Appointment ID: " . $appointment_id . "<br>";
    
    echo "</div>";
    
    // Continue with original code
    $patient_id = $appointment['patient_id'];
    $symptoms = $_POST['symptoms'];
    $diagnosis = $_POST['diagnosis'];
    $blood_pressure = $_POST['blood_pressure'];
    $temperature = $_POST['temperature'];
    $medications = $_POST['medications'];
    $notes = $_POST['notes'];

    $insertQuery = "INSERT INTO prescription (patient_id, doctor_id, appointment_id, symptoms, diagnosis, blood_pressure, temperature, medications, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    // Debug the SQL query
    echo "<div style='background: #f4f4f4; padding: 20px; margin: 20px; border: 1px solid #ddd;'>";
    echo "<h4>SQL Query:</h4>";
    echo $insertQuery;
    echo "</div>";
    
    $stmt->bind_param("sssssssss", $patient_id, $doctor_id, $appointment_id, $symptoms, $diagnosis, $blood_pressure, $temperature, $medications, $notes);
    
    try {
        $stmt->execute();
        echo "<script>alert('Prescription added successfully!'); window.location.href='appointments.php';</script>";
    } catch (mysqli_sql_exception $e) {
        echo "<div style='background: #ffe6e6; padding: 20px; margin: 20px; border: 1px solid #ff9999;'>";
        echo "<h4>Error Details:</h4>";
        echo "Error Message: " . $e->getMessage() . "<br>";
        echo "Error Code: " . $e->getCode() . "<br>";
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Prescription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .prescription-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .prescription-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
            margin-bottom: 30px;
        }

        .prescription-header h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .doctor-details {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .patient-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group textarea {
            min-height: 100px;
        }

        .vitals {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .prescription-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #007bff;
            text-align: right;
        }

        .btn-save {
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-save:hover {
            background: #0056b3;
        }

        @media print {
            .btn-save, .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="prescription-container">
        <div class="prescription-header">
            <h1>Medical Prescription</h1>
            <p>MediLink Healthcare</p>
        </div>

        <div class="doctor-details">
            <strong>Dr. <?php echo htmlspecialchars($_SESSION['name']); ?></strong><br>
            <small>Doctor ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></small>
        </div>

        <div class="patient-details">
            <div>
                <strong>Patient Name:</strong> <?php echo htmlspecialchars($patient['name']); ?><br>
                <strong>Patient ID:</strong> <?php echo htmlspecialchars($patient['id']); ?><br>
                <strong>Age:</strong> <?php echo htmlspecialchars($patient['age']); ?> years
            </div>
            <div>
                <strong>Date:</strong> <?php echo date('d M Y'); ?><br>
                <strong>Time:</strong> <?php echo date('h:i A'); ?><br>
                <strong>Appointment ID:</strong> <?php echo htmlspecialchars($appointment_id); ?>
            </div>
        </div>

        <form method="POST">
            <div class="vitals">
                <div class="form-group">
                    <label><i class="fas fa-heartbeat"></i> Blood Pressure (mm/Hg)</label>
                    <input type="text" name="blood_pressure" required placeholder="e.g., 120/80">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-thermometer-half"></i> Temperature (°F)</label>
                    <input type="text" name="temperature" required placeholder="e.g., 98.6">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-notes-medical"></i> Symptoms</label>
                <textarea name="symptoms" required placeholder="Patient's symptoms..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-stethoscope"></i> Diagnosis</label>
                <textarea name="diagnosis" required placeholder="Medical diagnosis..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-prescription"></i> Medications</label>
                <textarea name="medications" required placeholder="Medicine name, dosage, duration..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-comment-medical"></i> Additional Notes</label>
                <textarea name="notes" placeholder="Additional instructions or notes..."></textarea>
            </div>

            <div class="prescription-footer">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save & Print Prescription
                </button>
            </div>
        </form>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            if(this.checkValidity()) {
                if(confirm('Are you sure you want to save this prescription?')) {
                    // Will proceed with form submission
                    setTimeout(() => {
                        window.print();
                    }, 1000);
                } else {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>
