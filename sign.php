<?php
require_once 'db.php';
session_start();

// Ensure no output is sent before header() calls
ob_start();

// Add this function at the top
function generateUniqueId($type, $conn) {
    $prefix = '';
    $table = '';
    
    switch($type) {
        case 'doctor':
            $prefix = 'DT';
            $table = 'doctor';  // Changed from 'doctor' to 'doctors'
            break;
        case 'patient':
            $prefix = 'PT';
            $table = 'patient';
            break;
        case 'medical':
            $prefix = 'MS';
            $table = 'medical_stores';
            break;
    }
    
    // Modified SQL query to handle VARCHAR id
    $sql = "SELECT id FROM $table WHERE id LIKE '$prefix%' ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if ($row) {
        $last_id = intval(substr($row['id'], 2));
        $next_id = $last_id + 1;
    } else {
        $next_id = 1001;
    }
    
    return $prefix . $next_id;
}

// Modify the form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Check if user_type is set
        if (!isset($_POST['user_type']) || empty($_POST['user_type'])) {
            throw new Exception("Please select a user type");
        }

        $user_type = $_POST['user_type'];
        $id = generateUniqueId($user_type, $conn);
        if (!$id) {
            throw new Exception("Error generating ID");
        }

        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        switch($user_type) {
            case 'doctor':
                $sql = "INSERT INTO doctor (id, name, specialization, email, phone, address, experience, qualification, availability, password) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssss",
                    $id,
                    $_POST['name'],
                    $_POST['specialization'],
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['address'],
                    $_POST['experience'],
                    $_POST['qualification'],
                    $_POST['availability'],
                    $password
                );
                break;
                
            case 'patient':
                $stmt = $conn->prepare("INSERT INTO patient (id, name, age, gender, email, phone, address, medical_history, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if(!$stmt) {
                    throw new Exception($conn->error);
                }
                
                // Store values in variables first
                $medical_history = isset($_POST['medical_history']) ? $_POST['medical_history'] : '';
                
                $stmt->bind_param("sssssssss", 
                    $id,
                    $_POST['name'],
                    $_POST['age'],
                    $_POST['gender'],
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['address'],
                    $medical_history,  // Now using the variable
                    $password
                );
                break;
                
            case 'medical':
                $sql = "INSERT INTO medical_stores (id, name, email, phone, address, password) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss",
                    $id,
                    $_POST['name'],
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['address'],
                    $password
                );
                break;
        }

        if(!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $success_message = "Registration successful! Your ID is: " . $id;
        $_SESSION['registration_id'] = $id; // Store ID for display on login page
        header("Location: login.php?registered=1&id=" . $id);
        exit();

    } catch (Exception $e) {
        $error_message = "Registration failed: " . $e->getMessage();
        error_log("Registration error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Sign Up & Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        .form-container {
            padding: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-group {
            position: relative;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group i {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 20px;
        }
        button {
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .tooltip {
            position: relative;
            display: inline-block;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%; /* Position above the tooltip element */
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 400px;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .container {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Sign Up</div>
        <div class="form-container">
            <?php if (isset($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <select id="userType" onchange="changeForm()" required>
                <option value="">Select User Type</option>
                <option value="doctor">Doctor</option>
                <option value="patient">Patient</option>
                <option value="medical">Medical Store</option>
            </select>

            <form id="registrationForm" method="post" action="">
                <input type="hidden" name="user_type" id="user_type">
                <div id="formFields"></div>
                <button type="submit">Register</button>
            </form>
            <button onclick="openLoginModal()">Already have an account? Sign In</button>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <h2>Sign In</h2>
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" name="user_id" placeholder="User ID" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit">Sign In</button>
            </form>
        </div>
    </div>

    <script>
        function changeForm() {
            const userType = document.getElementById('userType').value;
            document.getElementById('user_type').value = userType;
            const formFields = document.getElementById('formFields');

            // Clear the form fields if no user type is selected
            if (!userType) {
                formFields.innerHTML = '<p>Please select a user type to see the form fields.</p>';
                return;
            }

            let fields = '';

            switch (userType) {
                case 'doctor':
                    fields = `
                        <div class="form-group">
                            <i class="fas fa-user-md form-icon"></i>
                            <input type="text" name="name" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-stethoscope form-icon"></i>
                            <input type="text" name="specialization" placeholder="Specialization" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-envelope form-icon"></i>
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-phone form-icon"></i>
                            <input type="tel" name="phone" placeholder="Phone" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-map-marker-alt form-icon"></i>
                            <textarea name="address" placeholder="Address" required></textarea>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-briefcase form-icon"></i>
                            <input type="number" name="experience" placeholder="Experience (years)" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-graduation-cap form-icon"></i>
                            <input type="text" name="qualification" placeholder="Qualification" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-lock form-icon"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                    `;
                    break;

                case 'patient':
                    fields = `
                        <div class="form-group">
                            <i class="fas fa-user form-icon"></i>
                            <input type="text" name="name" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-birthday-cake form-icon"></i>
                            <input type="number" name="age" placeholder="Age" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-venus-mars form-icon"></i>
                            <select name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-envelope form-icon"></i>
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-phone form-icon"></i>
                            <input type="tel" name="phone" placeholder="Phone" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-map-marker-alt form-icon"></i>
                            <textarea name="address" placeholder="Address" required></textarea>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-notes-medical form-icon"></i>
                            <textarea name="medical_history" placeholder="Medical History"></textarea>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-lock form-icon"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                    `;
                    break;

                case 'medical':
                    fields = `
                        <div class="form-group">
                            <i class="fas fa-store form-icon"></i>
                            <input type="text" name="name" placeholder="Store Name" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-envelope form-icon"></i>
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-phone form-icon"></i>
                            <input type="tel" name="phone" placeholder="Phone" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-map-marker-alt form-icon"></i>
                            <textarea name="address" placeholder="Address" required></textarea>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-lock form-icon"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                    `;
                    break;

                default:
                    fields = '<p>Please select a user type to see the form fields.</p>';
            }

            formFields.innerHTML = fields;
        }

        function openLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function (event) {
            const modal = document.getElementById('loginModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // Updated table names to be consistent
    $tables = ['doctor', 'patient', 'medical_stores'];
    $found = false;

    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()) {
            if(password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['name'] = $row['name'];
                
                // Determine directory and type based on ID prefix
                $id_prefix = substr($row['id'], 0, 2);
                switch($id_prefix) {
                    case 'DT':
                        $directory = 'doctor';
                        $user_type = 'doctor';
                        break;
                    case 'PT':
                        $directory = 'patient';
                        $user_type = 'patient';
                        break;
                    case 'MS':
                        $directory = 'mstore';
                        $user_type = 'medical';
                        break;
                }
                
                $_SESSION['user_type'] = $user_type;
                header("Location: {$directory}/{$directory}.php");  
                exit();
            }
            $found = true;
            break;
        }
    }
    $error = "Invalid credentials";
}
?>

</body>
</html>