<nav class="topnav navbar navbar-light">
    <div class="navBar-Button d-flex align-items-center">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
            <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
        <p>Child Development Center Management System</p>
    </div>

    <ul class="nav">
        <li class="nav-item">
            <section class="nav-link text-muted my-2 circle-icon" id="chatToggle">
                <span class="fe fe-message-circle fe-16"></span>
            </section>
            <?php include 'includes/chat.php'; ?>
        </li>

        <li class="nav-item nav-notif">
            <section class="nav-link text-muted my-2 circle-icon" href="#" data-toggle="modal" data-target=".modal-notif">
                <span class="fe fe-bell fe-16"></span>
                <span id="notification-count" style="
                    position: absolute; 
                    top: 12px; right: 4px; 
                    font-size: 9px; 
                    color: white;
                    background-color: red;
                    width: 10px;
                    height: 10px;
                    display: none;
                    justify-content: center;
                    align-items: center;
                    border-radius: 50px;
                "></span>
            </section>
        </li>

        <li class="nav-item dropdown">
            <span class="nav-link text-muted pr-0 avatar-icon" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="avatar avatar-sm mt-2">
                    <div class="avatar-img rounded-circle avatar-initials-min text-center position-relative">
                        <?php echo isset($_SESSION['user_initials']) ? $_SESSION['user_initials'] : 'U'; ?>
                    </div>
                </span>
            </span>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="teacher_profile.php"><i class="fe fe-user"></i>&nbsp;&nbsp;&nbsp;Profile</a>
                <a class="dropdown-log-out" href="logout.php"><i class="fe fe-log-out"></i>&nbsp;&nbsp;&nbsp;Log Out</a>
            </div>    
        </li>
    </ul>
</nav>

<aside class="sidebar-left border-right bg-white" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>

    <nav class="vertnav navbar-side navbar-light">
        <!-- nav bar -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="#">   
                <img src="assets/images/unified-lgu-logo.png" width="45">
                <div class="brand-title">
                    <br>
                    <span>CDCMS</span>
                </div>         
            </a>
        </div>

        <!--Sidebar-->
        <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('teacher_dashboard.php') ? 'active' : ''; ?>">
            <li class="nav-item dropdown">
                <a class="nav-link" href="teacher_dashboard.php">
                    <i class="fas fa-chart-line"></i>
                    <span class="ml-3 item-text">Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="text-muted-nav nav-heading mt-4 mb-1">
            <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">MAIN COMPONENTS</span>
        </p>

        <ul class="navbar-nav flex-fill w-100 mb-2">
            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('student_management.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="student_management.php">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span class="ml-3 item-text">Student Management</span>
                    </a>
                </li>
            </ul>

            <ul id="guardian_management" class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('guardian_management.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="guardian_management.php">
                        <i class="fa-solid fa-people-roof"></i>
                        <span class="ml-3 item-text">Guardian Management</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('teacher_management.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="teacher_management.php">
                        <i class="fa-solid fa-person-chalkboard"></i>
                        <span class="ml-3 item-text">Teacher Management</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('announcement.php') ? 'active' : ''; ?>" id="announcement">
                <li class="nav-item w-100">
                    <a class="nav-link" href="announcement.php">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span class="ml-3 item-text">Announcement</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('attendance.php') ? 'active' : ''; ?>" id="attendance">
                <li class="nav-item w-100">
                    <a class="nav-link" href="attendance.php">
                        <i class="fa-solid fa-clipboard-user"></i>
                        <span class="ml-3 item-text">Attendance</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('grades.php') ? 'active' : ''; ?>" id="grades">
                <li class="nav-item w-100">
                    <a class="nav-link" href="grades.php">
                        <i class="fa-solid fa-newspaper"></i>
                        <span class="ml-3 item-text">Grades</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('ai_recommendation.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="ai_recommendation.php">
                        <i class="fa-solid fa-robot"></i>
                        <span class="ml-3 item-text">AI Recommendation</span>
                    </a>
                </li>
            </ul>
        </ul>
    </nav>
</aside>

<main role="main" class="main-content">
    <!--For Notification header-->
    <div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="list-group list-group-flush my-n3">
                        <div class="col-12 mb-4">
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-start" role="alert" id="notification">
                                <img class="fade show" src="assets/images/unified-lgu-logo.png" width="35" height="35">
                                <strong style="font-size:12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Update</strong> 
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>

                        <div id="no-notifications" style="display: none; text-align:center; margin-top:10px;">
                            No notifications
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a type="button" href="notification_page.php" class="btn btn-secondary btn-block">View All Notifications</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content Here -->
    <div class="container-fluid py-3">

