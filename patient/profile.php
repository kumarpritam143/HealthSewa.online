<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$page_name = 'profile';
$patient_id = $_SESSION['user_id'];

// Fetch patient details
$sql = "SELECT * FROM patient WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

// Add edit mode flag
$edit_mode = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $edit_mode) {
    try {
        // First verify if email already exists for another user
        $check_email = $conn->prepare("SELECT id FROM patient WHERE email = ? AND id != ?");
        $check_email->bind_param("ss", $_POST['email'], $patient_id);
        $check_email->execute();
        $email_result = $check_email->get_result();
        
        if($email_result->num_rows > 0) {
            throw new Exception("Email already exists!");
        }

        // Update all fields in database
        $update_sql = "UPDATE patient SET 
            name = ?, 
            email = ?, 
            phone = ?, 
            address = ?, 
            age = ?,
            gender = ?
            WHERE id = ?";
            
        $update_stmt = $conn->prepare($update_sql);
        if(!$update_stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $update_stmt->bind_param("sssssss", 
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['age'],
            $_POST['gender'],
            $patient_id
        );
        
        if($update_stmt->execute()) {
            // Update session data
            $_SESSION['name'] = $_POST['name'];
            
            // Refetch updated patient data
            $stmt = $conn->prepare("SELECT * FROM patient WHERE id = ?");
            $stmt->bind_param("s", $patient_id);
            $stmt->execute();
            $patient = $stmt->get_result()->fetch_assoc();
            
            $success_message = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
        } else {
            throw new Exception("Error updating profile");
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .profile-container {
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        .profile-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: #2c71d3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        .profile-avatar i {
            font-size: 40px;
            color: white;
        }
        .patient-id {
            background: #e3f2fd;
            color: #2c71d3;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            margin-top: 10px;
        }
        .profile-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            color: #444;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            border: 1px solid #dce1e6;
            border-radius: 8px;
            padding: 12px 15px;
            width: 100%;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #2c71d3;
            outline: none;
        }
        .update-btn {
            background: #2c71d3;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }
        .update-btn:hover {
            background: #1b5bb4;
        }
        .readonly-field {
            background: #f8f9fa;
            cursor: not-allowed;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        
        .edit-btn, .update-btn, .cancel-btn {
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            margin: 0 10px;
        }
        
        .edit-btn {
            background: #2c71d3;
            color: white;
        }
        
        .cancel-btn {
            background: #dc3545;
            color: white;
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/header.php'; ?>
            
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($patient['name']); ?></h2>
                    <div class="patient-id">ID: <?php echo htmlspecialchars($patient['id']); ?></div>
                </div>

                <?php if(isset($success_message)): ?>
                    <div class="alert alert-success mb-4"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form method="POST" class="profile-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" 
                                    value="<?php echo htmlspecialchars($patient['name']); ?>" 
                                    <?php echo !$edit_mode ? 'readonly' : ''; ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Age</label>
                                <input type="number" name="age" class="form-control" 
                                    value="<?php echo htmlspecialchars($patient['age']); ?>" 
                                    <?php echo !$edit_mode ? 'readonly' : ''; ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gender</label>
                                <?php if($edit_mode): ?>
                                    <select name="gender" class="form-control" required>
                                        <option value="male" <?php echo $patient['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo $patient['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo $patient['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($patient['gender']); ?>" readonly>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                    value="<?php echo htmlspecialchars($patient['email']); ?>" 
                                    <?php echo !$edit_mode ? 'readonly' : ''; ?> required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" class="form-control" 
                                    value="<?php echo htmlspecialchars($patient['phone']); ?>" 
                                    <?php echo !$edit_mode ? 'readonly' : ''; ?> required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3" 
                                    <?php echo !$edit_mode ? 'readonly' : ''; ?> 
                                    required><?php echo htmlspecialchars($patient['address']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <?php if($edit_mode): ?>
                            <button type="submit" class="update-btn">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="profile.php" class="cancel-btn">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php else: ?>
                            <a href="profile.php?edit=true" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include '../includes/chatbot_button.php'; ?>
    <script src="../js/script.js"></script>
</body>
</html>
