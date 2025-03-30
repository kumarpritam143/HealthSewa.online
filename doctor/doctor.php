<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard - healthsewa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <li class="active"><a href="doctor.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="patients.php"><i class="fas fa-users"></i> Patients List</a></li>
                <li><a href="schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <?php 
            $page_title = 'Doctor Dashboard';
            include '../includes/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <h2>Welcome back, Dr. <?php echo $_SESSION['name']; ?>!</h2>
                    <p>Here's your daily overview</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-cards">
                    <?php
                    $doctor_id = $_SESSION['user_id'];
                    $today = date('Y-m-d');
                    
                    // Simple count query for appointments
                    $today_query = mysqli_query($conn, "SELECT COUNT(*) as today_count FROM appointment WHERE doctor_id='$doctor_id' AND appointment_date='$today'");
                    $total_query = mysqli_query($conn, "SELECT COUNT(*) as total_count FROM appointment WHERE doctor_id='$doctor_id'");
                    
                    $today_count = mysqli_fetch_assoc($today_query)['today_count'];
                    $total_count = mysqli_fetch_assoc($total_query)['total_count'];
                    ?>
                    <div class="card">
                        <div class="card-inner">
                            <h3>Today's Appointments</h3>
                            <i class="fas fa-calendar-day"></i>
                            <h2><?php echo $today_count; ?></h2>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-inner">
                            <h3>Total Appointments</h3>
                            <i class="fas fa-calendar-check"></i>
                            <h2><?php echo $total_count; ?></h2>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-inner">
                            <h3>Patient Satisfaction</h3>
                            <i class="fas fa-smile"></i>
                            <h2>4.8/5</h2>
                            <p>Based on 45 reviews</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <button onclick="location.href='appointments.php?action=new'">
                            <i class="fas fa-plus"></i> All New Appointment
                        </button>
                        <button onclick="location.href='patients.php?action=new'">
                            <i class="fas fa-user-plus"></i> Add Patient Prescription
                        </button>
                        <button onclick="location.href='schedule.php'">
                            <i class="fas fa-calendar-alt"></i> View Schedule
                        </button>
                        
                    </div>
                </div>

                <div class="dashboard-grid">
                    <!-- Upcoming Appointments -->
                    <div class="dashboard-card upcoming-appointments">
                        <h3>Today's Appointments</h3>
                        <div class="appointment-list">
                            <div class="appointment-item">
                                <div class="time">09:00 AM</div>
                                <div class="details">
                                    <h4>Rahul Sharma</h4>
                                    <p>General Checkup</p>
                                </div>
                                <div class="status pending">Pending</div>
                            </div>
                            <div class="appointment-item">
                                <div class="time">10:30 AM</div>
                                <div class="details">
                                    <h4>Priya Patel</h4>
                                    <p>Follow-up</p>
                                </div>
                                <div class="status confirmed">Confirmed</div>
                            </div>
                            <div class="appointment-item">
                                <div class="time">02:00 PM</div>
                                <div class="details">
                                    <h4>Amit Kumar</h4>
                                    <p>Consultation</p>
                                </div>
                                <div class="status confirmed">Confirmed</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Patients -->
                    <div class="dashboard-card recent-patients">
                        <h3>Recent Patients</h3>
                        <div class="patient-list">
                            <div class="patient-item">
                                <div class="patient-info">
                                    <h4>Neha Singh</h4>
                                    <p>Last Visit: Yesterday</p>
                                </div>
                                <button onclick="location.href='patient-details.php?id=1'">View</button>
                            </div>
                            <div class="patient-item">
                                <div class="patient-info">
                                    <h4>Raj Malhotra</h4>
                                    <p>Last Visit: 3 days ago</p>
                                </div>
                                <button onclick="location.href='patient-details.php?id=2'">View</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts">
                    <div class="chart">
                        <h3>Weekly Appointments</h3>
                        <canvas id="appointmentsChart" style="max-height: 250px;"></canvas>
                    </div>
                    <div class="chart">
                        <h3>Patient Demographics</h3>
                        <canvas id="demographicsChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

           
        </div>
    </div>

    <script>
        // Enhanced Charts Configuration
        const appointmentsChart = new Chart(document.getElementById('appointmentsChart'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Appointments',
                    data: [12, 19, 15, 8, 14, 10, 7],
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2196F3',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        bodyFont: {
                            size: 14
                        },
                        titleFont: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const demographicsChart = new Chart(document.getElementById('demographicsChart'), {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female', 'Other'],
                datasets: [{
                    data: [55, 40, 5],
                    backgroundColor: [
                        'rgba(33, 150, 243, 0.8)',
                        'rgba(233, 30, 99, 0.8)',
                        'rgba(156, 39, 176, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                cutout: '70%',
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        // Add loading animation
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });
    </script>

    <style>
        /* Add loading animation styles */
        body {
            opacity: 0;
            transition: opacity 0.5s;
        }
        
        body.loaded {
            opacity: 1;
        }
        
        .card, .dashboard-card {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
    </style>
    <script src="../js/script.js"></script>
</body>
</html>
