<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$page_name = 'medical-history';
$patient_id = $_SESSION['user_id'];

// Fetch medical history
$sql = "SELECT * FROM medical_history WHERE patient_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        <div class="main-content">
         
            <div class="container">
                <h2>Medical History</h2>
                <div class="medical-history-list">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <div class="history-card">
                        <div class="history-header">
                            <h3><?php echo htmlspecialchars($row['condition_name']); ?></h3>
                            <span class="date"><?php echo date('d M Y', strtotime($row['date'])); ?></span>
                        </div>
                        <div class="history-details">
                            <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($row['diagnosis']); ?></p>
                            <p><strong>Treatment:</strong> <?php echo htmlspecialchars($row['treatment']); ?></p>
                            <p><strong>Notes:</strong> <?php echo htmlspecialchars($row['notes']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>
