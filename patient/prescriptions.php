<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['user_id'];

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Updated query with correct table names and error checking
$query = "SELECT p.*, a.appointment_date, a.appointment_time, a.reason, a.status,
          d.name as doctor_name, d.specialization
          FROM prescription p 
          JOIN appointment a ON p.appointment_id = a.id
          JOIN doctor d ON p.doctor_id = d.id
          WHERE p.patient_id = ?";

$stmt = $conn->prepare($query);

// Check if prepare failed
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $patient_id);

// Check if execution failed
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Debug output
if ($result === false) {
    die("Query failed: " . $conn->error);
}

// Add this for debugging
echo "<!-- Found " . $result->num_rows . " records -->"; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My prescription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .prescription-list {
            padding: 20px;
        }
        .prescription-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #2c71d3;
        }
        .doctor-info {
            margin-bottom: 15px;
        }
        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .view-report-btn {
            background: #2c71d3;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .view-report-btn:hover {
            background: #235ab0;
        }
        .medical-report {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .report-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #2c71d3;
            margin-bottom: 20px;
        }
        .report-header h3 {
            color: #2c71d3;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .patient-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .medical-details {
            margin: 20px 0;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .medical-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .medical-details th, .medical-details td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .medical-details th {
            background: #f8f9fa;
            font-weight: 600;
            text-align: left;
            width: 30%;
        }
        .prescription-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .doctor-signature {
            text-align: right;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .vital-signs {
            display: flex;
            gap: 30px;
            margin: 20px 0;
            padding: 15px;
            background: #e8f4f8;
            border-radius: 8px;
        }
        .vital-sign-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .vital-sign-item i {
            color: #2c71d3;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        <div class="main-content">
          
            <div class="container">
                <h2>My Medical Records</h2>
                
                <div class="prescription-list">
                    <?php 
                    if ($result && $result->num_rows > 0): 
                        while ($row = $result->fetch_assoc()): 
                            // Debug output
                            echo "<!-- Processing record ID: " . ($row['id'] ?? 'unknown') . " -->";
                    ?>
                            <div class="prescription-card">
                                <div class="doctor-info">
                                    <h3>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></h3>
                                    <span><?php echo htmlspecialchars($row['specialization']); ?></span>
                                </div>
                                
                                <div class="appointment-details">
                                    <?php
                                    // Add null checks for each field
                                    $appointmentDate = !empty($row['appointment_date']) ? date('d M Y', strtotime($row['appointment_date'])) : 'N/A';
                                    $appointmentTime = !empty($row['appointment_time']) ? date('h:i A', strtotime($row['appointment_time'])) : 'N/A';
                                    ?>
                                    <div>
                                        <i class="fas fa-calendar"></i>
                                        <strong>Date:</strong> 
                                        <?php echo $appointmentDate; ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-clock"></i>
                                        <strong>Time:</strong> 
                                        <?php echo $appointmentTime; ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-comment-medical"></i>
                                        <strong>Reason:</strong> 
                                        <?php echo htmlspecialchars($row['reason']); ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Status:</strong> 
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </div>
                                </div>

                                <div class="report-actions">
                                    <button class="view-report-btn" onclick="showPrescriptionDetails('<?php echo $row['id']; ?>')">
                                        <i class="fas fa-file-medical"></i> View Full Report
                                    </button>
                                  
                                </div>

                                <div id="prescription-details-<?php echo $row['id']; ?>" style="display: none;" class="medical-report">
                                    <div class="report-header">
                                        <h3>Medical Report</h3>
                                        <p>Report Date: <?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
                                    </div>

                                    <div class="patient-info">
                                        <div>
                                            <h4><i class="fas fa-user"></i> Patient Details</h4>
                                            <p><strong>ID:</strong> <?php echo htmlspecialchars($row['patient_id']); ?></p>
                                            <p><strong>Appointment:</strong> #<?php echo htmlspecialchars($row['appointment_id']); ?></p>
                                        </div>
                                        <div>
                                            <h4><i class="fas fa-calendar-check"></i> Appointment Info</h4>
                                            <p><strong>Date:</strong> <?php echo $appointmentDate; ?></p>
                                            <p><strong>Time:</strong> <?php echo $appointmentTime; ?></p>
                                        </div>
                                    </div>

                                    <div class="vital-signs">
                                        <div class="vital-sign-item">
                                            <i class="fas fa-heartbeat fa-lg"></i>
                                            <div>
                                                <strong>Blood Pressure</strong><br>
                                                <?php echo htmlspecialchars($row['blood_pressure']); ?> mmHg
                                            </div>
                                        </div>
                                        <div class="vital-sign-item">
                                            <i class="fas fa-thermometer-half fa-lg"></i>
                                            <div>
                                                <strong>Temperature</strong><br>
                                                <?php echo htmlspecialchars($row['temperature']); ?>°F
                                            </div>
                                        </div>
                                    </div>

                                    <div class="medical-details">
                                        <table>
                                            <tr>
                                                <th>Symptoms</th>
                                                <td><?php echo nl2br(htmlspecialchars($row['symptoms'])); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Diagnosis</th>
                                                <td><?php echo nl2br(htmlspecialchars($row['diagnosis'])); ?></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="prescription-section">
                                        <h4><i class="fas fa-prescription"></i> Prescribed Medications</h4>
                                        <div style="white-space: pre-line;">
                                            <?php echo htmlspecialchars($row['medications']); ?>
                                        </div>
                                    </div>

                                    <?php if(!empty($row['notes'])): ?>
                                    <div class="medical-details">
                                        <table>
                                            <tr>
                                                <th>Additional Notes</th>
                                                <td><?php echo nl2br(htmlspecialchars($row['notes'])); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php endif; ?>

                                    <div class="doctor-signature">
                                        <p><strong>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></strong></p>
                                        <p><?php echo htmlspecialchars($row['specialization']); ?></p>
                                    </div>
                                </div>
                            </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <p>No medical records found. <?php echo $conn->error ? "Error: " . $conn->error : ""; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    function showPrescriptionDetails(id) {
        const details = document.getElementById(`prescription-details-${id}`);
        if (details.style.display === 'none') {
            details.style.display = 'block';
        } else {
            details.style.display = 'none';
        }
    }
    </script>
    <?php include '../includes/chatbot_button.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>
