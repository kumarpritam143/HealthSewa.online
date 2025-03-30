<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

// Doctor ka ID session se fetch karna
$doctor_id = $_SESSION['user_id']; // DT1001, DT1002 jaisa

// Debug: Print doctor ID


// Update query to include patient details
$query = "SELECT a.*, p.name as patient_name 
          FROM appointment a 
          LEFT JOIN patients p ON a.patient_id = p.id 
          WHERE a.doctor_id = ? AND a.status = 'approved' ORDER BY appointment_date DESC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $doctor_id);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Debug: Print number of rows

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients - Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>Dr. <?php echo $_SESSION['name']; ?></h3>
                <p>ID: <?php echo $_SESSION['user_id']; ?></p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="doctor.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="patients.php"><i class="fas fa-users"></i> Patients List</a></li>
                <li><a href="schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="disease.php"><i class="fas fa-file-medical"></i>Upload Report Disease</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <?php include '../includes/header.php'; ?>
            
            <div class="dashboard-content">
                <h2>Approved Appointments</h2>
                
                <?php if ($result->num_rows > 0): ?>
                    <table class="patients-table">
                        <thead>
                            <tr>
                               
                                <th>Patient ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                  
                                    <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($row['appointment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                    <td>
                                        <button class="btn-view" onclick="location.href='add_prescription.php?appointment_id=<?php echo $row['id']; ?>&patient_id=<?php echo $row['patient_id']; ?>'">
                                            <i class="fas fa-prescription-bottle-medical"></i> Add Prescription
                                        </button>
                                        <button class="btn-report" onclick="location.href='view_report.php?appointment_id=<?php echo $row['id']; ?>&patient_id=<?php echo $row['patient_id']; ?>'">
                                            <i class="fas fa-file-medical"></i> View Report
                                        </button>
                                        <button class="btn-update" onclick="location.href='update_report.php?appointment_id=<?php echo $row['id']; ?>&patient_id=<?php echo $row['patient_id']; ?>'">
                                            <i class="fas fa-edit"></i> Update Report
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-appointments">
                        <i class="fas fa-info-circle"></i>
                        <p>No approved appointments found.</p>
                    </div>
                <?php endif; ?>
            </div>


        </div>
    </div>
    <script src="../js/script.js"></script>
</body> 
</html>

<style>
    .patients-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .patients-table th {
        background: #f8f9fa;
        padding: 12px 15px;
        font-weight: 600;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
    }

    .patients-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
    }

    .patients-table tr:hover {
        background: #f8f9fa;
    }

    .btn-view, .btn-report, .btn-update {
        padding: 6px 12px;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 5px;
    }

    .btn-view {
        background: #007bff;
    }

    .btn-report {
        background: #28a745;
    }

    .btn-update {
        background: #ffc107;
        color: #000;
    }

    .btn-view:hover {
        background: #0056b3;
    }

    .btn-report:hover {
        background: #218838;
    }

    .btn-update:hover {
        background: #e0a800;
    }

    .no-appointments {
        text-align: center;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 20px;
    }

    .no-appointments i {
        font-size: 24px;
        color: #6c757d;
        margin-bottom: 10px;
    }
</style>
