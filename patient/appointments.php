<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$page_name = 'appointments';
$patient_id = $_SESSION['user_id'];

// Fetch appointments with doctor details
$sql = "SELECT a.*, d.name as doctor_name, d.specialization 
        FROM appointment a 
        JOIN doctor d ON a.doctor_id = d.id 
        WHERE a.patient_id = ? 
        ORDER BY a.appointment_date ASC, a.appointment_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .appointments-container {
            padding: 20px;
        }
        
        .appointment-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
            position: relative;
            border-left: 4px solid #2c71d3;
        }

        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #cce5ff;
            color: #004085;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .doctor-info {
            margin-bottom: 15px;
        }

        .doctor-name {
            font-size: 18px;
            font-weight: 600;
            color: #2c71d3;
            margin: 0;
        }

        .doctor-specialization {
            color: #666;
            font-size: 14px;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-item i {
            color: #2c71d3;
            width: 16px;
        }

        .no-appointments {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .appointment-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn-cancel {
            background: none;
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>
            
            <div class="appointments-container">
                <h2 class="mb-4">My Appointments</h2>
                
                <?php if($result->num_rows > 0): ?>
                    <?php while($appointment = $result->fetch_assoc()): ?>
                        <div class="appointment-card" data-appointment-id="<?php echo $appointment['id']; ?>">
                            <?php
                            $status_class = '';
                            switch($appointment['status']) {
                                case 'pending':
                                    $status_class = 'status-pending';
                                    break;
                                case 'confirmed':
                                    $status_class = 'status-confirmed';
                                    break;
                                case 'completed':
                                    $status_class = 'status-completed';
                                    break;
                                case 'cancelled':
                                    $status_class = 'status-cancelled';
                                    break;
                            }
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo ucfirst($appointment['status']); ?>
                            </span>
                            
                            <div class="doctor-info">
                                <h3 class="doctor-name">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h3>
                                <span class="doctor-specialization"><?php echo htmlspecialchars($appointment['specialization']); ?></span>
                            </div>
                            
                            <div class="appointment-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('D, d M Y', strtotime($appointment['appointment_date'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-comment-medical"></i>
                                    <span><?php echo htmlspecialchars($appointment['reason']); ?></span>
                                </div>
                            </div>
                            
                            <?php if($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                                <div class="appointment-actions">
                                    <button class="btn-cancel" onclick="cancelAppointment(<?php echo $appointment['id']; ?>)">
                                        <i class="fas fa-times"></i> Cancel Appointment
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-appointments">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <h3>No Appointments Found</h3>
                        <p>You don't have any appointments scheduled.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/chatbot_button.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>
