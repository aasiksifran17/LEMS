/**
 * Leave Application Form Handler - Enhanced with Auto Date Detection
 */

$(document).ready(function() {
    // Initialize date pickers with enhanced options
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        startDate: 'today',
        daysOfWeekDisabled: [0, 6], // Disable weekends
        todayBtn: 'linked',
        clearBtn: true,
        orientation: 'bottom auto'
    });

    // Set default from date to today
    const today = new Date();
    $('input[name="fromdate"]').datepicker('setDate', today);

    // Set default to date to next working day
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    // If tomorrow is Saturday, set to Monday
    if (tomorrow.getDay() === 6) {
        tomorrow.setDate(tomorrow.getDate() + 2);
    } 
    // If tomorrow is Sunday, set to Monday
    else if (tomorrow.getDay() === 0) {
        tomorrow.setDate(tomorrow.getDate() + 1);
    }
    $('input[name="todate"]').datepicker('setDate', tomorrow);

    // Calculate working days on page load
    calculateWorkingDays();

    // Leave type validation rules
    const leaveTypeLimits = {
        'Annual Leave': { max: 30 },
        'Casual Leave': { max: 30 },
        'Medical Leave': { max: 20 },
        'Maternity Leave': { max: 84, gender: 'Female' },
        'Paternity Leave': { max: 3, gender: 'Male' },
        'Study Leave': { max: 365, approval: true },
        'Sabbatical Leave': { max: 365, staff: 'Senior Staff' },
        'Duty Leave': { max: 90 },
        'No Pay Leave': { max: 365 }
    };

    function getSelectedLeaveType() {
        return $('select[name="leavetype"]').val();
    }

    function getUserGender() {
        // Replace with actual gender detection if available
        return $('#user-gender').val() || 'Male';
    }
    function getUserStaffType() {
        // Replace with actual staff type detection if available
        return $('#user-stafftype').val() || 'Staff';
    }

    function showLeaveError(msg) {
        $('#leave-error').remove();
        $('<div id="leave-error" style="color:red;margin:10px 0;">'+msg+'</div>').insertBefore('form#example-form');
        $('#apply').prop('disabled', true);
    }
    function clearLeaveError() {
        $('#leave-error').remove();
        $('#apply').prop('disabled', false);
    }

    function validateLeaveDays() {
        clearLeaveError();
        const leaveType = getSelectedLeaveType();
        if (!leaveType) return;
        const limits = leaveTypeLimits[leaveType];
        if (!limits) return;
        const from = $('input[name="fromdate"]').val();
        const to = $('input[name="todate"]').val();
        if (!from || !to) return;
        const fromDate = new Date(from);
        const toDate = new Date(to);
        let days = 0;
        let current = new Date(fromDate);
        while (current <= toDate) {
            // Exclude weekends
            if (current.getDay() !== 0 && current.getDay() !== 6) days++;
            current.setDate(current.getDate() + 1);
        }
        // Gender rule
        if (limits.gender && limits.gender !== getUserGender()) {
            showLeaveError(leaveType + ' is only allowed for ' + limits.gender + 's.');
            return false;
        }
        // Staff rule
        if (limits.staff && limits.staff !== getUserStaffType()) {
            showLeaveError(leaveType + ' is only allowed for ' + limits.staff + '.');
            return false;
        }
        // Max days rule
        if (days > limits.max) {
            showLeaveError(leaveType + ' allows a maximum of ' + limits.max + ' days. You selected ' + days + ' days.');
            return false;
        }
        // Approval rule (for Study, Sabbatical, No Pay, etc. - can be extended)
        // If you want to add approval logic, do it here
        clearLeaveError();
        return true;
    }

    $('select[name="leavetype"], input[name="fromdate"], input[name="todate"]').on('change', function() {
        validateLeaveDays();
    });

    $('form#example-form').on('submit', function(e) {
        if (!validateLeaveDays()) {
            e.preventDefault();
        }
    });

    // Leave type change handler
    $('select[name="leavetype"]').on('change', function() {
        updateLeaveBalance();
        updateDateRestrictions();
    });

    // Date change handlers
    $('input[name="fromdate"]').on('changeDate', function(e) {
        const fromDate = $(this).datepicker('getDate');
        const toDate = $('input[name="todate"]').datepicker('getDate');
        
        // If from date is after to date, update to date to be same as from date
        if (toDate && fromDate > toDate) {
            $('input[name="todate"]').datepicker('setDate', fromDate);
        }
        
        // Update working days and restrictions
        calculateWorkingDays();
        updateLeaveBalance();
        updateDateRestrictions();
    });

    $('input[name="todate"]').on('changeDate', function() {
        calculateWorkingDays();
        updateLeaveBalance();
    });

    // Form submission handler
    $('#leaveForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
    });

    // Initialize form
    updateLeaveBalance();
    updateDateRestrictions();
});

/**
 * Calculate working days between two dates (excluding weekends)
 */
