
<?php
session_start();
error_reporting(0);
include('includes/config.php');
// Code for change password 
if(isset($_POST['change']))
    {
$newpassword=md5($_POST['newpassword']);
$empid=$_SESSION['empid'];

$con="update tblemployees set Password=:newpassword where id=:empid";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':empid', $empid, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();
$msg="Your Password succesfully changed";
}

?><!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>ELMS | Password Recovery</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Employee Leave Management System - University of Vavuniya" />
        <meta name="keywords" content="password recovery, employee portal, university of vavuniya" />
        <meta name="author" content="University of Vavuniya" />
        
        <!-- Favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
        <link href="assets/css/materialdesign.css" rel="stylesheet">
        <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
        
        <!-- Theme Styles -->
        <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
        
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
            .input-field input[type=password]:focus + label,
            .input-field input[type=email]:focus + label {
                color: #800000 !important;
            }
            .input-field input[type=text]:focus,
            .input-field input[type=password]:focus,
            .input-field input[type=email]:focus {
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
            .errorWrap {
                background: #ffebee;
                color: #c62828;
                padding: 12px 15px;
                border-radius: 4px;
                margin-bottom: 20px;
                font-size: 14px;
                border-left: 4px solid #c62828;
                display: flex;
                align-items: center;
            }
            .succWrap {
                background: #e8f5e9;
                color: #1b5e20;
                padding: 12px 15px;
                border-radius: 4px;
                margin-bottom: 20px;
                font-size: 14px;
                border-left: 4px solid #4caf50;
                display: flex;
                align-items: center;
            }
            .errorWrap i, .succWrap i {
                margin-right: 10px;
                font-size: 20px;
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
                            <img src="assets/images/uov_logo.png" alt="University of Vavuniya">
                            <h4 style="color: #800000; margin-top: 15px; font-weight: 500;">Employee Leave Management System</h4>
                            <p style="color: #666; margin-top: 5px;">University of Vavuniya</p>
                        </div>
                        
                        <!-- Password Recovery Card -->
                        <div class="card">
                            <div class="card-content">
                                <span class="card-title">Password Recovery</span>
                                <p style="color: #666; font-size: 14px; text-align: center; margin-bottom: 25px;">
                                    Enter your employee ID and email address to reset your password
                                </p>
                                
                                <?php if($msg): ?>
                                    <div class="succWrap">
                                        <i class="material-icons">check_circle</i>
                                        <?php echo htmlentities($msg); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form name="signin" method="post">
                                    <div class="input-field">
                                        <i class="material-icons prefix">badge</i>
                                        <input id="empid" type="text" name="empid" class="validate" autocomplete="off" required>
                                        <label for="empid">Employee ID</label>
                                    </div>
                                    
                                    <div class="input-field">
                                        <i class="material-icons prefix">email</i>
                                        <input id="emailid" type="email" name="emailid" class="validate" autocomplete="off" required>
                                        <label for="emailid">Email Address</label>
                                    </div>
                                    
                                    <button type="submit" name="submit" class="btn waves-effect waves-light">
                                        Reset Password
                                        <i class="material-icons right">arrow_forward</i>
                                    </button>
                                    
                                    <div class="login-links">
                                        <a href="index.php"><i class="material-icons" style="vertical-align: middle; font-size: 16px;">arrow_back</i> Back to Login</a>
                                        <span style="margin: 0 10px; color: #ddd;">|</span>
                                        <a href="admin/">Admin Login <i class="material-icons" style="vertical-align: middle; font-size: 16px;">admin_panel_settings</i></a>
                                    </div>
                                </form>
<?php if(isset($_POST['submit'])) {
    $empid = $_POST['empid'];
    $email = $_POST['emailid'];
    $sql = "SELECT id FROM tblemployees WHERE EmailId=:email and EmpId=:empid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    if($query->rowCount() > 0) {
        foreach ($results as $result) {
            $_SESSION['empid'] = $result->id;
        }
        ?>
        
        <!-- Password Reset Form -->
        <div class="card" style="margin-top: 20px;">
            <div class="card-content">
                <span class="card-title">Change Your Password</span>
                <p style="color: #666; font-size: 14px; margin-bottom: 25px;">
                    Please enter and confirm your new password below.
                </p>
                
                <form name="udatepwd" method="post">
                    <div class="input-field">
                        <i class="material-icons prefix">lock_outline</i>
                        <input id="newpassword" type="password" name="newpassword" class="validate" autocomplete="off" required>
                        <label for="newpassword">New Password</label>
                    </div>

                    <div class="input-field">
                        <i class="material-icons prefix">lock_outline</i>
                        <input id="confirmpassword" type="password" name="confirmpassword" class="validate" autocomplete="off" required>
                        <label for="confirmpassword">Confirm New Password</label>
                    </div>
                    
                    <button class="btn waves-effect waves-light" type="submit" name="change">
                        Change Password
                        <i class="material-icons right">lock_reset</i>
                    </button>
                </form>
            </div>
        </div>
        <?php 
    } else {
        echo "<script>
            M.toast({
                html: 'Invalid Employee ID or Email Address',
                classes: 'red darken-1',
                displayLength: 4000
            });
        </script>";
    } 
} 
?>

<?php 
if(isset($_POST['change'])) {
    $empid = $_SESSION['empid'];
    $password = md5($_POST['newpassword']);
    $sql = "UPDATE tblemployees SET Password = :password WHERE id = :empid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
    
    if($query->execute()) {
        echo "<script>
            M.toast({
                html: 'Your password has been changed successfully!',
                classes: 'green darken-1',
                displayLength: 5000,
                completeCallback: function() {
                    window.location.href = 'index.php';
                }
            });
        </script>";
        // Clear session after password change
        unset($_SESSION['empid']);
    } else {
        echo "<script>
            M.toast({
                html: 'Error updating password. Please try again.',
                classes: 'red darken-1',
                displayLength: 4000
            });
        </script>";
    }
}
?>

                              </div>
                          </div>
                    </div>
                </div>
            </main>
            
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Footer -->
        <footer class="page-footer white" style="padding: 20px 0; margin-top: 50px; border-top: 1px solid #eee;">
            <div class="container">
                <div class="row" style="margin-bottom: 0;">
                    <div class="col s12 center-align">
                        <p style="color: #666; font-size: 13px; margin: 0;">
                            &copy; <?php echo date('Y'); ?> University of Vavuniya. All Rights Reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        
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
                
                // Form validation
                var forms = document.querySelectorAll('form');
                forms.forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        // Check if this is the password reset form
                        if (form.name === 'udatepwd') {
                            var newPassword = document.querySelector('input[name="newpassword"]').value;
                            var confirmPassword = document.querySelector('input[name="confirmpassword"]').value;
                            
                            if (newPassword !== confirmPassword) {
                                e.preventDefault();
                                M.toast({
                                    html: 'Passwords do not match!',
                                    classes: 'red darken-1',
                                    displayLength: 4000
                                });
                                return false;
                            }
                            
                            if (newPassword.length < 6) {
                                e.preventDefault();
                                M.toast({
                                    html: 'Password must be at least 6 characters long!',
                                    classes: 'red darken-1',
                                    displayLength: 4000
                                });
                                return false;
                            }
                            
                            // Show loading indicator
                            var submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = 'Updating... <i class="material-icons right">hourglass_empty</i>';
                            }
                        }
                    });
                });
                
                // Add active class to inputs on focus
                var inputs = document.querySelectorAll('.input-field input, .input-field textarea');
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
                var firstInput = document.querySelector('input:not([type="hidden"])');
                if (firstInput) {
                    firstInput.focus();
                }
                
                // Add ripple effect to buttons
                var buttons = document.querySelectorAll('.btn');
                buttons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        var x = e.pageX - this.getBoundingClientRect().left;
                        var y = e.pageY - this.getBoundingClientRect().top;
                        var ripple = document.createElement('span');
                        ripple.classList.add('ripple');
                        ripple.style.left = x + 'px';
                        ripple.style.top = y + 'px';
                        this.appendChild(ripple);
                        setTimeout(() => {
                            ripple.remove();
                        }, 600);
                    });
                });
            });
            
            // Show loading spinner on form submission
            document.addEventListener('submit', function() {
                var loader = document.querySelector('.loader-bg');
                if (loader) {
                    loader.style.display = 'flex';
                }
            });
            
            // Handle back button to prevent form resubmission
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    </body>
</html>