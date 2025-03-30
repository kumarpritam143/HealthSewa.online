<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$page_name = 'dashboard';

// Get all unique specializations for filter
$spec_sql = "SELECT DISTINCT specialization FROM doctor ORDER BY specialization";
$spec_result = $conn->query($spec_sql);
$specializations = [];
while($row = $spec_result->fetch_assoc()) {
    $specializations[] = $row['specialization'];
}

// Handle filter
$filter = isset($_GET['specialization']) ? $_GET['specialization'] : '';
$sql = "SELECT * FROM doctor";
if(!empty($filter)) {
    $sql .= " WHERE specialization = '" . $conn->real_escape_string($filter) . "'";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .doctor-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.2s;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .doctor-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }

        .doctor-avatar {
            width: 60px;
            height: 60px;
            background: #2c71d3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .doctor-info h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }

        .doctor-info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        .doctor-details {
            margin-top: 15px;
        }

        .detail-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            color: #444;
            font-size: 14px;
        }

        .detail-item i {
            color: #2c71d3;
            width: 20px;
        }

        .badge {
            background: #e3f2fd;
            color: #2c71d3;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .filter-section {
            padding: 20px;
            background: white;
            border-bottom: 1px solid #eee;
        }
        
        .filter-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            min-width: 200px;
            color: #333;
        }
        
        .filter-label {
            font-weight: 500;
            color: #444;
        }
        
        .clear-filter {
            color: #2c71d3;
            text-decoration: none;
            font-size: 14px;
            margin-left: 10px;
        }
        
        .clear-filter:hover {
            text-decoration: underline;
        }
        
        .book-appointment-btn {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background: #2c71d3;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }

        .book-appointment-btn:hover {
            background: #1b5bb4;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        <div class="main-content">
       
            
            <!-- Add Filter Section -->
            <div class="filter-section">
                <div class="filter-container">
                    <span class="filter-label">Filter by Specialization:</span>
                    <select class="filter-select" onchange="window.location.href='?specialization='+this.value">
                        <option value="">All Specializations</option>
                        <?php foreach($specializations as $spec): ?>
                            <option value="<?php echo htmlspecialchars($spec); ?>" 
                                <?php echo ($filter === $spec) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($spec); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(!empty($filter)): ?>
                        <a href="?" class="clear-filter">Clear Filter</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="doctor-grid">
                <?php while($doctor = $result->fetch_assoc()): ?>
                    <div class="doctor-card">
                        <div class="doctor-header">
                            <div class="doctor-avatar">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="doctor-info">
                                <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
                                <p><span class="badge"><?php echo htmlspecialchars($doctor['specialization']); ?></span></p>
                            </div>
                        </div>
                        <div class="doctor-details">
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <?php echo htmlspecialchars($doctor['email']); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($doctor['phone']); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($doctor['address']); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                Experience: <?php echo htmlspecialchars($doctor['experience']); ?> years
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-graduation-cap"></i>
                                <?php echo htmlspecialchars($doctor['qualification']); ?>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-check"></i>
                                <?php echo htmlspecialchars($doctor['availability']); ?>
                            </div>
                            <!-- Add Book Appointment Button -->
                            <a href="book_appointment.php?doctor_id=<?php echo htmlspecialchars($doctor['id']); ?>" 
                               class="book-appointment-btn">
                                <i class="fas fa-calendar-plus"></i>
                                Book Appointment
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php include '../includes/chatbot_button.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>
