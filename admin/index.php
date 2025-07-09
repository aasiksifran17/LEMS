<?php
session_start();
include('includes/config.php');

if(isset($_POST['signin'])) {
    $uname = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT UserName, Password FROM admin WHERE UserName = :uname AND Password = :password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uname', $uname, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    if($query->rowCount() > 0) {
        $_SESSION['alogin'] = $_POST['username'];
        echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                M.toast({
                    html: 'Invalid username or password',
                    classes: 'red darken-1',
                    displayLength: 4000
                });
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>Admin Login | Employee Leave Management System</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
    <link href="../assets/css/materialdesign.css" rel="stylesheet">
    <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
    
    <!-- Theme Styles -->
    <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
    
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Roboto', sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
            border-top: 3px solid #800000;
        }
        .card .card-content {
            padding: 30px;
        }
        .card-title {
            color: #800000 !important;
            font-weight: 500;
            margin-bottom: 25px !important;
            font-size: 24px !important;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        .card-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #800000;
        }
        .input-field input[type=text]:focus + label,
        .input-field input[type=password]:focus + label {
            color: #800000 !important;
        }
        .input-field input[type=text]:focus,
        .input-field input[type=password]:focus {
            border-bottom: 1px solid #800000 !important;
            box-shadow: 0 1px 0 0 #800000 !important;
        }
        .btn {
            background-color: #800000 !important;
            width: 100%;
            height: 45px;
            line-height: 45px;
            font-size: 16px;
            text-transform: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background-color: #660000 !important;
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            max-width: 120px;
            height: auto;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .login-links {
            margin-top: 20px;
            text-align: center;
        }
        .login-links a {
            color: #800000;
            text-decoration: none;
            font-size: 14px;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        .login-links a:hover {
            color: #660000;
            text-decoration: underline;
        }
        .input-field .prefix {
            color: #800000;
        }
        @media (max-width: 600px) {
            .card {
                margin: 0 15px;
            }
            .login-logo img {
                max-width: 100px;
            }
        }
        /* Loader */
        .loader-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        .spinner-maroon > .circle-clipper > .circle {
            border-color: #800000;
        }
    </style>
</head>
<body>
    <!-- Loader -->
    <div class="loader-bg">
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-maroon">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="login-container">
        <div class="container">
            <div class="row">
                <div class="col s12 m8 l6 offset-m2 offset-l3">
                    <!-- Logo and Title -->
                    <div class="login-logo">
                        <img src="../assets/images/uov_logo.png" alt="University of Vavuniya">
                        <h4 style="color: #800000; margin-top: 15px; font-weight: 500;">Employee Leave Management System</h4>
                        <p style="color: #666; margin-top: 5px;">Admin Portal</p>
                    </div>
                    
                    <!-- Login Card -->
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">Admin Login</span>
                            <p style="color: #666; font-size: 14px; text-align: center; margin-bottom: 25px;">
                                Please enter your credentials to access the admin panel
                            </p>
                            
                            <form name="signin" method="post">
                                <div class="input-field">
                                    <i class="material-icons prefix">person_outline</i>
                                    <input id="username" type="text" name="username" class="validate" autocomplete="off" required>
                                    <label for="username">Username</label>
                                </div>
                                
                                <div class="input-field">
                                    <i class="material-icons prefix">lock_outline</i>
                                    <input id="password" type="password" name="password" class="validate" autocomplete="off" required>
                                    <label for="password">Password</label>
                                </div>
                                
                                <button type="submit" name="signin" class="btn waves-effect waves-light">
                                    Sign In
                                    <i class="material-icons right">arrow_forward</i>
                                </button>
                                
                                <div class="login-links">
                                    <a href="../index.php"><i class="material-icons" style="vertical-align: middle; font-size: 16px;">arrow_back</i> Back to Employee Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="login-footer">
                        &copy; <?php echo date('Y'); ?> University of Vavuniya. All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    
    <script>
        // Initialize components when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize materialize components
            M.AutoInit();
            
            // Hide loader
            var loader = document.querySelector('.loader-bg');
            if (loader) {
                loader.style.display = 'none';
            }
            
            // Show loading spinner on form submission
            var form = document.querySelector('form[name="signin"]');
            if (form) {
                form.addEventListener('submit', function() {
                    var loader = document.querySelector('.loader-bg');
                    if (loader) {
                        loader.style.display = 'flex';
                    }
                });
            }
            
            // Add active class to inputs on focus
            var inputs = document.querySelectorAll('.input-field input');
            inputs.forEach(function(input) {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.parentElement.classList.remove('focused');
                    }
                });
                // Check if input has value on page load
                if (input.value !== '') {
                    input.parentElement.classList.add('focused');
                }
            });
            
            // Auto focus on first input
            var firstInput = document.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Handle back button to prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>