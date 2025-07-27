<?php
session_start();
require_once('includes/config.php');
require_once('includes/LeaveManager.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['emplogin']) || empty($_SESSION['emplogin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get employee ID from session
$employeeId = $_SESSION['eid'] ?? 0;

// Get leave type ID from request
$leaveTypeId = filter_input(INPUT_GET, 'leave_type_id', FILTER_VALIDATE_INT);
$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT, [
    'options' => [
        'default' => date('Y'),
        'min_range' => 2000,
        'max_range' => 2100
    ]
]);

if (!$leaveTypeId) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid leave type']);
    exit;
}

try {
    // Initialize LeaveManager
    $leaveManager = new LeaveManager($dbh);
    
    // Get leave balance
    $balance = $leaveManager->getLeaveBalance($employeeId, $leaveTypeId, $year);
    
    // Calculate available days
    $availableDays = $balance['total_days'] - $balance['used_days'] - $balance['pending_days'];
    
    // Return balance information
    echo json_encode([
        'success' => true,
        'data' => [
            'total_days' => (float)$balance['total_days'],
            'used_days' => (float)$balance['used_days'],
            'pending_days' => (float)$balance['pending_days'],
            'available_days' => $availableDays,
            'carried_over_days' => (float)$balance['carried_over_days'],
            'year' => (int)$balance['year']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to get leave balance',
        'message' => $e->getMessage()
    ]);
}
