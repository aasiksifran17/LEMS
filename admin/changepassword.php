<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize variables
$msg = '';
$error = '';

// Check if user is logged in
if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit();
}

// Process form submission
if(isset($_POST['change'])) {
    // Get form data
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $username = $_SESSION['alogin'];
    
    // Validate form data
    if(empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required";
    } elseif($newPassword !== $confirmPassword) {
        $error = "New password and confirm password do not match";
    } elseif(strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Verify current password
        $sql = "SELECT Password FROM admin WHERE UserName = :username";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        
        if($result && $result->Password === md5($currentPassword)) {
            // Update password
            $hashedPassword = md5($newPassword);
            $updateSql = "UPDATE admin SET Password = :password WHERE UserName = :username";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':username', $username, PDO::PARAM_STR);
            $updateQuery->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            
            if($updateQuery->execute()) {
                $msg = "Your password has been changed successfully";
                // Clear form on success
                $_POST = array();
                // Auto redirect after 2 seconds
                echo "<script>setTimeout(function(){window.location.href='dashboard.php'},2000);</script>";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        } else {
            $error = "Current password is incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Change Password | Admin Panel</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
    <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
    <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 600px;
            border: none;
        }
        
        .card .card-content {
            padding: 30px;
        }
        
        .page-title {
            font-size: 24px;
            margin: 20px 0;
            color: #444;
            font-weight: 500;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .input-field label {
            color: #666;
            font-size: 14px;
        }
        
        .input-field input[type=password]:focus + label,
        .input-field input[type=password]:focus {
            color: #800000;
            border-bottom: 1px solid #800000;
            box-shadow: 0 1px 0 0 #800000;
        }
        
        .btn-custom {
            background-color: #800000;
            text-transform: capitalize;
            padding: 0 30px;
            height: 42px;
            line-height: 42px;
            font-size: 15px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-custom:hover {
            background-color: #6a0a0a;
        }
        
        .errorWrap {
            padding: 15px;
            margin: 0 0 20px 0;
            background: #ffebee;
            border-left: 4px solid #f44336;
            border-radius: 0 4px 4px 0;
            color: #c62828;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .succWrap {
            padding: 15px;
            margin: 0 0 20px 0;
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            border-radius: 0 4px 4px 0;
            color: #2e7d32;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .errorWrap i, .succWrap i {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .strength-weak { color: #f44336; }
        .strength-medium { color: #ff9800; }
        .strength-strong { color: #4caf50; }
        
        @media (max-width: 600px) {
            .card {
                margin: 10px;
            }
            
            .card .card-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Change Password</div>
            </div>
            
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <?php 
                        if($error) { 
                            echo '<div class="errorWrap"><i class="material-icons">error_outline</i> ' . htmlspecialchars($error) . '</div>';
                        } 
                        if($msg) { 
                            echo '<div class="succWrap"><i class="material-icons">check_circle</i> ' . htmlspecialchars($msg) . '</div>';
                        }
                        ?>
                        
                        <form id="changePasswordForm" method="post" autocomplete="off">
                            <div class="row">
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">lock_outline</i>
                                    <input id="current_password" name="current_password" type="password" class="validate" required>
                                    <label for="current_password">Current Password <span class="red-text">*</span></label>
                                </div>
                                
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">vpn_key</i>
                                    <input id="new_password" name="new_password" type="password" class="validate" minlength="6" required>
                                    <label for="new_password">New Password <span class="red-text">*</span></label>
                                    <span class="helper-text" data-error="Password must be at least 6 characters">At least 6 characters</span>
                                    <div class="password-strength" id="passwordStrength"></div>
                                </div>
                                
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">check_circle_outline</i>
                                    <input id="confirm_password" name="confirm_password" type="password" class="validate" minlength="6" required>
                                    <label for="confirm_password">Confirm New Password <span class="red-text">*</span></label>
                                    <span class="helper-text" id="passwordMatch"></span>
                                </div>
                                
                                <div class="col s12">
                                    <button type="submit" name="change" class="btn btn-custom waves-effect waves-light">
                                        <i class="material-icons left">lock_reset</i> Change Password
                                    </button>
                                    <a href="dashboard.php" class="btn grey lighten-1 waves-effect" style="margin-left: 10px;">
                                        <i class="material-icons left">cancel</i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Javascripts -->
    <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/js/alpha.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize materialize components
        M.updateTextFields();
        
        // Auto-hide messages after 5 seconds
        setTimeout(function() {
            $('.errorWrap, .succWrap').fadeOut('slow');
        }, 5000);
        
        // Password strength indicator
        $('#new_password').on('keyup', function() {
            var password = $(this).val();
            var strength = 0;
            var strengthText = '';
            var strengthClass = '';
            
            if (password.length >= 6) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength += 1;
            
            if (strength < 2) {
                strengthText = 'Weak';
                strengthClass = 'strength-weak';
            } else if (strength < 4) {
                strengthText = 'Medium';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Strong';
                strengthClass = 'strength-strong';
            }
            
            if (password.length > 0) {
                $('#passwordStrength').html('Strength: <span class="' + strengthClass + '">' + strengthText + '</span>').show();
            } else {
                $('#passwordStrength').hide();
            }
        });
        
        // Password match validation
        $('#confirm_password').on('keyup', function() {
            if ($('#new_password').val() !== $(this).val()) {
                $('#passwordMatch').html('Passwords do not match').css('color', '#f44336');
                return false;
            } else {
                $('#passwordMatch').html('Passwords match').css('color', '#4caf50');
                return true;
            }
        });
        
        // Form validation
        $('#changePasswordForm').on('submit', function(e) {
            if ($('#new_password').val() !== $('#confirm_password').val()) {
                e.preventDefault();
                M.toast({html: 'Passwords do not match!', classes: 'red'});
                return false;
            }
            return true;
        });
    });
    </script>
</body>
</html> 