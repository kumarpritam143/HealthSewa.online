<?php
session_start();
require_once '../config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

// Handle status updates
if(isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];
    
    $update_query = "UPDATE appointment SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_status, $appointment_id);
    if($update_stmt->execute()) {
        echo "<script>alert('Status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating status: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-content {
            padding: 30px;
            background: #f0f2f5;
            min-height: calc(100vh - 60px);
        }
        
        .appointments-section {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
        }

        .section-header h3 {
            font-size: 24px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header i {
            color: #4e73df;
        }

        .appointment-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.03);
            border: 1px solid #eef0f7;
            border-left: 5px solid #4e73df;
            transition: all 0.3s ease;
        }

        .appointment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eef0f7;
        }

        .appointment-time {
            font-size: 16px;
            color: #2c3e50;
        }

        .appointment-time i {
            color: #4e73df;
            margin-right: 8px;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
            color: #505e84;
        }

        .appointment-details p {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .appointment-details i {
            color: #4e73df;
            width: 20px;
        }

        .appointment-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eef0f7;
        }

        .status-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #505e84;
            min-width: 140px;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view {
            background-color: #4e73df;
            color: white;
        }

        .btn-view:hover {
            background-color: #2e59d9;
        }

        .btn-update {
            background-color: #1cc88a;
            color: white;
        }

        .btn-update:hover {
            background-color: #169b6b;
        }

        .no-appointments {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 16px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(28, 200, 138, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(28, 200, 138, 0); }
            100% { box-shadow: 0 0 0 0 rgba(28, 200, 138, 0); }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>Dr. <?php echo $_SESSION['name']; ?></h3>
                <p>ID: <?php echo $_SESSION['user_id']; ?></p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="doctor.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="active"><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="patients.php"><i class="fas fa-users"></i> Patients List</a></li>
                <li><a href="schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="disease.php"><i class="fas fa-file-medical"></i>Upload Report Disease</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <?php 
            $page_title = 'Appointments';
            include '../includes/header.php'; 
            ?>
            
            <div class="dashboard-content">
                <div class="appointments-section">
                    <div class="section-header">
                        <h3><i class="fas fa-calendar-check"></i> Today's Appointments</h3>
                    </div>
                    <?php
                    $doctor_id = $_SESSION['user_id'];
                    $query = "SELECT * FROM appointment WHERE doctor_id = ? ORDER BY appointment_date ASC, appointment_time ASC";
                    
                    try {
                        $stmt = $conn->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Query preparation failed: " . $conn->error);
                        }
                        
                        $stmt->bind_param("s", $doctor_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Query execution failed: " . $stmt->error);
                        }
                        
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $is_new = (strtotime($row['created_at']) > strtotime('-24 hours'));
                                ?>
                                <div class="appointment-card <?php echo $is_new ? 'new' : ''; ?>">
                                    <div class="appointment-header">
                                        <span class="appointment-time">
                                            <i class="fas fa-clock"></i> 
                                            <?php echo date('h:i A', strtotime($row['appointment_time'])); ?>
                                            <span style="color: #6c757d; margin: 0 5px;">|</span>
                                            <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?>
                                        </span>
                                        <span class="appointment-status status-<?php echo strtolower($row['status']); ?>">
                                            <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px;"></i>
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </div>
                                    <div class="appointment-details">
                                        <p><i class="fas fa-user"></i> Patient ID: <?php echo htmlspecialchars($row['patient_id']); ?></p>
                                        <p><i class="fas fa-notes-medical"></i> Reason: <?php echo htmlspecialchars($row['reason']); ?></p>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn-action btn-view" onclick="viewAppointment('<?php echo $row['id']; ?>')">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                            <select name="status" class="status-select">
                                                <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="approved" <?php echo ($row['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                                <option value="completed" <?php echo ($row['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-action btn-update">
                                                <i class="fas fa-check"></i> Update
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="no-appointments">No appointments found</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                    ?>
                </div>
            </div>


        </div>
    </div>
    <script src="../js/script.js"></script>
</body> 
</html>