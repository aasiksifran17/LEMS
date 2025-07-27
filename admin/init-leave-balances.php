<?php
// Script to initialize leave balances for all employees
require_once('../includes/config.php');
require_once('../includes/LeaveManager.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get current year
$currentYear = date('Y');
$leaveManager = new LeaveManager($dbh);

// Get all active employees
$query = "SELECT id, role, position FROM tblemployees WHERE Status = 1";
$stmt = $dbh->query($query);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all active leave types
$query = "SELECT id, code FROM tblleavetype WHERE is_active = 1";
$stmt = $dbh->query($query);
$leaveTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$processed = 0;
$errors = [];

foreach ($employees as $employee) {
    foreach ($leaveTypes as $leaveType) {
        try {
            // This will automatically create balance record if it doesn't exist
            $balance = $leaveManager->getLeaveBalance($employee['id'], $leaveType['id'], $currentYear);
            $processed++;
        } catch (Exception $e) {
            $errors[] = "Error for employee ID {$employee['id']}, leave type {$leaveType['code']}: " . $e->getMessage();
        }
    }
}

// Output results
echo "<h2>Leave Balances Initialization Complete</h2>";
echo "<p>Processed $processed leave balance records.</p>";

if (!empty($errors)) {
    echo "<h3>Errors encountered:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}

// Add link to admin dashboard
echo "<p><a href='dashboard.php'>Return to Dashboard</a></p>";
?>
