<aside id="slide-out" class="side-nav maroon darken-1 fixed" style="background: linear-gradient(180deg, #6a0a0a 0%, #800000 100%) !important; box-shadow: 5px 0 25px rgba(0,0,0,0.25);">
    <style>
        @import url('https://fonts.googleapis.com/icon?family=Material+Icons+Round');
        
        /* Sidebar Base Styles */
        .side-nav {
            width: 250px;
            background: linear-gradient(180deg,rgb(105, 5, 100) 0%,rgb(25, 4, 29) 70%) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: fixed;
            height: 100vh;
            z-index: 999;
            border-right: 1px solid rgba(255,255,255,0.05);
            transform: translateX(0);
        }
        
        .side-nav.collapsed {
            transform: translateX(-250px);
        }
        
        /* Profile Section */
        .sidebar-profile {
            padding: 30px 20px 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(0,0,0,0.15) 100%);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-profile:hover {
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(0,0,0,0.2) 100%);
        }
        
        .sidebar-profile:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff4d4d, #ff9999);
            box-shadow: 0 0 15px rgba(255, 153, 153, 0.5);
        }
        
        .sidebar-profile-image {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid rgba(255,255,255,0.2);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        
        .sidebar-profile-image:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            border-color: rgba(143, 56, 56, 0.4);
        }
        
        .sidebar-profile-image img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: all 0.3s ease;
        }
        
        .sidebar-profile-info p {
            color: #fff;
            margin: 8px 0 0;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            position: relative;
            display: inline-block;
            padding: 0 10px;
            transition: all 0.3s ease;
        }
        
        .sidebar-profile-info p:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #ff4d4d, #ff9999);
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .sidebar-profile-info p:hover {
            letter-spacing: 1.5px;
        }
        
        .sidebar-profile-info p:hover:after {
            width: 70px;
            background: linear-gradient(90deg, #ff9999, #ff4d4d);
        }
        
        /* Menu Styles */
        .sidebar-menu {
            margin: 0;
            padding: 20px 0 100px;
            position: relative;
            height: calc(100vh - 280px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        
        /* Custom Scrollbar */
        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-menu::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.2);
            border-radius: 20px;
        }
        
        .sidebar-menu > li > a {
            color: rgba(255,255,255,0.9) !important;
            font-size: 15px;
            font-weight: 500;
            padding: 0 25px;
            height: 50px;
            line-height: 50px;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border-left: 4px solid transparent;
            margin: 2px 10px;
            border-radius: 8px;
        }
        
        .sidebar-menu > li > a i {
            margin-right: 18px;
            font-size: 22px;
            width: 24px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }
        
        .sidebar-menu > li > a:hover,
        .sidebar-menu > li.active > a {
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%) !important;
            color: #fff !important;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #ff6b6b;
        }
        
        .sidebar-menu > li > a:hover i {
            transform: scale(1.1);
            color: #ff9999 !important;
        }
        
        /* Collapsible Menu Items */
        .collapsible-header {
            background: transparent !important;
            border: none !important;
            padding: 0 25px !important;
            height: 50px !important;
            line-height: 50px !important;
            color: rgba(158, 138, 138, 0.9) !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 2px 10px;
            border-radius: 8px;
            border-left: 4px solid transparent;
        }
        
        .collapsible-header i:first-child {
            margin-right: 18px;
            font-size: 22px !important;
            width: 24px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .collapsible-header:hover {
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%) !important;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #ff6b6b;
        }
        
        .collapsible-header:hover i:first-child {
            transform: scale(1.1);
            color: #ff9999 !important;
        }
        
        .collapsible-body {
            background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.25) 100%) !important;
            border: none !important;
            padding: 10px 0 !important;
            margin: 0 15px 5px;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
            box-shadow: inset 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .collapsible-body a {
            color: rgba(255,255,255,0.85) !important;
            font-size: 14px !important;
            padding: 0 20px 0 65px !important;
            height: 42px;
            line-height: 42px;
            display: block;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            margin: 2px 10px;
            border-radius: 6px;
        }
        
        .collapsible-body a:before {
            content: '';
            position: absolute;
            left: 40px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: #ff9999;
            border-radius: 50%;
            opacity: 0.8;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
        }
        
        .collapsible-body a:hover {
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%) !important;
            padding-left: 70px !important;
            color: #fff !important;
            transform: translateX(5px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .collapsible-body a:hover:before {
            opacity: 1;
            transform: translateY(-50%) scale(1.5);
            background: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.3);
        }
        
        /* Dropdown Icons */
        .nav-drop-icon {
            position: absolute;
            right: 15px;
            font-size: 20px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0.7;
        }
        
        .collapsible-header:hover .nav-drop-icon {
            opacity: 1;
            color: #ff9999 !important;
        }
        
        .collapsible-header.active .nav-drop-icon {
            transform: rotate(90deg);
            color: #ff6b6b !important;
            opacity: 1;
        }
        
        /* Footer */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 18px 0;
            text-align: center;
            background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.25) 100%);
            border-top: 1px solid rgba(255,255,255,0.05);
            backdrop-filter: blur(5px);
            z-index: 2;
        }
        
        .copyright {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            margin: 0;
            font-weight: 400;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .footer:hover .copyright {
            color: #fff;
            letter-spacing: 0.8px;
        }
        
        /* Active State */
        .sidebar-menu > li.active > a {
            background: linear-gradient(90deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.15) 100%) !important;
            border-left: 4px solid #ff6b6b;
            font-weight: 600;
            color: #fff !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transform: translateX(5px);
        }
        
        .sidebar-menu > li.active > a i {
            color: #ff6b6b !important;
            transform: scale(1.1);
        }
        
        /* Hover Effects */
        .sidebar-menu > li > a:after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
            border-radius: 4px;
        }
        
        .sidebar-menu > li > a:hover:after {
            width: calc(100% - 5px);
            left: 5px;
        }
        
        /* Active menu indicator */
        .sidebar-menu > li.active > a:before {
            content: '';
            position: absolute;
            right: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 30px;
            background: #ff6b6b;
            border-radius: 4px 0 0 4px;
            box-shadow: -2px 0 10px rgba(255, 107, 107, 0.5);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .side-nav {
                transform: translateX(-100%);
                z-index: 1000;
            }
            
            .side-nav.active {
                transform: translateX(0);
                box-shadow: 5px 0 30px rgba(0,0,0,0.3);
            }
        }
    </style>
    
    <div class="side-nav-wrapper">
        <!-- Profile Section -->
        <div class="sidebar-profile">
            <div class="sidebar-profile-image">
                <img src="../assets/images/uov_logo.png" alt="UoV Logo">
            </div>
            <div class="sidebar-profile-info">
                <p>Admin</p>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
            <!-- Dashboard -->
            <li class="no-padding">
                <a class="waves-effect waves-white" href="dashboard.php">
                    <i class="material-icons">dashboard</i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Department -->
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-white">
                    <i class="material-icons">business</i>
                    <span>Department</span>
                    <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="adddepartment.php">Add Department</a></li>
                        <li><a href="managedepartments.php">Manage Department</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Leave Type -->
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-white">
                    <i class="material-icons">event_available</i>
                    <span>Leave Type</span>
                    <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="addleavetype.php">Add Leave Type</a></li>
                        <li><a href="manageleavetype.php">Manage Leave Type</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Employees -->
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-white">
                    <i class="material-icons">people</i>
                    <span>Employees</span>
                    <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="addemployee.php">Add Employee</a></li>
                        <li><a href="manageemployee.php">Manage Employee</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Leave Management -->
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-white">
                    <i class="material-icons">assignment</i>
                    <span>Leave Management</span>
                    <i class="nav-drop-icon material-icons">keyboard_arrow_right</i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="leaves.php">All Leaves</a></li>
                        <li><a href="pending-leavehistory.php">Pending Leaves</a></li>
                        <li><a href="approvedleave-history.php">Approved Leaves</a></li>
                        <li><a href="notapproved-leaves.php">Not Approved Leaves</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Change Password -->
            <li class="no-padding">
                <a class="waves-effect waves-white" href="changepassword.php">
                    <i class="material-icons">lock_outline</i>
                    <span>Change Password</span>
                </a>
            </li>
            
            <!-- Sign Out -->
            <li class="no-padding">
                <a class="waves-effect waves-white" href="logout.php">
                    <i class="material-icons">exit_to_app</i>
                    <span>Sign Out</span>
                </a>
            </li>
        </ul>
        
        <!-- Footer -->
        <div class="footer">
            <p class="copyright">ELMS © <?php echo date('Y'); ?></p>
        </div>
    </div>
</aside>