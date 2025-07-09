<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
    $msg = '';
    $error = '';
    
    if(isset($_POST['add'])) {
        $deptname = trim($_POST['departmentname']);
        $deptshortname = trim($_POST['departmentshortname']);
        $deptcode = trim($_POST['deptcode']);   
        $fcode = $_POST['faculty'];
        
        // Input validation
        if(empty($fcode)) {
            $error = "Please select a faculty";
        } elseif(empty($deptname) || empty($deptshortname) || empty($deptcode)) {
            $error = "All fields are required";
        } else {
            // Check if department code already exists
            $sql = "SELECT id FROM tbldepartments WHERE DepartmentCode = :deptcode";
            $query = $dbh->prepare($sql);
            $query->bindParam(':deptcode', $deptcode, PDO::PARAM_STR);
            $query->execute();
            
            if($query->rowCount() > 0) {
                $error = "Department code already exists";
            } else {
                $sql = "INSERT INTO tbldepartments(DepartmentName, DepartmentCode, DepartmentShortName, FacultyCode, CreationDate) 
                        VALUES(:deptname, :deptcode, :deptshortname, :facultycode, NOW())";
                $query = $dbh->prepare($sql);
                $query->bindParam(':deptname', $deptname, PDO::PARAM_STR);
                $query->bindParam(':deptcode', $deptcode, PDO::PARAM_STR);
                $query->bindParam(':facultycode', $fcode, PDO::PARAM_STR);
                $query->bindParam(':deptshortname', $deptshortname, PDO::PARAM_STR);
                
                if($query->execute()) {
                    $msg = "Department created successfully";
                    // Clear form
                    $deptname = $deptshortname = $deptcode = '';
                    echo "<script>setTimeout(function(){window.location.href='manage-departments.php'},2000);</script>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>Add Department | Employee Leave Management System</title>
    
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
        .input-field input[type=password]:focus + label,
        .input-field input[type=email]:focus + label {
            color: #800000;
        }
        
        .input-field input[type=text]:focus,
        .input-field input[type=password]:focus,
        .input-field input[type=email]:focus {
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
        }
        
        .succWrap {
            padding: 15px;
            margin: 0 0 20px 0;
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            border-radius: 0 4px 4px 0;
            color: #2e7d32;
            font-size: 14px;
        }
        
        select {
            display: block;
        }
        
        .select-wrapper input.select-dropdown {
            margin-bottom: 0;
        }
        
        .input-field {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Add New Department</div>
            </div>
            
            <div class="col s12 m8 offset-m2">
                <div class="card">
                    <div class="card-content">
                        <div class="row">
                            <form class="col s12" method="post" autocomplete="off">
                                <?php 
                                if($error) { 
                                    echo '<div class="errorWrap"><i class="material-icons" style="vertical-align:middle;margin-right:5px;">error_outline</i> ' . htmlentities($error) . '</div>';
                                } 
                                if($msg) { 
                                    echo '<div class="succWrap"><i class="material-icons" style="vertical-align:middle;margin-right:5px;">check_circle</i> ' . htmlentities($msg) . '</div>';
                                }
                                ?>
                                
                                <div class="row">
                                    <div class="input-field col s12">
                                        <select name="faculty" class="browser-default" required>
                                            <option value="" disabled selected>Select Faculty</option>
                                            <?php 
                                            $sql = "SELECT FacultyName, FacultyCode FROM tblfaculty ORDER BY FacultyName";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            
                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) {   
                                            ?>                                            
                                            <option value="<?php echo htmlentities($result->FacultyCode); ?>" <?php echo (isset($fcode) && $fcode == $result->FacultyCode) ? 'selected' : ''; ?>>
                                                <?php echo htmlentities($result->FacultyName); ?>
                                            </option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="input-field col s12">
                                        <input id="departmentname" type="text" class="validate" name="departmentname" value="<?php echo isset($deptname) ? htmlentities($deptname) : ''; ?>" required>
                                        <label for="departmentname">Department Name</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <input id="departmentshortname" type="text" class="validate" name="departmentshortname" value="<?php echo isset($deptshortname) ? htmlentities($deptshortname) : ''; ?>" required>
                                        <label for="departmentshortname">Department Short Name</label>
                                    </div>
                                    
                                    <div class="input-field col s12">
                                        <input id="deptcode" type="text" name="deptcode" class="validate" value="<?php echo isset($deptcode) ? htmlentities($deptcode) : ''; ?>" required>
                                        <label for="deptcode">Department Code</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <button type="submit" name="add" class="waves-effect waves-light btn btn-custom">
                                            <i class="material-icons left">add_circle</i> Add Department
                                        </button>
                                        <a href="manage-departments.php" class="waves-effect waves-light btn grey lighten-1" style="margin-left: 10px;">
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
    <script src="../assets/js/alpha.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize select dropdown
            $('select').formSelect();
            
            // Auto-hide success/error messages after 5 seconds
            setTimeout(function() {
                $('.succWrap, .errorWrap').fadeOut('slow');
            }, 5000);
        });
    </script>
</body>
</html>
<?php } ?> 