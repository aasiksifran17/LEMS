<?php
/**
 * LeaveManager Class
 * 
 * Handles all leave-related operations including leave requests, approvals,
 * balance calculations, and validations.
 */
class LeaveManager {
    private $dbh;
    private $currentYear;
    
    /**
     * Constructor
     * 
     * @param PDO $dbh Database connection handle
     */
    public function __construct($dbh) {
        $this->dbh = $dbh;
        $this->currentYear = date('Y');
    }
    
    /**
     * Get available leave types for an employee
     * 
     * @param int $employeeId Employee ID
     * @return array Array of available leave types with limits
     */
    public function getAvailableLeaveTypes($employeeId) {
        // Get employee details
        $employee = $this->getEmployeeDetails($employeeId);
        if (!$employee) {
            throw new Exception('Employee not found');
        }
        
        // Get position level
        $positionLevel = $this->getPositionLevel($employee['position']);
        
        // Get available leave types
        $query = "
            SELECT lt.*, rll.days_per_year, rll.is_carry_forward, rll.max_carry_forward_days
            FROM tblleavetype lt
            JOIN tbl_leave_limits rll ON lt.id = rll.leave_type_id
            WHERE rll.role = :role
            AND lt.is_active = 1
            AND (lt.is_gender_specific IS NULL OR lt.is_gender_specific = :gender)
            AND (lt.min_position_level = 0 OR :position_level >= lt.min_position_level)
            ORDER BY lt.LeaveType
        ";
        
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([
            ':role' => $employee['role'],
            ':gender' => $employee['gender'],
            ':position_level' => $positionLevel
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get leave balance for an employee
     * 
     * @param int $employeeId Employee ID
     * @param int $leaveTypeId Leave Type ID
     * @param int $year Year (optional, defaults to current year)
     * @return array Leave balance information
     */
    public function getLeaveBalance($employeeId, $leaveTypeId, $year = null) {
        if ($year === null) {
            $year = $this->currentYear;
        }
        
        // Get or create leave balance record
        $query = "
            SELECT * FROM tbl_leave_balances 
            WHERE employee_id = ? AND leave_type_id = ? AND year = ?
            LIMIT 1
        ";
        
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([$employeeId, $leaveTypeId, $year]);
        $balance = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If no balance record exists, create one
        if (!$balance) {
            return $this->initializeLeaveBalance($employeeId, $leaveTypeId, $year);
        }
        
        return $balance;
    }
    
    /**
     * Submit a leave request
     * 
     * @param array $data Leave request data
     * @return int|bool New leave request ID or false on failure
     */
    public function submitLeaveRequest($data) {
        $this->dbh->beginTransaction();
        
        try {
            // Validate leave request
            $this->validateLeaveRequest($data);
            
            // Calculate working days
            $workingDays = $this->calculateWorkingDays($data['from_date'], $data['to_date']);
            
            // Insert leave request
            $query = "
                INSERT INTO tblleaves 
                (LeaveType, ToDate, FromDate, Description, Status, IsRead, PostingDate, AdminRemark, AdminRemarkDate, 
                 AdminStatus, AdminStatusDate, empid, NumOfDays, IsHalfDay, LeaveTypeId, LeaveStatus, 
                 LeaveApprovedDate, LeaveApprovedBy, LeaveRejectDate, LeaveRejectBy, LeaveCancelDate, 
                 LeaveCancelBy, LeaveCancelReason, LeaveCancelStatus, LeaveCancelApprovedBy, LeaveCancelApprovedDate)
                VALUES (?, ?, ?, ?, 0, 0, NOW(), NULL, NULL, 
                        NULL, NULL, ?, ?, 0, ?, 0, 
                        NULL, NULL, NULL, NULL, NULL, 
                        NULL, NULL, 0, NULL, NULL)";
            
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                $data['leave_type_name'],
                $data['to_date'],
                $data['from_date'],
                $data['description'],
                $data['employee_id'],
                $workingDays,
                $data['leave_type_id']
            ]);
            
            $leaveId = $this->dbh->lastInsertId();
            
            // Update pending days in leave balance
            $this->updateLeaveBalance(
                $data['employee_id'],
                $data['leave_type_id'],
                $workingDays,
                'pending'
            );
            
            $this->dbh->commit();
            return $leaveId;
            
        } catch (Exception $e) {
            $this->dbh->rollBack();
            throw $e;
        }
    }
    
    /**
     * Process leave approval/rejection
     * 
     * @param int $leaveId Leave request ID
     * @param string $status 'Approved' or 'Rejected'
     * @param string $remarks Admin remarks
     * @param int $adminId Admin user ID
     * @return bool True on success
     */
    public function processLeave($leaveId, $status, $remarks, $adminId) {
        $this->dbh->beginTransaction();
        
        try {
            // Get leave request details
            $query = "SELECT * FROM tblleaves WHERE id = ?";
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([$leaveId]);
            $leave = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$leave) {
                throw new Exception('Leave request not found');
            }
            
            // Update leave request status
            $updateQuery = "
                UPDATE tblleaves 
                SET Status = 1,
                    AdminRemark = ?,
                    AdminRemarkDate = NOW(),
                    AdminStatus = ?,
                    AdminStatusDate = NOW(),
                    LeaveStatus = ?,
                    LeaveApprovedDate = NOW(),
                    LeaveApprovedBy = ?
                WHERE id = ?
            ";
            
            $stmt = $this->dbh->prepare($updateQuery);
            $stmt->execute([
                $remarks,
                $status,
                $status === 'Approved' ? 1 : 2, // 1=Approved, 2=Rejected
                $adminId,
                $leaveId
            ]);
            
            // If approved, update leave balance
            if ($status === 'Approved') {
                $this->updateLeaveBalance(
                    $leave['empid'],
                    $leave['LeaveTypeId'],
                    -$leave['NumOfDays'], // Remove from pending
                    'pending'
                );
                
                $this->updateLeaveBalance(
                    $leave['empid'],
                    $leave['LeaveTypeId'],
                    $leave['NumOfDays'], // Add to used
                    'used'
                );
            } else {
                // If rejected, remove from pending
                $this->updateLeaveBalance(
                    $leave['empid'],
                    $leave['LeaveTypeId'],
                    -$leave['NumOfDays'],
                    'pending'
                );
            }
            
            $this->dbh->commit();
            return true;
            
        } catch (Exception $e) {
            $this->dbh->rollBack();
            throw $e;
        }
    }
    
