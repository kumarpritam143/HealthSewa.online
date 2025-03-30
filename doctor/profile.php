<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $availability = $_POST['availability'];

    $update_stmt = $conn->prepare("UPDATE doctor SET email=?, phone=?, address=?, qualification=?, experience=?, availability=? WHERE id=?");
    $update_stmt->bind_param("sssssss", $email, $phone, $address, $qualification, $experience, $availability, $_SESSION['user_id']);
    
    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}

// Fetch doctor's details
$stmt = $conn->prepare("SELECT * FROM doctor WHERE id = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            color: white;
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .profile-image {
            position: relative;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 5px solid white;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .profile-image:hover img {
            transform: scale(1.1);
        }

        .doctor-info {
            flex: 1;
        }

        .doctor-info h2 {
            font-size: 2em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .doctor-info p {
            font-size: 1.1em;
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .doctor-info i {
            width: 24px;
        }

        .profile-tabs {
            padding: 30px;
        }

        .tab-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .tab-btn {
            padding: 12px 25px;
            font-size: 1.1em;
            border: none;
            background: none;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .tab-btn:after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 100%;
            height: 3px;
            background: #00b4db;
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .tab-btn.active {
            color: #00b4db;
        }

        .tab-btn.active:after {
            transform: scaleX(1);
        }

        .profile-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .profile-table tr {
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        .profile-table tr:hover {
            background: #f8f9fa;
        }

        .profile-table th,
        .profile-table td {
            padding: 15px;
            text-align: left;
        }

        .profile-table th {
            width: 200px;
            color: #666;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            color: #555;
            font-size: 0.95em;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 1em;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #00b4db;
            box-shadow: 0 0 0 3px rgba(0,180,219,0.1);
            outline: none;
        }

        .btn-update {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1.1em;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,180,219,0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert i {
            font-size: 1.2em;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #721c24;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-image {
                margin: 0 auto;
            }
            
            .tab-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .profile-table th,
            .profile-table td {
                display: block;
                width: 100%;
            }
        }

        /* Card Styles */
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .card-header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            border-bottom: 2px solid #eee;
        }

        .card-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.4em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 25px;
        }

        /* Tab Navigation */
        .profile-nav {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-link {
            flex: 1;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .nav-link.active {
            border-color: #00b4db;
            background: #f8f9fa;
        }

        .nav-link i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #00b4db;
        }

        .nav-link span {
            display: block;
            font-weight: 500;
        }

        /* View Profile Section */
        .profile-info-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            width: 200px;
            font-weight: 600;
            color: #2c3e50;
        }

        .info-value {
            flex: 1;
            color: #34495e;
        }

        /* Edit Profile Section */
        .edit-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-nav {
                flex-direction: column;
            }
            .edit-form {
                grid-template-columns: 1fr;
            }
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
                <li><a href="patients.php"><i class="fas fa-users"></i> Patients</a></li>
                <li><a href="schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="disease.php"><i class="fas fa-file-medical"></i>Upload Report Disease</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <?php 
            $page_title = 'My Profile';
            include '../includes/header.php'; 
            ?>

            <div class="dashboard-content">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="profile-container">
                    <div class="profile-header">
                        <div class="profile-image">
                            <img src="../assets/images/default-avatar.png" alt="Doctor Profile">
                        </div>
                        <div class="doctor-info">
                            <h2>Dr. <?php echo htmlspecialchars($doctor['name']); ?></h2>
                            <p><i class="fas fa-user-md"></i> <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                            <p><i class="fas fa-id-card"></i> ID: <?php echo htmlspecialchars($doctor['id']); ?></p>
                            <p><i class="fas fa-clock"></i> Available: <?php echo htmlspecialchars($doctor['availability']); ?></p>
                        </div>
                    </div>

                    <div class="profile-tabs">
                        <div class="profile-nav">
                            <div class="nav-link active" onclick="showTab('view')">
                                <i class="fas fa-user-circle"></i>
                                <span>View Profile</span>
                            </div>
                            <div class="nav-link" onclick="showTab('edit')">
                                <i class="fas fa-edit"></i>
                                <span>Edit Profile</span>
                            </div>
                        </div>

                        <div id="view" class="tab-content active">
                            <div class="profile-card">
                                <div class="card-header">
                                    <h3><i class="fas fa-info-circle"></i> Personal Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="profile-info-item">
                                        <div class="info-label">Name</div>
                                        <div class="info-value">Dr. <?php echo htmlspecialchars($doctor['name']); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="info-label">Specialization</div>
                                        <div class="info-value"><?php echo htmlspecialchars($doctor['specialization']); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="info-label">Email</div>
                                        <div class="info-value"><?php echo htmlspecialchars($doctor['email']); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="info-label">Phone</div>
                                        <div class="info-value"><?php echo htmlspecialchars($doctor['phone']); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="info-label">Address</div>
                                        <div class="info-value"><?php echo htmlspecialchars($doctor['address']); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="info-label">Experience</div>
                                        <div class="info-value"><?php echo htmlspecialchars($doctor['experience']); ?> years</div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="info-label">Qualification</div>
                                        <div class="info-value"><?php echo htmlspecialchars($doctor['qualification']); ?></div>
                                    </div>
                                    <div class="profile-info-item">
                                        <div class="info-label">Availability</div>
                                        <div class="info-value"><?php echo htmlspecialchars($doctor['availability']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="edit" class="tab-content">
                            <div class="profile-card">
                                <div class="card-header">
                                    <h3><i class="fas fa-user-edit"></i> Edit Profile</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post" class="edit-form">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address" required><?php echo htmlspecialchars($doctor['address']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Qualification</label>
                                            <input type="text" name="qualification" value="<?php echo htmlspecialchars($doctor['qualification']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Experience (Years)</label>
                                            <input type="number" name="experience" value="<?php echo htmlspecialchars($doctor['experience']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Availability</label>
                                            <input type="text" name="availability" value="<?php echo htmlspecialchars($doctor['availability']); ?>" placeholder="e.g. 9-5" required>
                                        </div>
                                        <div class="form-group full-width">
                                            <button type="submit" class="btn-update">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include '../includes/footer.php'; ?>
        </div>
    </div>
    <script src="../js/script.js"></script>
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Add smooth scrolling to form
        document.querySelector('.tab-btn:nth-child(2)').addEventListener('click', () => {
            setTimeout(() => {
                document.querySelector('.profile-form').scrollIntoView({ behavior: 'smooth' });
            }, 100);
        });

        // Add form validation
        document.querySelector('.profile-form').addEventListener('submit', function(e) {
            const phone = this.querySelector('[name="phone"]').value;
            const email = this.querySelector('[name="email"]').value;
            
            if (!phone.match(/^\d{10}$/)) {
                e.preventDefault();
                alert('Please enter a valid 10-digit phone number');
            }
            
            if (!email.match(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/)) {
                e.preventDefault();
                alert('Please enter a valid email address');
            }
        });

        // Add input masking for phone
        const phoneInput = document.querySelector('[name="phone"]');
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substr(0, 10);
        });
    </script>
</body> 
</html>
