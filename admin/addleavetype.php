<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit();
}

$msg = '';
$error = '';
$leavetype = '';
$description = '';

if(isset($_POST['add'])) {
    $leavetype = trim($_POST['leavetype']);
    $description = trim($_POST['description']);
    
    // Input validation
    if(empty($leavetype)) {
        $error = "Leave type is required";
    } else {
        // Check if leave type already exists
        $check_sql = "SELECT id FROM tblleavetype WHERE LOWER(LeaveType) = LOWER(:leavetype)";
        $check_query = $dbh->prepare($check_sql);
        $check_query->bindParam(':leavetype', $leavetype, PDO::PARAM_STR);
        $check_query->execute();
        
        if($check_query->rowCount() > 0) {
            $error = "This leave type already exists";
        } else {
            $sql = "INSERT INTO tblleavetype(LeaveType, Description, CreationDate) VALUES(:leavetype, :description, NOW())";
            $query = $dbh->prepare($sql);
            $query->bindParam(':leavetype', $leavetype, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            
            if($query->execute()) {
                $msg = "Leave type added successfully";
                // Clear form on success
                $leavetype = $description = '';
                echo "<script>setTimeout(function(){window.location.href='manage-leavetype.php'},2000);</script>";
            } else {
                $error = "Something went wrong. Please try again";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Add Leave Type | Employee Leave Management System</title>
    
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
            margin-bottom: 30px;
            border: none;
        }
        
        .card .card-content {
            padding: 30px;
        }
        
        .page-title {
            font-size: 24px;
            margin-bottom: 30px;
            color: #444;
            font-weight: 500;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .input-field label {
            color: #666;
            font-size: 14px;
        }
        
        .input-field input[type=text]:focus + label,
        .input-field textarea.materialize-textarea:focus + label {
            color: #800000;
        }
        
        .input-field input[type=text]:focus,
        .input-field textarea.materialize-textarea:focus {
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
        
        .char-counter {
            font-size: 12px;
            color: #9e9e9e;
            margin-top: -15px;
            margin-bottom: 15px;
        }
        
        .btn-cancel {
            margin-left: 10px;
            background-color: #9e9e9e;
        }
        
        .btn-cancel:hover {
            background-color: #757575;
        }
        
        @media (max-width: 600px) {
            .btn-custom, .btn-cancel {
                width: 100%;
                margin: 5px 0;
            }
            
            .btn-cancel {
                margin-left: 0;
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
                    <div class="page-title">Add New Leave Type</div>
                </div>
                
                <div class="col s12 m8 offset-m2">
                    <div class="card">
                        <div class="card-content">
                            <div class="row">
                                <form class="col s12" method="post" autocomplete="off">
                                    <?php 
                                    if($error) { 
                                        echo '<div class="errorWrap"><i class="material-icons">error_outline</i> ' . htmlentities($error) . '</div>';
                                    } 
                                    if($msg) { 
                                        echo '<div class="succWrap"><i class="material-icons">check_circle</i> ' . htmlentities($msg) . '</div>';
                                    }
                                    ?>
                                    
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="leavetype" type="text" class="validate" name="leavetype" 
                                                   value="<?php echo htmlentities($leavetype); ?>" required>
                                            <label for="leavetype">Leave Type <span style="color:red">*</span></label>
                                        </div>

                                        <div class="input-field col s12">
                                            <textarea id="description" name="description" class="materialize-textarea" data-length="500"><?php echo htmlentities($description); ?></textarea>
                                            <label for="description">Description</label>
                                            <span class="char-counter">Max 500 characters</span>
                                        </div>

                                        <div class="input-field col s12">
                                            <button type="submit" name="add" class="waves-effect waves-light btn btn-custom">
                                                <i class="material-icons left">add_circle</i> Add Leave Type
                                            </button>
                                            <a href="manage-leavetype.php" class="waves-effect waves-light btn btn-cancel">
                                                <i class="material-icons left">cancel</i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
                // Initialize character counter for textarea
                $('textarea#description').characterCounter();
                
                // Auto-hide success/error messages after 5 seconds
                setTimeout(function() {
                    $('.succWrap, .errorWrap').fadeOut('slow');
                }, 5000);
                
                // Initialize materialize components
                M.updateTextFields();
                M.textareaAutoResize($('textarea'));
            });
        </script>
    </body>
</html>