<?php
/**
 * Test Script for Leave Management System
 * 
 * This script verifies that all required database tables exist and have the correct structure.
 * It also tests basic functionality of the LeaveManager class.
 */

require_once('../includes/config.php');
require_once('../includes/LeaveManager.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Leave Management System Test</h2>";

// Test database connection
try {
    $dbh->query('SELECT 1');
    echo "<div class='alert alert-success'>✓ Database connection successful</div>";
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>✗ Database connection failed: " . $e->getMessage() . "</div>");
}

// List of required tables and their required columns
$requiredTables = [
    'tblemployees' => ['id', 'EmpId', 'FirstName', 'LastName', 'role', 'gender', 'position', 'Status'],
    'tblleavetype' => ['id', 'LeaveType', 'Description', 'is_active', 'requires_approval', 'is_gender_specific', 'min_position_level'],
    'tbl_leave_limits' => ['id', 'role', 'leave_type_id', 'days_per_year', 'is_carry_forward', 'max_carry_forward_days'],
    'tbl_leave_balances' => ['id', 'employee_id', 'leave_type_id', 'year', 'total_days', 'used_days', 'pending_days', 'carried_over_days'],
    'tbl_position_levels' => ['id', 'position_name', 'level']
];

// Check if tables exist and have required columns
$allTablesOk = true;
foreach ($requiredTables as $table => $requiredColumns) {
    try {
        // Check if table exists
        $stmt = $dbh->query("SHOW TABLES LIKE '" . $table . "'");
        if ($stmt->rowCount() == 0) {
            echo "<div class='alert alert-danger'>✗ Table '$table' does not exist</div>";
            $allTablesOk = false;
            continue;
        }
        
        // Check columns
        $stmt = $dbh->query("DESCRIBE `$table`");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (!empty($missingColumns)) {
            echo "<div class='alert alert-warning'>⚠ Table '$table' is missing columns: " . implode(', ', $missingColumns) . "</div>";
            $allTablesOk = false;
        } else {
            echo "<div class='alert alert-success'>✓ Table '$table' exists with all required columns</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>✗ Error checking table '$table': " . $e->getMessage() . "</div>";
        $allTablesOk = false;
    }
}

// Test LeaveManager if tables are OK
if ($allTablesOk) {
    echo "<h3>Testing LeaveManager</h3>";
    
    try {
        $leaveManager = new LeaveManager($dbh);
        
        // Test 1: Get available leave types for admin (assuming admin is employee ID 1)
        echo "<h4>Test 1: Get available leave types</h4>";
        $availableLeaveTypes = $leaveManager->getAvailableLeaveTypes(1);
        if (is_array($availableLeaveTypes) && !empty($availableLeaveTypes)) {
            echo "<div class='alert alert-success'>✓ Successfully retrieved " . count($availableLeaveTypes) . " leave types</div>";
            echo "<pre>" . print_r($availableLeaveTypes, true) . "</pre>";
        } else {
            echo "<div class='alert alert-warning'>⚠ No leave types available or error retrieving them</div>";
        }
        
        // Test 2: Get leave balance
        echo "<h4>Test 2: Get leave balance</h4>";
        if (!empty($availableLeaveTypes)) {
            $firstLeaveType = reset($availableLeaveTypes);
            $balance = $leaveManager->getLeaveBalance(1, $firstLeaveType['id']);
            if (is_array($balance) && isset($balance['total_days'])) {
                echo "<div class='alert alert-success'>✓ Successfully retrieved leave balance for leave type ID " . $firstLeaveType['id'] . "</div>";
                echo "<pre>" . print_r($balance, true) . "</pre>";
            } else {
                echo "<div class='alert alert-warning'>⚠ Could not retrieve leave balance</div>";
            }
        }
        
        // Test 3: Calculate working days
        echo "<h4>Test 3: Calculate working days</h4>";
        $workingDays = $leaveManager->calculateWorkingDays('2023-01-01', '2023-01-31');
        if ($workingDays > 0) {
            echo "<div class='alert alert-success'>✓ Working days between Jan 1-31, 2023: $workingDays days</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠ Unexpected working days calculation: $workingDays</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>✗ Error testing LeaveManager: " . $e->getMessage() . "</div>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}

// Add some basic CSS
echo "
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
    .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
    .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
    .alert-warning { color: #8a6d3b; background-color: #fcf8e3; border-color: #faebcc; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";
?>
