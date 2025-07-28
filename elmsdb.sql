SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE admin (
  id int(11) NOT NULL,
  UserName varchar(100) NOT NULL,
  Password varchar(100) NOT NULL,
  updationDate timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table admin
INSERT INTO admin (id, UserName, Password, updationDate) VALUES
(1, 'admin', 'admin', '2025-05-22 05:00:54');

-- --------------------------------------------------------

-- Table structure for table tbldepartments
CREATE TABLE tbldepartments (
  id int(11) NOT NULL,
  DepartmentName varchar(150) DEFAULT NULL,
  DepartmentShortName varchar(100) DEFAULT NULL,
  DepartmentCode varchar(50) DEFAULT NULL,
  FacultyCode varchar(50) DEFAULT NULL,
  CreationDate timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table tbldepartments
INSERT INTO tbldepartments (id, DepartmentName, DepartmentShortName, DepartmentCode, FacultyCode, CreationDate) VALUES
(1, 'Physical Science', 'ASP', 'APS1','A1', '2023-08-31 14:50:20'),
(2, 'Information Technology', 'IT', 'AIT1','A1', '2023-08-31 14:50:56'),
(3, 'Biological Science', 'ASB', 'ABS1','A1', '2023-08-31 14:51:26'),
(4, 'ADMIN', 'Admin', 'ADMN01','NULL','2023-09-01 11:35:50'),
(5, 'Information Communication Technology', 'BICT', 'BICT1','T1', '2023-08-31 14:51:26'),
(6, 'Banking and Finance', 'BF', 'BF1','BS1', '2023-08-31 14:51:26'),
(7, 'Project Management', 'PM', 'PM1','BS1', '2023-08-31 14:51:26');

-- --------------------------------------------------------

-- Table structure for table tblemployees
CREATE TABLE tblemployees (
  id int(11) NOT NULL,
  EmpId varchar(100) NOT NULL,
  FirstName varchar(150) DEFAULT NULL,
  LastName varchar(150) DEFAULT NULL,
  EmailId varchar(200) DEFAULT NULL,
  Password varchar(180) DEFAULT NULL,
  Gender varchar(100) DEFAULT NULL,
  DepartmentCode varchar(255) DEFAULT NULL,
  Phonenumber char(11) DEFAULT NULL,
  Status int(1) DEFAULT NULL,
  RegDate timestamp NULL DEFAULT current_timestamp(),
  Faculty varchar(100) DEFAULT NULL,
  employeetype varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --- LEMS EXTENSION: Add columns and tables for LeaveManager compatibility ---
-- Add missing columns to tblemployees table
ALTER TABLE tblemployees 
ADD COLUMN role VARCHAR(50) DEFAULT 'employee' AFTER EmailId,
ADD COLUMN gender VARCHAR(10) DEFAULT 'Male' AFTER role,
ADD COLUMN position VARCHAR(100) DEFAULT 'Staff' AFTER gender;

-- Create position levels table if not exists
CREATE TABLE IF NOT EXISTS tbl_position_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL,
    level INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create leave limits table if not exists
CREATE TABLE IF NOT EXISTS tbl_leave_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    leave_type_id INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    days_per_year INT NOT NULL,
    is_carry_forward TINYINT(1) DEFAULT 0,
    max_carry_forward_days INT DEFAULT 0,
    FOREIGN KEY (leave_type_id) REFERENCES tblleavetype(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert default position levels if not exists
INSERT IGNORE INTO tbl_position_levels (position_name, level) VALUES
('Staff', 1),
('Senior Staff', 2),
('Manager', 3),
('Director', 4);

-- Insert default leave limits for employee role if not exists
INSERT IGNORE INTO tbl_leave_limits (leave_type_id, role, days_per_year, is_carry_forward, max_carry_forward_days)
SELECT id, 'employee', 
    CASE 
        WHEN LeaveType = 'Annual Leave' THEN 30
        WHEN LeaveType = 'Casual Leave' THEN 30
        WHEN LeaveType = 'Medical Leave' THEN 20
        WHEN LeaveType = 'Maternity Leave' THEN 84
        WHEN LeaveType = 'Paternity Leave' THEN 3
        WHEN LeaveType = 'Study Leave' THEN 365
        WHEN LeaveType = 'Sabbatical Leave' THEN 365
        WHEN LeaveType = 'Duty Leave' THEN 90
        WHEN LeaveType = 'No Pay Leave' THEN 365
        ELSE 10 
    END,
    1,  -- is_carry_forward
    5   -- max_carry_forward_days
FROM tblleavetype
WHERE id NOT IN (SELECT leave_type_id FROM tbl_leave_limits WHERE role = 'employee');

-- Update existing employees with default values if needed
UPDATE tblemployees SET 
    role = 'employee',
    gender = COALESCE(gender, 'Male'),
    position = COALESCE(position, 'Staff')
WHERE role IS NULL OR gender IS NULL OR position IS NULL;
-- --- END LEMS EXTENSION ---

-- Dumping data for table tblemployees
INSERT INTO tblemployees (id, EmpId, FirstName, LastName, EmailId, Password, Gender, DepartmentCode, Phonenumber, Status, RegDate, Faculty, employeetype) VALUES
(6, '2020/ASP/93', 'Fatima', 'Zhuzana', 'fatimazhuzana123@gmail.com', '25f9e794323b453885f5181f1b624d0b', 'Female', 'Human Resource', '025796416', 1, '2025-05-22 05:35:57', NULL, NULL);

-- --------------------------------------------------------

-- Table structure for table tblfaculty
CREATE TABLE tblfaculty (
  id int(11) NOT NULL,
  FacultyName varchar(150) DEFAULT NULL,
  FacultyShortName varchar(100) DEFAULT NULL,
  FacultyCode varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table tblfaculty
INSERT INTO tblfaculty (id, FacultyName, FacultyShortName, FacultyCode) VALUES
(1, 'Applied Science', 'FAS', 'A1'),
(2, 'Business Studies', 'FOBS', 'BS1'),
(3, 'Technological Studies', 'FOTS', 'T1');

-- --------------------------------------------------------

-- Table structure for table tblleaves
CREATE TABLE tblleaves (
  id int(11) NOT NULL,
  LeaveType varchar(110) DEFAULT NULL,
  ToDate varchar(120) DEFAULT NULL,
  FromDate varchar(120) DEFAULT NULL,
  Description mediumtext DEFAULT NULL,
  PostingDate timestamp NULL DEFAULT current_timestamp(),
  AdminRemark mediumtext DEFAULT NULL,
  AdminRemarkDate varchar(120) DEFAULT NULL,
  Status int(1) DEFAULT NULL,
  IsRead int(1) DEFAULT NULL,
  empid int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table tblleaves
INSERT INTO tblleaves (id, LeaveType, ToDate, FromDate, Description, PostingDate, AdminRemark, AdminRemarkDate, Status, IsRead, empid) VALUES
(11, 'Casual Leaves', '17/09/2023', '10/09/2023', 'I need leave to visit my home town. ', '2023-08-31 15:06:21', 'Approved', '2023-08-31 20:39:40 ', 1, 1, 1),
(12, 'Casual Leaves', '15/09/2023', '09/09/2023', 'Need casual leaves for some personal work.', '2023-09-01 11:42:40', 'Leave approved', '2023-09-01 17:13:20 ', 1, 1, 5);

-- --------------------------------------------------------

-- Table structure for table tblleavetype
CREATE TABLE tblleavetype (
  id int(11) NOT NULL,
  LeaveType varchar(200) DEFAULT NULL,
  Description mediumtext DEFAULT NULL,
  CreationDate timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table tblleavetype
INSERT INTO tblleavetype (id, LeaveType, Description, CreationDate) VALUES
(1, 'Casual Leaves', 'Casual Leaves', '2023-08-31 14:52:22'),
(2, 'Earned Leaves', 'Earned Leaves', '2023-08-31 14:52:49'),
(3, 'Sick Leaves', 'Sick Leaves', '2023-08-31 14:53:15'),
(4, 'RH (Restricted Leaves)', 'Restricted Leaves', '2023-09-01 11:37:06');

-- Indexes for dumped tables

-- Indexes for table admin
ALTER TABLE admin
  ADD PRIMARY KEY (id);

-- Indexes for table tbldepartments
ALTER TABLE tbldepartments
  ADD PRIMARY KEY (id);

-- Indexes for table tblemployees
ALTER TABLE tblemployees
  ADD PRIMARY KEY (id);

-- Indexes for table tblfaculty
ALTER TABLE tblfaculty
  ADD PRIMARY KEY (id);

-- Indexes for table tblleaves
ALTER TABLE tblleaves
  ADD PRIMARY KEY (id),
  ADD KEY UserEmail (empid);

-- Indexes for table tblleavetype
ALTER TABLE tblleavetype
  ADD PRIMARY KEY (id);

-- AUTO_INCREMENT for dumped tables

ALTER TABLE admin
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE tbldepartments
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE tblemployees
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE tblfaculty
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE tblleaves
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE tblleavetype
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

COMMIT;

-- Character set reset
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
