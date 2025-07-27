<?php
session_start();
include('includes/config.php');
require_once('includes/LeaveManager.php');

// Check if user is logged in
if(strlen($_SESSION['emplogin'])==0) {
    header('location:index.php');
    exit();
}

$employeeId = $_SESSION['eid'];
$leaveManager = new LeaveManager($dbh);
$error = '';

// Get available leave types
try {
    $availableLeaveTypes = $leaveManager->getAvailableLeaveTypes($employeeId);
} catch (Exception $e) {
    $error = "Error loading leave types: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $leaveTypeId = $_POST['leavetype'] ?? 0;
        $fromDate = $_POST['fromdate'] ?? '';
        $toDate = $_POST['todate'] ?? '';
        $description = $_POST['description'] ?? '';
        
        // Find selected leave type
        $selectedLeaveType = array_filter($availableLeaveTypes, fn($t) => $t['id'] == $leaveTypeId);
        if (empty($selectedLeaveType)) {
            throw new Exception('Invalid leave type selected');
        }
        $selectedLeaveType = reset($selectedLeaveType);
        
        // Submit leave request
        $leaveId = $leaveManager->submitLeaveRequest([
            'employee_id' => $employeeId,
            'leave_type_id' => $leaveTypeId,
            'leave_type_name' => $selectedLeaveType['LeaveType'],
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'description' => $description
        ]);
        
        $_SESSION['success'] = "Leave request submitted successfully. Reference ID: " . $leaveId;
        header('Location: leave-history.php');
        exit();
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get today's date in YYYY-MM-DD format for the date input min attribute
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Apply For Leave</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        /* Style for modern date inputs */
        input[type="date"] {
            position: relative;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 38px;
        }
        
        /* Hide the default calendar icon in WebKit browsers */
        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            padding: 0;
            color: transparent;
            background: transparent;
        }
        
        /* Custom calendar icon */
        .date-input-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .date-input-container:after {
            content: '\f073';
            font-family: 'FontAwesome';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #555;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="container">
        <div class="row">
            <?php include('includes/sidebar.php'); ?>
            
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Apply For Leave</h3>
                    </div>
                    <div class="panel-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <form id="leaveForm" method="post">
                            <div class="form-group">
                                <label>Leave Type</label>
                                <select class="form-control" name="leavetype" required>
                                    <option value="">-- Select --</option>
                                    <?php foreach ($availableLeaveTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>">
                                            <?php echo htmlspecialchars($type['LeaveType']); ?>
                                            (<?php echo $type['days_per_year']; ?> days)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <div class="date-input-container">
                                            <input type="date" class="form-control" name="fromdate" 
                                                   min="<?php echo $today; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <div class="date-input-container">
                                            <input type="date" class="form-control" name="todate" 
                                                   min="<?php echo $today; ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Working Days</label>
                                <input type="text" class="form-control" id="numofdays" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script>
    // Function to get next working day (skip weekends)
    function getNextWorkingDay(date) {
        const nextDay = new Date(date);
        nextDay.setDate(nextDay.getDate() + 1);
        
        // Skip weekends (0 = Sunday, 6 = Saturday)
        while (nextDay.getDay() === 0 || nextDay.getDay() === 6) {
            nextDay.setDate(nextDay.getDate() + 1);
        }
        
        return nextDay;
    }
    
    // Format date as YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    $(document).ready(function() {
        // Set default dates
        const today = new Date();
        const tomorrow = getNextWorkingDay(today);
        
        // Set default from date (today)
        $('input[name="fromdate"]').val(formatDate(today));
        
        // Set default to date (next working day)
        $('input[name="todate"]').val(formatDate(tomorrow));
        
        // Update min date for to date when from date changes
        $('input[name="fromdate"]').on('change', function() {
            const fromDate = new Date($(this).val());
            $('input[name="todate"]').attr('min', $(this).val());
            
            // If to date is before from date, update it
            const toDate = new Date($('input[name="todate"]').val());
            if (toDate < fromDate) {
                $('input[name="todate"]').val($(this).val());
            }
            
            calculateWorkingDays();
        });
        
        // Calculate working days when either date changes
        $('input[name="todate"]').on('change', function() {
            calculateWorkingDays();
        });
        
        // Initial calculation
        calculateWorkingDays();
        
        // Calculate working days between two dates (excluding weekends)
        function calculateWorkingDays() {
            const fromDate = new Date($('input[name="fromdate"]').val());
            const toDate = new Date($('input[name="todate"]').val());
            
            if (isNaN(fromDate.getTime()) || isNaN(toDate.getTime())) {
                $('#numofdays').val('0');
                return;
            }
            
            // Swap dates if from date is after to date
            if (fromDate > toDate) {
                const temp = $('input[name="fromdate"]').val();
                $('input[name="fromdate"]').val($('input[name="todate"]').val());
                $('input[name="todate"]').val(temp);
                return calculateWorkingDays();
            }
            
            let workingDays = 0;
            const currentDate = new Date(fromDate);
            
            while (currentDate <= toDate) {
                const dayOfWeek = currentDate.getDay();
                // Check if it's a weekday (0 = Sunday, 6 = Saturday)
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    workingDays++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            $('#numofdays').val(workingDays);
        }
    });
    </script>
</body>
</html>