    /**
     * Calculate working days between two dates (excluding weekends)
     * 
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return int Number of working days
     */
    public function calculateWorkingDays($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day'); // Include end date in calculation
        
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
        
        $workingDays = 0;
        
        foreach ($period as $date) {
            // Check if it's a weekend (5 = Saturday, 6 = Sunday)
            if ($date->format('N') < 6) {
                $workingDays++;
            }
        }
        
        return $workingDays;
    }
    
    /**
     * Initialize leave balance for an employee
     * 
     * @param int $employeeId Employee ID
     * @param int $leaveTypeId Leave Type ID
     * @param int $year Year
     * @return array Leave balance information
     */
    private function initializeLeaveBalance($employeeId, $leaveTypeId, $year) {
        // Get employee details
        $employee = $this->getEmployeeDetails($employeeId);
        if (!$employee) {
            throw new Exception('Employee not found');
        }
        
        // Get leave type details
        $query = "
            SELECT rll.days_per_year, rll.is_carry_forward, rll.max_carry_forward_days
            FROM tbl_leave_limits rll
            WHERE rll.leave_type_id = ? AND rll.role = ?
            LIMIT 1
        ";
        
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([$leaveTypeId, $employee['role']]);
        $leaveLimit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$leaveLimit) {
            throw new Exception('Leave type not available for this role');
        }
        
