
<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_POST['signin']))
{
$uname=$_POST['username'];
$password=md5($_POST['password']);
$sql ="SELECT EmailId,Password,Status,id FROM tblemployees WHERE EmailId=:uname and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':uname', $uname, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
 foreach ($results as $result) {
    $status=$result->Status;
    $_SESSION['eid']=$result->id;
  } 
if($status==0)
{
$msg="Your account is Inactive. Please contact admin";
} else{
$_SESSION['emplogin']=$_POST['username'];
echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
} }

else{
  
  echo "<script>alert('Invalid Details');</script>";

}

}

?><!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>ELMS | Home Page</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                margin-top: 15px;
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
                padding: 10px 15px;
                border-radius: 4px;
                margin-bottom: 20px;
                font-size: 14px;
                border-left: 4px solid #c62828;
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
        </style>
        
        
    </head>
    <body>
        <div class="loader-bg"></div>
        <div class="loader">
            <div class="preloader-wrapper big active">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-spinner-teal lighten-1">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mn-content fixed-sidebar">
            <header class="navbar-fixed" style="box-shadow: none;">
                <nav class="white" style="height: 70px; line-height: 70px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <div class="nav-wrapper container">
                        <a href="#" class="brand-logo" style="color: #800000; font-weight: 500; font-size: 1.5rem;">
                            <img src="assets/images/uov_logo.png" alt="Logo" style="height: 50px; vertical-align: middle; margin-right: 10px;">
                            <span style="vertical-align: middle;">ELMS</span>
                        </a>
                        <ul id="nav-mobile" class="right hide-on-med-and-down">
                            <li><a href="admin/" style="color: #800000;">Admin Login</a></li>
                        </ul>
                    </div>
                </nav>
            </header>
           
           
            <aside id="slide-out" class="side-nav white fixed">
                <div class="side-nav-wrapper">
                   
                  
                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion" style="">
                    <li>&nbsp;</li>
                    <li class="no-padding"><a class="waves-effect waves-grey" href="index.php"><i class="material-icons">account_box</i>Employe Login</a></li>
                    <li class="no-padding"><a class="waves-effect waves-grey" href="forgot-password.php"><i class="material-icons">account_box</i>Emp Password Recovery</a></li>
                
                       <li class="no-padding"><a class="waves-effect waves-grey" href="admin/"><i class="material-icons">account_box</i>Admin Login</a></li>
                
                </ul>
          <div class="footer">
                    <p class="copyright">ELMS ©</p>
                
                </div>
                </div>
            </aside>
            <main>
                <div class="container login-container">
                    <div class="row">
                        <div class="col s12 m8 l6 offset-m2 offset-l3">
                            <div class="login-logo">
                                <img src="assets/images/uov_logo.png" alt="University of Vavuniya">
                                <h4 style="color: #800000; margin-top: 15px; font-weight: 500;">Employee Leave Management System</h4>
                                <p style="color: #666; margin-top: 5px;">University of Vavuniya</p>
                            </div>
                            
                            <div class="card">
                                <div class="card-content">
                                    <span class="card-title">Employee Login</span>
                                    <?php if($msg){ ?>
                                        <div class="errorWrap">
                                            <i class="material-icons" style="vertical-align: middle; margin-right: 5px;">error_outline</i>
                                            <?php echo htmlentities($msg); ?>
                                        </div>
                                    <?php } ?>
                                    
                                    <form name="signin" method="post">
                                        <div class="input-field">
                                            <i class="material-icons prefix" style="color: #800000;">email</i>
                                            <input id="username" type="text" name="username" class="validate" autocomplete="off" required>
                                            <label for="username">Email Address</label>
                                        </div>
                                        
                                        <div class="input-field">
                                            <i class="material-icons prefix" style="color: #800000;">lock</i>
                                            <input id="password" type="password" name="password" autocomplete="off" required>
                                            <label for="password">Password</label>
                                        </div>
                                        
                                        <div class="login-links">
                                            <a href="forgot-password.php">Forgot Password?</a>
                                            <span>|</span>
                                            <a href="admin/">Admin Login</a>
                                        </div>
                                        
                                        <button type="submit" name="signin" class="btn waves-effect waves-light">
                                            Sign In
                                            <i class="material-icons right">arrow_forward</i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="login-footer">
                                &copy; <?php echo date('Y'); ?> University of Vavuniya. All rights reserved.
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/plugins/chart.js/chart.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="assets/plugins/curvedlines/curvedLines.js"></script>
        <script src="assets/plugins/peity/jquery.peity.min.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        <script src="assets/js/pages/dashboard.js"></script>
        
    </body>
</html>