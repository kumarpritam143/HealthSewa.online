<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'medical') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Store Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../doctor/css/style.css">
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3><?php echo $_SESSION['name']; ?></h3>
                <p>ID: <?php echo $_SESSION['user_id']; ?></p>
            </div>
            <ul class="sidebar-menu">
                <li class="active"><a href="mstore.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <?php 
            $page_title = 'Medical Store Dashboard';
            include '../includes/header.php'; 
            ?>
            <!-- Add your medical store dashboard content here -->
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>
