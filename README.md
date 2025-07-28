# 🗓️ Leave Management System (LMS)

A comprehensive and robust Leave Management System (LMS) designed to handle all aspects of employee leave requests, validations, approvals, and tracking. This system enforces business rules, ensures compliance with labor regulations, and supports a multi-tier approval process.

---

## 🚀 System Overview

The Leave Management System allows organizations to efficiently manage various types of employee leaves. It provides validations based on leave type, gender, position, and approval flow. The system is ideal for HR departments, educational institutions, and corporate environments.

---

## 🛠️ Core Features

### ✅ Leave Types & Rules

| Leave Type        | Max Days | Special Rules                          |
|-------------------|----------|----------------------------------------|
| Annual Leave      | 30       | Standard yearly entitlement            |
| Casual Leave      | 30       | For short, unplanned absences         |
| Medical Leave     | 20       | Health-related absences               |
| Maternity Leave   | 84       | Female only – Gender validation        |
| Paternity Leave   | 3        | Male only – Gender validation          |
| Study Leave       | 365      | Requires special HR approval          |
| Sabbatical Leave  | 365      | Professors/Senior Lecturers only      |
| Duty Leave        | 90       | For official duty work                |
| No Pay Leave      | 365      | Extended unpaid leave                 |

---

### 📋 Leave Request Processing
- Submit new leave requests
- Auto-validation of rules
- Overlap detection
- Leave balance check

### 📊 Leave Balance Management
- Tracks used, pending, and available leave days
- Auto initialization and carry-forward logic
- Rollover support per leave policy

### 🔁 Approval Workflow
- Multi-level leave approval system
- Status tracking: Pending, Approved, Rejected
- Admin remarks & audit trail logging

### 🔒 Validation System
- Working day calculations (excluding weekends)
- Rule-based validation with error messages
- Gender and position-based restrictions

---

## 🗄️ Database Schema Overview

- **`tblleaves`** – Leave applications (requests)
- **`tblleavetype`** – Leave categories and their rules
- **`tblemployees`** – Employee master table with roles, genders, positions, etc.

---

## 🧰 Tech Stack (example)

> This will vary based on your actual implementation. You can modify accordingly.

- **Frontend:** HTML, CSS, JavaScript / React
- **Backend:** PHP / Node.js / Laravel
- **Database:** MySQL / PostgreSQL
- **Authentication:** Session / JWT-based (optional)
- **Hosting:** Apache / Nginx

---

## 📎 Installation (Example)

```bash
git clone https://github.com/yourusername/leave-management-system.git
cd leave-management-system
