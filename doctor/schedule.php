<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

// Use user_id as doctor_id
$doctor_id = $_SESSION['user_id'];

// Fetch doctor's schedule from database
$schedule_query = "SELECT * FROM doctor_schedule WHERE doctor_id = ?";
$stmt = $conn->prepare($schedule_query);
$stmt->bind_param("s", $doctor_id); // Changed from "i" to "s"
$stmt->execute();
$schedule_result = $stmt->get_result();
$schedules = [];
while($row = $schedule_result->fetch_assoc()) {
    $schedules[$row['day_of_week']][] = [
        'start_time' => $row['start_time'],
        'end_time' => $row['end_time'],
        'id' => $row['id']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - Doctor Dashboard</title>
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
                <li><a href="patients.php"><i class="fas fa-users"></i> Patients</a></li>
                <li class="active"><a href="schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <?php 
            $page_title = 'My Schedule';
            include '../includes/header.php'; 
            ?>
            
            <div class="dashboard-content">
                <div class="schedule-section">
                    <div class="schedule-header">
                        <h3>Weekly Schedule</h3>
                        <button class="btn-primary add-slot-btn" onclick="openAddScheduleModal()">
                            <i class="fas fa-plus"></i> Add Time Slot
                        </button>
                    </div>
                    
                    <div class="schedule-grid">
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach($days as $day) {
                            echo '<div class="day-schedule">
                                    <h4>'.$day.'</h4>
                                    <div class="time-slots">';
                            if(isset($schedules[$day])) {
                                foreach($schedules[$day] as $slot) {
                                    echo '<div class="slot">
                                            <span class="time">
                                                <i class="fas fa-clock"></i> 
                                                '.date('h:i A', strtotime($slot['start_time'])).' - '.
                                                 date('h:i A', strtotime($slot['end_time'])).'
                                            </span>
                                            <div class="slot-actions">
                                                <button class="btn-icon" onclick="editSchedule('.$slot['id'].')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon delete" onclick="deleteSchedule('.$slot['id'].')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>';
                                }
                            } else {
                                echo '<div class="no-slots">No time slots added</div>';
                            }
                            echo '</div></div>';
                        }
                        ?>
                    </div>

                    <!-- Slide-up Form -->
                    <div id="scheduleForm" class="slide-up-form">
                        <div class="form-header">
                            <h2><i class="fas fa-clock"></i> Add New Time Slot</h2>
                           
                        </div>
                        <form onsubmit="saveSchedule(event)">
                            <div class="form-content">
                                <div class="form-row">
                                    <div class="form-group full-width">
                                        <label for="day">
                                            <i class="fas fa-calendar-day"></i> Select Day
                                        </label>
                                        <select name="day" id="day" required class="form-control">
                                            <?php foreach($days as $day): ?>
                                                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row time-inputs">
                                    <div class="form-group">
                                        <label for="start_time">
                                            <i class="fas fa-hourglass-start"></i> Start Time
                                        </label>
                                        <input type="time" id="start_time" name="start_time" required class="form-control">
                                    </div>
                                    <div class="time-separator">to</div>
                                    <div class="form-group">
                                        <label for="end_time">
                                            <i class="fas fa-hourglass-end"></i> End Time
                                        </label>
                                        <input type="time" id="end_time" name="end_time" required class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="closeForm()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i> Save Schedule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <script src="../js/script.js"></script>
    <script>
        function openAddScheduleModal() {
            document.getElementById('scheduleForm').classList.add('active');
        }

        function closeForm() {
            document.getElementById('scheduleForm').classList.remove('active');
        }

        function saveSchedule(e) {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch('save_schedule.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    closeForm();
                    location.reload();
                } else {
                    alert(data.message || 'Error saving schedule');
                }
            });
        }
    </script>
</body>
</html>
