<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

if(isset($_POST['submit'])) {
    $doctor_id = $_SESSION['user_id'];
    $slot_date = $_POST['slot_date'];
    $slot1 = $_POST['slot1'];
    $slot2 = $_POST['slot2'];
    $slot3 = $_POST['slot3'];
    $slot4 = $_POST['slot4'];

    $query = "INSERT INTO doctor_slots (doctor_id, slot_date, slot1, slot2, slot3, slot4) 
              VALUES ('$doctor_id', '$slot_date', '$slot1', '$slot2', '$slot3', '$slot4')";
    mysqli_query($con, $query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Slots - healthsewa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .dashboard-content .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .slot-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        .slot-form .form-group {
            margin-bottom: 15px;
        }
        .slot-form label {
            font-weight: 600;
            color: #333;
        }
        .slot-form input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        .slot-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .slot-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        .slot-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .slot-time {
            background: #e3f2fd;
            padding: 5px 10px;
            border-radius: 4px;
            color: #1976d2;
            font-size: 14px;
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
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
                <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="patients.php"><i class="fas fa-users"></i> Patients</a></li>
                <li><a href="schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                <li class="active"><a href="slot.php"><i class="fas fa-clock"></i> Manage Slots</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-cards">
                    <div class="card">
                        <div class="card-inner">
                            <h3>Total Slots</h3>
                            <i class="fas fa-clock"></i>
                            <?php
                            $doctor_id = $_SESSION['user_id'];
                            $count_query = "SELECT COUNT(*) as total FROM doctor_slots WHERE doctor_id='" . mysqli_real_escape_string($con, $doctor_id) . "'";
                            $count_result = mysqli_query($con, $count_query);
                            if($count_result) {
                                $count = mysqli_fetch_assoc($count_result)['total'];
                            } else {
                                $count = 0;
                            }
                            ?>
                            <h2><?php echo $count; ?></h2>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-inner">
                            <h3>Today's Slots</h3>
                            <i class="fas fa-calendar-day"></i>
                            <?php
                            $today_query = "SELECT COUNT(*) as today FROM doctor_slots 
                                           WHERE doctor_id='" . mysqli_real_escape_string($con, $doctor_id) . "' 
                                           AND slot_date=CURDATE()";
                            $today_result = mysqli_query($con, $today_query);
                            if($today_result) {
                                $today = mysqli_fetch_assoc($today_result)['today'];
                            } else {
                                $today = 0;
                            }
                            ?>
                            <h2><?php echo $today; ?></h2>
                        </div>
                    </div>
                </div>

                <!-- Slot Form -->
                <div class="card slot-form">
                    <h3><i class="fas fa-plus-circle"></i> Add New Slots</h3>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Date</label>
                                    <input type="date" name="slot_date" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Morning Slot</label>
                                    <input type="time" name="slot1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Afternoon Slot</label>
                                    <input type="time" name="slot2">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Evening Slot</label>
                                    <input type="time" name="slot3">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Night Slot</label>
                                    <input type="time" name="slot4">
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn-action btn-primary">
                            <i class="fas fa-save"></i> Save Slots
                        </button>
                    </form>
                </div>

                <!-- Slots Table -->
                <div class="card">
                    <h3><i class="fas fa-list"></i> My Scheduled Slots</h3>
                    <div class="table-responsive">
                        <table class="slot-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Morning</th>
                                    <th>Afternoon</th>
                                    <th>Evening</th>
                                    <th>Night</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $doctor_id = $_SESSION['user_id'];
                                $query = "SELECT * FROM doctor_slots WHERE doctor_id='$doctor_id' ORDER BY slot_date DESC";
                                $result = mysqli_query($con, $query);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . date('d M Y', strtotime($row['slot_date'])) . "</td>";
                                    echo "<td><span class='slot-time'>" . date('h:i A', strtotime($row['slot1'])) . "</span></td>";
                                    echo "<td><span class='slot-time'>" . date('h:i A', strtotime($row['slot2'])) . "</span></td>";
                                    echo "<td><span class='slot-time'>" . date('h:i A', strtotime($row['slot3'])) . "</span></td>";
                                    echo "<td><span class='slot-time'>" . date('h:i A', strtotime($row['slot4'])) . "</span></td>";
                                    echo "<td>
                                            <button class='btn-action btn-danger' onclick='deleteSlot(".$row['id'].")'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                          </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>