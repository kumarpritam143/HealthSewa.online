<nav class="sidebar">
    <div class="sidebar-header">
        <h3><?php echo $_SESSION['name']; ?></h3>
        <p>ID: <?php echo $_SESSION['user_id']; ?></p>
    </div>
    <ul class="sidebar-menu">
        <li class="<?php echo ($page_name === 'dashboard') ? 'active' : ''; ?>">
            <a href="patient.php"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li class="<?php echo ($page_name === 'appointments') ? 'active' : ''; ?>">
            <a href="appointments.php"><i class="fas fa-calendar"></i> My Appointments</a>
        </li>
        <li class="<?php echo ($page_name === 'prescriptions') ? 'active' : ''; ?>">
            <a href="prescriptions.php"><i class="fas fa-prescription"></i> Prescriptions</a>
        </li>
        <li class="<?php echo ($page_name === 'medical-history') ? 'active' : ''; ?>">
            <a href="medical-history.php"><i class="fas fa-history"></i> Medical History</a>
        </li>
        <li class="<?php echo ($page_name === 'profile') ? 'active' : ''; ?>">
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        </li>
        <!-- <li class="<?php echo ($page_name === 'chatbot') ? 'active' : ''; ?>">
            <a href="chatbot.php"><i class="fas fa-robot"></i> Chat Assistant</a>
        </li> -->
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>
