<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        <!-- Meta Tags -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="keywords" content="Site keywords here">
		<meta name="description" content="">
		<meta name='copyright' content=''>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Title -->
        <title>Healthsewa</title>
		
		<!-- Favicon -->
        <link rel="icon" href="img/favicon.png">
		
		<!-- Google Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<!-- Nice Select CSS -->
		<link rel="stylesheet" href="css/nice-select.css">
		<!-- Font Awesome CSS -->
        <link rel="stylesheet" href="css/font-awesome.min.css">
		<!-- icofont CSS -->
        <link rel="stylesheet" href="css/icofont.css">
		<!-- Slicknav -->
		<link rel="stylesheet" href="css/slicknav.min.css">
		<!-- Owl Carousel CSS -->
        <link rel="stylesheet" href="css/owl-carousel.css">
		<!-- Datepicker CSS -->
		<link rel="stylesheet" href="css/datepicker.css">
		<!-- Animate CSS -->
        <link rel="stylesheet" href="css/animate.min.css">
		<!-- Magnific Popup CSS -->
        <link rel="stylesheet" href="css/magnific-popup.css">
		
		<!-- Medipro CSS -->
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="css/responsive.css">
		
		<!-- Color CSS -->
		<link rel="stylesheet" href="css/color/color1.css">


		<link rel="stylesheet" href="#" id="colors">
        <!-- Font Awesome CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
		
    </head>
    <body>
    <script src="https://botmint-ai.onrender.com/embed.js" data-bot-id="67e7c98e2b03616bc8bb5f71"></script>

		<!-- Preloader -->
        <div class="preloader">
            <div class="loader">
                <div class="loader-outter"></div>
                <div class="loader-inner"></div>

                <div class="indicator"> 
                    <svg width="16px" height="12px">
                        <polyline id="back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                        <polyline id="front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline>
                    </svg>
                </div>
            </div>
        </div>
        <!-- End Preloader -->
		

	
    <!-- Header Area -->
    <header class="header">
        <!-- Topbar -->
        <div class="topbar">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-5 col-12">
                        <!-- Contact -->
                        <ul class="top-link">
                            <li><a href="about.php">About</a></li>
                            <li><a href="doctors.php">Doctors</a></li>
                            <li><a href="contact.php">Contact</a></li>
                            <li><a href="patient.php">Patients Location</a></li>
                        </ul>
                        <!-- End Contact -->
                    </div>
                    <div class="col-lg-6 col-md-7 col-12">
                        <!-- Top Contact -->
                        <ul class="top-contact">
                            <li><i class="fa fa-phone"></i>+91 8809114486</li>
                            <li><i class="fa fa-envelope"></i><a>info@healthsewa.online</span></a></li>
                            </ul>
                        <!-- End Top Contact -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Topbar -->
        <!-- Header Inner -->
        <div class="header-inner">
            <div class="container">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-12">
                            <!-- Start Logo -->
                            <div class="logo">
                                <a href="index.php"><img src="img/healthlogo.png" alt="#"></a>
                            </div>
                            <!-- End Logo -->
                            <!-- Mobile Nav -->
                            <div class="mobile-nav"></div>
                            <!-- End Mobile Nav -->
                        </div>
                        <div class="col-lg-7 col-md-9 col-12">
                            <!-- Main Menu -->
                            <div class="main-menu">
                                <nav class="navigation">
                                    <ul class="nav menu">
                                        <li class="active"><a href="#">Home </a>
                                        </li>
                                        <li><a href="#">Doctos <i class="icofont-rounded-down"></i></a>
                                            <ul class="dropdown">
                                                <li><a href="doctors.php">Doctor</a></li>
                                                <li><a href="doctor-details.php">Doctor Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Services <i class="icofont-rounded-down"></i></a>
                                            <ul class="dropdown">
                                                <li><a href="service.php">Service</a></li>
                                                <li><a href="service-details.php">Service Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Pages <i class="icofont-rounded-down"></i></a>
                                            <ul class="dropdown">
                                                <li><a href="about.php">About Us</a></li>
                                                <li><a href="appointment.php">Appointment</a></li>
                                                <li><a href="time-table.php">Time Table</a></li>
                                                <li><a href="testimonials.php">Testimonials</a></li>

                                            </ul>
                                        </li>
                                        <li><a href="blog.php">Blogs</a>

                                        </li>
                                        <li><a href="contact.php">Contact Us</a></li>
                                    </ul>
                                </nav>
                            </div>
                            <!--/ End Main Menu -->
                        </div>
                        <div class="col-lg-2 col-12">
                            <div class="get-quote">
                                <button class="btn" onclick="openSignModal()">Sign In/Sign Up</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ End Header Inner -->
    </header>
    <!-- End Header Area -->

    <!-- Sign In/Sign Up Modal -->
    <div id="signModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSignModal()">&times;</span>
            <div class="modal-header">
                <button id="signInTab" class="tab-button active" onclick="showTab('signIn')">Sign In</button>
                <button id="signUpTab" class="tab-button" onclick="showTab('signUp')">Sign Up</button>
            </div>
            <div class="modal-body">
                <!-- Sign In Form -->
                <div id="signIn" class="tab-content active">
                    <h2><i class="fas fa-sign-in-alt"></i> Sign In</h2>
                    <form method="post" action="sign.php">
                        <div class="form-group">
                            <i class="fas fa-user form-icon"></i>
                            <input type="text" name="user_id" placeholder="User ID" required>
                        </div>
                        <div class="form-group">
                            <i class="fas fa-lock form-icon"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit"><i class="fas fa-arrow-right"></i> Sign In</button>
                    </form>
                </div>
                <!-- Sign Up Form -->
                <div id="signUp" class="tab-content">
                    <h2><i class="fas fa-user-plus"></i> Sign Up</h2>
                    <select id="userType" onchange="changeForm()" required>
                        <option value="">Select User Type</option>
                        <option value="doctor">Doctor</option>
                        <option value="patient">Patient</option>
                        <option value="medical">Medical Store</option>
                    </select>
                    <form id="registrationForm" method="post" action="">
                        <input type="hidden" name="user_type" id="user_type">
                        <div id="formFields"></div>
                        <button type="submit"><i class="fas fa-user-check"></i> Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
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
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #aaa;
        }
        .close:hover {
            color: #000;
        }
        .modal-header {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .tab-button {
            padding: 10px 20px;
            border: none;
            background: #f4f4f4;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
            border-radius: 5px;
            margin: 0 5px;
        }
        .tab-button.active {
            background: #4CAF50;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .form-group {
            position: relative;
            margin-bottom: 15px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group .form-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 20px;
        }
        button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        button:hover {
            background: #45a049;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
    </style>

    <script>
        function openSignModal() {
            document.getElementById('signModal').style.display = 'block';
        }

        function closeSignModal() {
            document.getElementById('signModal').style.display = 'none';
        }

        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-button');
            tabs.forEach(tab => tab.classList.remove('active'));
            buttons.forEach(button => button.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.getElementById(tabId + 'Tab').classList.add('active');
        }

        function changeForm() {
            const userType = document.getElementById('userType').value;
            document.getElementById('user_type').value = userType;
            const formFields = document.getElementById('formFields');

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
            }

            formFields.innerHTML = fields;
        }
    </script>
</body>
</html>