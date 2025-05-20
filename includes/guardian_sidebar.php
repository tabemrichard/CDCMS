<nav class="topnav navbar navbar-light">
    <div class="navBar-Button d-flex align-items-center">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
            <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
        <p>Child Development Center Management System</p>
    </div>

    <ul class="nav position-relative">
        <li class="nav-item">
            <section type="button" class="nav-link text-muted my-2 circle-icon" id="chatToggle">
                <span class="fe fe-message-circle fe-16"></span>
            </section>
            <?php include 'includes/chat.php'; ?>
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
                <a class="dropdown-item" href="guardian_profile.php"><i class="fe fe-user"></i>&nbsp;&nbsp;&nbsp;Profile</a>
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
        <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('guardian_announcement.php') ? 'active' : ''; ?>">
            <li class="nav-item dropdown">
                <a class="nav-link" href="guardian_announcement.php">
                    <i class="fas fa-chart-line"></i>
                    <span class="ml-3 item-text">Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="text-muted-nav nav-heading mt-4 mb-1">
            <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">MAIN COMPONENTS</span>
        </p>

        <ul class="navbar-nav flex-fill w-100 mb-2">
            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('guardian_attendance.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="guardian_attendance.php">
                        <i class="fa-solid fa-clipboard-user"></i>
                        <span class="ml-3 item-text">Attendance</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('guardian_grades.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="guardian_grades.php">
                        <i class="fa-solid fa-newspaper"></i>
                        <span class="ml-3 item-text">Grades</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('guardian_requirements.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="guardian_requirements.php">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span class="ml-3 item-text">Requirements</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo isActivePage('guardian_teachers_profile.php') ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="guardian_teachers_profile.php">
                        <i class="fa-solid fa-chalkboard-teacher"></i>
                        <span class="ml-3 item-text">Teacher's Profile</span>
                    </a>
                </li>
            </ul>
        </ul>
    </nav>
</aside>

<main role="main" class="main-content">
    <!-- Page Content Here -->
    <div class="container-fluid py-3">

