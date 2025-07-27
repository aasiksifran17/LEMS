<?php
// Initialize database with required tables and sample data
require_once('../includes/config.php');

// Disable error reporting for production
// error_reporting(0);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to execute SQL queries
executeSql("SET FOREIGN_KEY_CHECKS = 0");

// 1. Update employees table to add role and gender
$sql = [
    // Add role and gender to employees table if they don't exist
    "ALTER TABLE tblemployees 
     ADD COLUMN IF NOT EXISTS role ENUM('academic', 'non_academic') NOT NULL DEFAULT 'non_academic' AFTER Department,
     ADD COLUMN IF NOT EXISTS gender ENUM('M', 'F', 'O') DEFAULT 'O' AFTER role,
     ADD COLUMN IF NOT EXISTS position VARCHAR(100) DEFAULT 'Staff' AFTER gender",
     
    // Create position levels table if it doesn't exist
    "CREATE TABLE IF NOT EXISTS `tbl_position_levels` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `position_name` varchar(100) NOT NULL,
        `level` int(11) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `position_name` (`position_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // Insert default position levels
    "INSERT IGNORE INTO `tbl_position_levels` (`position_name`, `level`) VALUES
        ('Professor', 3),
        ('Associate Professor', 3),
        ('Assistant Professor', 2),
        ('Senior Lecturer', 2),
        ('Lecturer', 1),
        ('Teaching Assistant', 0),
        ('Manager', 2),
        ('Executive', 1),
        ('Staff', 0)",
    
    // Update leave types table with new fields
    "ALTER TABLE `tblleavetype`
        ADD COLUMN IF NOT EXISTS `code` VARCHAR(50) NULL AFTER `id`,
        ADD COLUMN IF NOT EXISTS `is_active` BOOLEAN DEFAULT TRUE AFTER `Description`,
        ADD COLUMN IF NOT EXISTS `requires_approval` BOOLEAN DEFAULT TRUE AFTER `is_active`,
        ADD COLUMN IF NOT EXISTS `is_gender_specific` ENUM('M', 'F') NULL DEFAULT NULL AFTER `requires_approval`,
        ADD COLUMN IF NOT EXISTS `min_position_level` INT DEFAULT 0 AFTER `is_gender_specific`,
        ADD UNIQUE INDEX IF NOT EXISTS `idx_code` (`code`)",
    
    // Create leave limits table
    "CREATE TABLE IF NOT EXISTS `tbl_leave_limits` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `role` enum('academic','non_academic') NOT NULL,
        `leave_type_id` int(11) NOT NULL,
        `days_per_year` int(11) NOT NULL,
        `is_carry_forward` tinyint(1) DEFAULT 0,
        `max_carry_forward_days` int(11) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `role_leave_type` (`role`,`leave_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // Create leave balances table
    "CREATE TABLE IF NOT EXISTS `tbl_leave_balances` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `employee_id` int(11) NOT NULL,
        `leave_type_id` int(11) NOT NULL,
        `year` int(4) NOT NULL,
        `total_days` int(11) NOT NULL DEFAULT 0,
        `used_days` decimal(5,1) NOT NULL DEFAULT 0.0,
        `pending_days` decimal(5,1) NOT NULL DEFAULT 0.0,
        `carried_over_days` decimal(5,1) NOT NULL DEFAULT 0.0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `employee_leave_year` (`employee_id`,`leave_type_id`,`year`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

// Execute the SQL statements
foreach ($sql as $query) {
    executeSql($query);
}

// Insert or update leave types
$leaveTypes = [
    ['ANNUAL', 'Annual Leave', 'Paid annual leave', 1, 1, NULL, 0],
    ['CASUAL', 'Casual Leave', 'Casual leave for personal reasons', 1, 1, NULL, 0],
    ['MEDICAL', 'Medical Leave', 'Sick leave with medical certificate', 1, 1, NULL, 0],
    ['MATERNITY', 'Maternity Leave', 'Maternity leave for female employees', 1, 1, 'F', 0],
    ['PATERNITY', 'Paternity Leave', 'Paternity leave for new fathers', 1, 1, 'M', 0],
    ['STUDY', 'Study Leave', 'Leave for academic studies', 1, 1, NULL, 0],
    ['SABBATICAL', 'Sabbatical Leave', 'Sabbatical leave for research', 1, 1, NULL, 2],
    ['DUTY', 'Duty Leave', 'Leave for official duties', 1, 1, NULL, 0],
    ['NOPAY', 'No-Pay Leave', 'Unpaid leave', 1, 1, NULL, 0],
    ['SPECIAL', 'Special Leave', 'Special leave with approval', 1, 1, NULL, 0]
];

foreach ($leaveTypes as $type) {
    $code = $type[0];
    $name = $type[1];
    $desc = $type[2];
    $isActive = $type[3];
    $requiresApproval = $type[4];
    $genderSpecific = $type[5] ? "'$type[5]'" : 'NULL';
    $minLevel = $type[6];
    
    // Check if leave type exists
    $stmt = $dbh->prepare("SELECT id FROM tblleavetype WHERE code = ? OR LeaveType = ?");
    $stmt->execute([$code, $name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Update existing leave type
        $query = "UPDATE tblleavetype SET 
                 code = :code, 
                 Description = :desc, 
                 is_active = :is_active,
                 requires_approval = :requires_approval,
                 is_gender_specific = $genderSpecific,
                 min_position_level = :min_level
                 WHERE id = :id";
        $stmt = $dbh->prepare($query);
        $stmt->execute([
            ':code' => $code,
            ':desc' => $desc,
            ':is_active' => $isActive,
            ':requires_approval' => $requiresApproval,
            ':min_level' => $minLevel,
            ':id' => $result['id']
        ]);
        $leaveTypeId = $result['id'];
    } else {
        // Insert new leave type
        $query = "INSERT INTO tblleavetype 
                 (code, LeaveType, Description, is_active, requires_approval, is_gender_specific, min_position_level) 
                 VALUES (?, ?, ?, ?, ?, $genderSpecific, ?)";
        $stmt = $dbh->prepare($query);
        $stmt->execute([$code, $name, $desc, $isActive, $requiresApproval, $minLevel]);
        $leaveTypeId = $dbh->lastInsertId();
    }
    
    // Set leave limits for academic and non-academic staff
    $limits = [
        // Format: [leave_code, academic_days, non_academic_days, can_carry_forward, max_carry_forward]
        ['ANNUAL', 30, 30, 1, 10],
        ['CASUAL', 7, 7, 0, 0],
        ['MEDICAL', 20, 20, 0, 0],
        ['MATERNITY', 84, 84, 0, 0],
        ['PATERNITY', 3, 0, 0, 0]
    ];
    
    foreach ($limits as $limit) {
        if ($code === $limit[0]) {
            // Academic staff
            setLeaveLimit('academic', $leaveTypeId, $limit[1], $limit[3], $limit[4]);
            // Non-academic staff
            setLeaveLimit('non_academic', $leaveTypeId, $limit[2], $limit[3], $limit[4]);
            break;
        }
    }
}

// Re-enable foreign key checks
executeSql("SET FOREIGN_KEY_CHECKS = 1");

echo "Database initialization completed successfully!";

/**
 * Execute SQL query and handle errors
 */
function executeSql($sql) {
    global $dbh;
    try {
        $dbh->exec($sql);
    } catch (PDOException $e) {
        die("Error executing SQL: " . $e->getMessage() . "<br>SQL: " . $sql);
    }
}

/**
 * Set leave limit for a role
 */
function setLeaveLimit($role, $leaveTypeId, $days, $canCarryForward = 0, $maxCarryForward = 0) {
    global $dbh;
    
    $query = "INSERT INTO tbl_leave_limits 
             (role, leave_type_id, days_per_year, is_carry_forward, max_carry_forward_days)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE 
             days_per_year = VALUES(days_per_year),
             is_carry_forward = VALUES(is_carry_forward),
             max_carry_forward_days = VALUES(max_carry_forward_days)";
    
    $stmt = $dbh->prepare($query);
    $stmt->execute([$role, $leaveTypeId, $days, $canCarryForward, $maxCarryForward]);
}