function calculateWorkingDays() {
    const fromDate = $('input[name="fromdate"]').datepicker('getDate');
    const toDate = $('input[name="todate"]').datepicker('getDate');
    
    if (!fromDate || !toDate) {
        $('#numofdays').val(0);
        return 0;
    }

    // If from date is after to date, swap them
    if (fromDate > toDate) {
        $('input[name="fromdate"]').datepicker('setDate', toDate);
        $('input[name="todate"]').datepicker('setDate', fromDate);
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
    return workingDays;
}

/**
 * Update date restrictions based on leave type
 */
function updateDateRestrictions() {
    const leaveType = $('select[name="leavetype"] option:selected').text().toLowerCase();
    const fromDateInput = $('input[name="fromdate"]');
    const toDateInput = $('input[name="todate"]');
    
    // Reset all date restrictions first
    fromDateInput.datepicker('setStartDate', 'today');
    toDateInput.datepicker('setStartDate', 'today');
    
    // Get current dates
    const fromDate = fromDateInput.datepicker('getDate');
    const toDate = toDateInput.datepicker('getDate');
    
    // Set default max days based on leave type
    let maxDays = 30; // Default maximum days
    
    if (leaveType.includes('casual')) {
        maxDays = 30;
    } else if (leaveType.includes('medical')) {
        maxDays = 20;
    } else if (leaveType.includes('maternity')) {
        maxDays = 84;
    } else if (leaveType.includes('paternity')) {
        maxDays = 3;
    } else if (leaveType.includes('study') || leaveType.includes('sabbatical')) {
        maxDays = 365;
    } else if (leaveType.includes('duty')) {
        maxDays = 90;
    } else if (leaveType.includes('no pay')) {
        maxDays = 365;
    }
    
    // Update date restrictions
    if (fromDate) {
        const maxDate = new Date(fromDate);
        maxDate.setDate(fromDate.getDate() + maxDays - 1);
        toDateInput.datepicker('setEndDate', maxDate);
    }
}

/**
 * Update leave balance information via AJAX
 */
function updateLeaveBalance() {
    const leaveTypeId = $('select[name="leavetype"]').val();
    const fromDate = $('input[name="fromdate"]').datepicker('getDate');
    const year = fromDate ? fromDate.getFullYear() : new Date().getFullYear();
    
    if (!leaveTypeId) {
        return;
    }

    // Show loading state
    const balanceContainer = $('#balance-container');
    if (balanceContainer.length === 0) {
        $('<div id="balance-container" class="mt-3 p-3 border rounded bg-light">' +
          '<div id="balance-loading" style="display: none;">' +
          '<i class="fa fa-spinner fa-spin"></i> Loading leave balance...' +
          '</div>' +
          '<div id="balance-info"></div>' +
          '</div>').insertAfter('textarea[name="description"]');
    }
    
    $('#balance-loading').show();
    $('#balance-info').hide();
    
    // Make AJAX request to get leave balance
    $.ajax({
        url: 'get-leave-balance.php',
        type: 'GET',
        dataType: 'json',
        data: {
            leave_type_id: leaveTypeId,
            year: year
        },
        success: function(response) {
            if (response.status === 'success') {
                const availableDays = response.data.total_days - response.data.used_days - response.data.pending_days;
                const daysRequested = parseInt($('#numofdays').val()) || 0;
                
                let balanceHtml = `
                    <div class="alert alert-info">
                        <strong>Leave Balance:</strong><br>
                        Total Days: ${response.data.total_days}<br>
                        Used Days: ${response.data.used_days}<br>
                        Pending Days: ${response.data.pending_days}<br>
                        <strong>Available: ${availableDays} days</strong>
                    </div>
                `;
                
                if (daysRequested > availableDays) {
                    balanceHtml += `
                        <div class="alert alert-warning">
                            Warning: You are requesting more days than available.
                        </div>
                    `;
                }
                
                $('#balance-info').html(balanceHtml);
            } else {
                $('#balance-info').html(`
                    <div class="alert alert-danger">
                        Error: ${response.message}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading leave balance:', error);
            $('#balance-info').html(`
                <div class="alert alert-danger">
                    Error loading leave balance. Please try again.
                </div>
            `);
        },
        complete: function() {
            $('#balance-loading').hide();
            $('#balance-info').show();
        }
    });
}

/**
 * Validate the leave application form
 */
function validateForm() {
    let isValid = true;
    
    // Reset error messages
    $('.form-group').removeClass('has-error');
    $('.help-block').remove();
    
    // Validate leave type
    if (!$('select[name="leavetype"]').val()) {
        showError('Please select a leave type', $('select[name="leavetype"]').closest('.form-group'));
        isValid = false;
    }
    
    // Validate dates
    const fromDate = $('input[name="fromdate"]').datepicker('getDate');
    const toDate = $('input[name="todate"]').datepicker('getDate');
    
    if (!fromDate) {
        showError('Please select a from date', $('input[name="fromdate"]').closest('.form-group'));
        isValid = false;
    }
    
    if (!toDate) {
        showError('Please select a to date', $('input[name="todate"]').closest('.form-group'));
        isValid = false;
    }
    
    if (fromDate && toDate && fromDate > toDate) {
        showError('To date cannot be before from date', $('input[name="todate"]').closest('.form-group'));
        isValid = false;
    }
    
    // Validate description
    if (!$('textarea[name="description"]').val().trim()) {
        showError('Please enter a description', $('textarea[name="description"]').closest('.form-group'));
        isValid = false;
    }
    
    return isValid;
}

/**
 * Display an error message for a form field
 */
function showError(message, fieldElement) {
    fieldElement.addClass('has-error');
    fieldElement.append(`<span class="help-block text-danger">${message}</span>`);
    fieldElement.find('input, select, textarea').focus();
}