        // Check for carry over from previous year
        $carriedOverDays = 0;
        if ($leaveLimit['is_carry_forward'] && $year > date('Y')) {
            $prevYearBalance = $this->getLeaveBalance($employeeId, $leaveTypeId, $year - 1);
            if ($prevYearBalance) {
                $remainingDays = $prevYearBalance['total_days'] - $prevYearBalance['used_days'];
                $carriedOverDays = min($remainingDays, $leaveLimit['max_carry_forward_days']);
            }
        }
        
        // Calculate total days (yearly allowance + carried over)
        $totalDays = $leaveLimit['days_per_year'] + $carriedOverDays;
        
        // Insert new balance record
        $query = "
            INSERT INTO tbl_leave_balances 
            (employee_id, leave_type_id, year, total_days, used_days, pending_days, carried_over_days)
            VALUES (?, ?, ?, ?, 0, 0, ?)
            ON DUPLICATE KEY UPDATE 
                total_days = VALUES(total_days),
                carried_over_days = VALUES(carried_over_days)
        ";
        
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([
            $employeeId,
            $leaveTypeId,
            $year,
            $totalDays,
            $carriedOverDays
        ]);
        
        return [
            'employee_id' => $employeeId,
            'leave_type_id' => $leaveTypeId,
            'year' => $year,
            'total_days' => $totalDays,
            'used_days' => 0,
            'pending_days' => 0,
            'carried_over_days' => $carriedOverDays
        ];
    }
    
    /**
     * Update leave balance
     * 
     * @param int $employeeId Employee ID
     * @param int $leaveTypeId Leave Type ID
     * @param float $days Number of days to add/remove
     * @param string $type 'used' or 'pending'
     * @return bool True on success
     */
    private function updateLeaveBalance($employeeId, $leaveTypeId, $days, $type) {
        if (!in_array($type, ['used', 'pending'])) {
            throw new InvalidArgumentException('Invalid balance type. Must be "used" or "pending"');
        }
        
        $field = $type . '_days';
        
        $query = "
            UPDATE tbl_leave_balances 
            SET $field = $field + :days
            WHERE employee_id = :employee_id 
            AND leave_type_id = :leave_type_id 
            AND year = :year
        ";
        
        $stmt = $this->dbh->prepare($query);
        return $stmt->execute([
            ':days' => $days,
            ':employee_id' => $employeeId,
            ':leave_type_id' => $leaveTypeId,
            ':year' => $this->currentYear
        ]);
    }
    
    /**
     * Validate leave request
     * 
     * @param array $data Leave request data
     * @throws Exception If validation fails
     */
    private function validateLeaveRequest($data) {
        // Check if dates are valid
        $fromDate = new DateTime($data['from_date']);
        $toDate = new DateTime($data['to_date']);
        
        if ($fromDate > $toDate) {
            throw new Exception('From date cannot be after to date');
        }
        
        // Calculate working days
        $workingDays = $this->calculateWorkingDays($data['from_date'], $data['to_date']);
        
        // Get leave type details to check for casual leave
        $query = "SELECT LeaveType FROM tblleavetype WHERE id = ?";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([$data['leave_type_id']]);
        $leaveType = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get employee details for validation
        $employee = $this->getEmployeeDetails($data['employee_id']);
        if (!$employee) {
            throw new Exception('Employee not found');
        }
        
        $leaveTypeName = strtolower($leaveType['LeaveType']);
        
        // Define leave type validation rules
        $leaveRules = [
            'annual' => [
                'max_days' => 30,
                'message' => 'Annual leave cannot exceed 30 days',
                'validation' => function() { return true; }
            ],
            'casual' => [
                'max_days' => 30,
                'message' => 'Casual leave cannot exceed 30 days',
                'validation' => function() { return true; }
            ],
            'medical' => [
                'max_days' => 20,
                'message' => 'Medical leave cannot exceed 20 days',
                'validation' => function() { return true; }
            ],
            'maternity' => [
                'max_days' => 84,
                'message' => 'Maternity leave cannot exceed 84 days',
                'validation' => function() use ($employee) {
                    if (strtolower($employee['gender']) !== 'female') {
                        throw new Exception('Maternity leave is only available for female employees');
                    }
                    return true;
                }
            ],
            'paternity' => [
                'max_days' => 3,
                'message' => 'Paternity leave cannot exceed 3 days',
                'validation' => function() use ($employee) {
                    if (strtolower($employee['gender']) !== 'male') {
                        throw new Exception('Paternity leave is only available for male employees');
                    }
                    return true;
                }
            ],
            'study' => [
                'max_days' => 365, // Maximum allowed, but requires special approval
                'message' => 'Study leave requires special approval',
                'validation' => function() use ($workingDays) {
                    // Log for approval workflow
                    error_log("Study leave request for approval: {$workingDays} days requested");
                    return true;
                }
            ],
            'sabbatical' => [
                'max_days' => 365, // Typically 1 year
                'message' => 'Sabbatical leave is only available for senior lecturers and professors',
                'validation' => function() use ($employee) {
                    $position = strtolower($employee['position'] ?? '');
                    if (strpos($position, 'senior lecturer') === false && 
                        strpos($position, 'professor') === false) {
                        throw new Exception('Sabbatical leave is only available for senior lecturers and professors');
                    }
                    return true;
                }
            ],
            'duty' => [
                'max_days' => 90, // Example limit
                'message' => 'Duty leave cannot exceed 90 days',
                'validation' => function() { return true; }
            ],
            'no pay' => [
                'max_days' => 365, // Maximum 1 year
                'message' => 'No pay leave cannot exceed 1 year',
                'validation' => function() { return true; }
            ]
        ];
        
        // Apply leave type specific validations
        $appliedRule = false;
        foreach ($leaveRules as $type => $rule) {
            if (stripos($leaveTypeName, $type) !== false) {
                // Check if working days exceed maximum allowed
                if ($workingDays > $rule['max_days']) {
                    throw new Exception($rule['message']);
                }
                
                // Apply any additional validation rules
                $rule['validation']();
                
                $appliedRule = true;
                break;
            }
        }
        
        // If no specific rule was applied, use default validation
        if (!$appliedRule) {
            // Default validation for unspecified leave types
            if ($workingDays > 30) { // Default maximum of 30 days
                throw new Exception("Leave cannot exceed 30 days");
            }
        }
        
        // Check leave balance
        $balance = $this->getLeaveBalance($data['employee_id'], $data['leave_type_id']);
        $availableDays = $balance['total_days'] - $balance['used_days'] - $balance['pending_days'];
        
        if ($workingDays > $availableDays) {
            throw new Exception('Insufficient leave balance');
        }
        
        // Check for overlapping leave requests
        $query = "
            SELECT COUNT(*) as count 
            FROM tblleaves 
            WHERE empid = ? 
            AND LeaveTypeId = ? 
            AND (
                (FromDate BETWEEN ? AND ?) 
                OR (ToDate BETWEEN ? AND ?)
                OR (? BETWEEN FromDate AND ToDate)
                OR (? BETWEEN FromDate AND ToDate)
            )
            AND id != ?
            AND Status = 0
        ";
        
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([
            $data['employee_id'],
            $data['leave_type_id'],
            $data['from_date'],
            $data['to_date'],
            $data['from_date'],
            $data['to_date'],
            $data['from_date'],
            $data['to_date'],
            $data['id'] ?? 0
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            throw new Exception('Leave request overlaps with an existing leave period');
        }
    }
    
    /**
     * Get employee details
     * 
     * @param int $employeeId Employee ID
     * @return array|bool Employee details or false if not found
     */
    private function getEmployeeDetails($employeeId) {
        $query = "SELECT id, EmpId, FirstName, LastName, role, gender, position 
                 FROM tblemployees 
                 WHERE id = ? AND Status = 1";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([$employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get position level
     * 
     * @param string $position Position name
     * @return int Position level (0 = lowest)
     */
    private function getPositionLevel($position) {
        $query = "SELECT level FROM tbl_position_levels WHERE position_name = ?";
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([$position]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['level'] : 0;
    }
}
