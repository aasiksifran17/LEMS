<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
    {   
header('location:index.php');
}
else{
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>Admin Dashboard | Employee Leave Management System</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
    <link href="../assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
    <link href="../assets/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet">
    <link href="../assets/css/materialdesign.css" rel="stylesheet">
    
    <!-- Theme Styles -->
    <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* Sidebar Toggle Button */
        .sidebar-toggle {
            position: fixed;
            left: 10px;
            top: 70px;
            background:rgb(145, 30, 30);
            color: white;
            border: none;
            border-radius: 4px;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background:rgb(145, 30, 30);
            transform: scale(1.1);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 25px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 0;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 70px 15px 15px;
            }
            
            .sidebar-toggle {
                display: flex;
            }
        }
        
        .card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px 0 rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px 0 rgba(0,0,0,0.15);
            border-color: rgba(128, 0, 0, 0.2);
        }
        
        .card .card-content {
            padding: 20px;
        }
        
        .card .card-title {
            color: #333;
            font-weight: 300;
            margin-bottom: 15px;
        }
        
        .stats-card .card-content {
            text-align: center;
        }
        
        .stats-card .card-title {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .stats-counter {
            font-size: 24px;
            font-weight: 400;
            color: #800000;
            margin: 10px 0;
        }
        
        .progress {
            margin: 20px 0 0;
            height: 4px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress .determinate {
            background-color: #fff;
            transition: width 0.6s ease;
        }
        
        .progress .determinate {
            background-color: #800000;
        }
        
        .collection {
            border: 1px solid #e0e0e0;
        }
        
        .collection .collection-item {
            padding: 10px 20px;
        }
        
        .collection .collection-item.avatar {
            min-height: 60px;
            padding-left: 72px;
        }
        
        .collection .collection-item.avatar .circle {
            width: 42px;
            height: 42px;
            line-height: 42px;
            font-size: 18px;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
        
        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
    </style>
    <body>
           <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>

            <main class="mn-inner">
                <div class="">
                    <div class="row no-m-t no-m-b">
                        <a href="manageemployee.php" target="blank">
                    <div class="col s12 m12 l4">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">Totle Regd Employee</span>
                                <span class="stats-counter">
<?php
$sql = "SELECT id from tblemployees";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$empcount=$query->rowCount();
?>

                                    <span class="counter"><?php echo htmlentities($empcount);?></span></span>
                            </div>
                           <div class="progress stats-card-progress">
                                <div class="determinate" style="width: 70%"></div>
                            </div>
                        </div>
                    </div></a>
                           <a href="managedepartments.php" target="blank">
                        <div class="col s12 m12 l4">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">Listed Departments </span>
    <?php
$sql = "SELECT id from tbldepartments";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$dptcount=$query->rowCount();
?>                            
                                <span class="stats-counter"><span class="counter"><?php echo htmlentities($dptcount);?></span></span>
                            </div>
                             <div class="progress stats-card-progress">
                                <div class="determinate" style="width: 70%"></div>
                            </div>
                        </div>
                    </div></a>

                      <a href="manageleavetype.php" target="blank">
                    <div class="col s12 m12 l4">
                        <div class="card stats-card">
                            <div class="card-content">
                                <span class="card-title">Listed leave Type</span>
                                    <?php
$sql = "SELECT id from  tblleavetype";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$leavtypcount=$query->rowCount();
?>   
                                <span class="stats-counter"><span class="counter"><?php echo htmlentities($leavtypcount);?></span></span>
                      
                            </div>
                            <div class="progress stats-card-progress">
                                <div class="determinate" style="width: 70%"></div>
                            </div>
                        </div>
                    </div></a>


                    <a href="leaves.php" target="blank">
                    <div class="col s12 m12 l4">
                        <div class="card stats-card">
                            <div class="card-content">
                                <span class="card-title">Total Leaves</span>
                                    <?php
$sql = "SELECT id from  tblleaves";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$totalleaves=$query->rowCount();
?>   
                                <span class="stats-counter"><span class="counter"><?php echo htmlentities($totalleaves);?></span></span>
                      
                            </div>
                            <div class="progress stats-card-progress">
                                <div class="success" style="width: 70%"></div>
                            </div>
                        </div>
                    </div></a>

     <a href="approvedleave-history.php" target="blank">
                    <div class="col s12 m12 l4">
                        <div class="card stats-card">
                            <div class="card-content">
                                <span class="card-title">Approved Leaves</span>
                                    <?php
$sql = "SELECT id from  tblleaves where Status=1";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$approvedleaves=$query->rowCount();
?>   
                                <span class="stats-counter"><span class="counter"><?php echo htmlentities($approvedleaves);?></span></span>
                      
                            </div>
                            <div class="progress stats-card-progress">
                                <div class="success" style="width: 70%"></div>
                            </div>
                        </div>
                    </div></a>



     <a href="pending-leavehistory.php" target="blank">
                    <div class="col s12 m12 l4">
                        <div class="card stats-card">
                            <div class="card-content">
                                <span class="card-title">New Leaves Applications</span>
                                    <?php
$sql = "SELECT id from  tblleaves where Status=0";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$approvedleaves=$query->rowCount();
?>   
                                <span class="stats-counter"><span class="counter"><?php echo htmlentities($approvedleaves);?></span></span>
                      
                            </div>
                            <div class="progress stats-card-progress">
                                <div class="success" style="width: 70%"></div>
                            </div>
                        </div>
                    </div></a>









                </div>
                 
                    <div class="row no-m-t no-m-b">
                        <div class="col s15 m12 l12">
                            <div class="card invoices-card">
                                <div class="card-content">
                                 
                                    <span class="card-title">Latest Leave Applications</span>
                             <table id="example" class="display responsive-table ">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th width="200">Employe Name</th>
                                            <th width="120">Leave Type</th>

                                             <th width="180">Posting Date</th>                 
                                            <th>Status</th>
                                            <th align="center">Action</th>
                                        </tr>
                                    </thead>
                                 
                                    <tbody>
<?php $sql = "SELECT tblleaves.id as lid,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblleaves.LeaveType,tblleaves.PostingDate,tblleaves.Status from tblleaves join tblemployees on tblleaves.empid=tblemployees.id order by lid desc limit 6";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{         
      ?>  

                                        <tr>
                                            <td> <b><?php echo htmlentities($cnt);?></b></td>
                                              <td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>" target="_blank"><?php echo htmlentities($result->FirstName." ".$result->LastName);?>(<?php echo htmlentities($result->EmpId);?>)</a></td>
                                            <td><?php echo htmlentities($result->LeaveType);?></td>
                                            <td><?php echo htmlentities($result->PostingDate);?></td>
                                                                       <td><?php $stats=$result->Status;
if($stats==1){
                                             ?>
                                                 <span style="color: green">Approved</span>
                                                 <?php } if($stats==2)  { ?>
                                                <span style="color: red">Not Approved</span>
                                                 <?php } if($stats==0)  { ?>
 <span style="color: blue">waiting for approval</span>
 <?php } ?>


                                             </td>

          <td>
           <td><a href="leave-details.php?leaveid=<?php echo htmlentities($result->lid);?>" class="waves-effect waves-light btn blue m-b-xs"  > View Details</a></td>
                                    </tr>
                                         <?php $cnt++;} }?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              
            </main>
          
        </div>

        
        
        <!-- Javascripts -->
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="../assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
        <script src="../assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="../assets/plugins/chart.js/chart.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../assets/plugins/curvedlines/curvedLines.js"></script>
        <script src="../assets/plugins/peity/jquery.peity.min.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/dashboard.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        $(document).ready(function() {
            const sidebar = $('.side-nav');
            const content = $('.main-content');
            const toggleBtn = $('#sidebarToggle');
            
            // Toggle sidebar on button click
            toggleBtn.on('click', function() {
                sidebar.toggleClass('collapsed');
                content.toggleClass('expanded');
                
                // Toggle icon between bars and times
                const icon = toggleBtn.find('i');
                if (icon.hasClass('fa-bars')) {
                    icon.removeClass('fa-bars').addClass('fa-times');
                } else {
                    icon.removeClass('fa-times').addClass('fa-bars');
                }
                
                // Save state to localStorage
                if (sidebar.hasClass('collapsed')) {
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            });
            
            // Check for saved state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.addClass('collapsed');
                content.addClass('expanded');
                toggleBtn.find('i').removeClass('fa-bars').addClass('fa-times');
            }
            
            // Close sidebar when clicking outside on mobile
            $(document).on('click', function(e) {
                if ($(window).width() <= 992) {
                    if (!$(e.target).closest('.side-nav, #sidebarToggle').length && !sidebar.hasClass('collapsed')) {
                        sidebar.addClass('collapsed');
                        content.addClass('expanded');
                        toggleBtn.find('i').removeClass('fa-times').addClass('fa-bars');
                        localStorage.setItem('sidebarCollapsed', 'true');
                    }
                }
            });
        });
    </script>
        
    </body>
</html>
<?php } ?>