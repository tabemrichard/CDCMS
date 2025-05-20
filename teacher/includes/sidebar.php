<aside class="sidebar-left border-right bg-white" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>

    <nav class="vertnav navbar-side navbar-light">
        <!-- nav bar -->
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="./../index.php">   
                <img src="../assets/images/unified-lgu-logo.png" width="45">
                <div class="brand-title">
                    <br>
                    <span>CDCMS</span>
                </div>         
            </a>
        </div>

        <!--Sidebar ito-->
        <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" id="dashboard">
            <li class="nav-item dropdown">
                <a class="nav-link" href="./dashboard.php">
                    <i class="fas fa-chart-line"></i>
                    <span class="ml-3 item-text">Dashboard</span>
                </a>
            </li>
        </ul>

        <p class="text-muted-nav nav-heading mt-4 mb-1">
            <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">MAIN COMPONENTS</span>
        </p>

        <ul class="navbar-nav flex-fill w-100 mb-2" id="studentManagement">
            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'student_management.php' ? 'active' : ''; ?>" >
                <li class="nav-item w-100">
                    <a class="nav-link" href="./student_management.php">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span class="ml-3 item-text">Student Management</span>
                    </a>
                </li>
            </ul>

            <ul id="guardian_management" class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'guardian_management.php' ? 'active' : ''; ?>">
                <li class="nav-item w-100">
                    <a class="nav-link" href="./guardian_management.php">
                        <i class="fa-solid fa-people-roof"></i>
                        <span class="ml-3 item-text">Guardian Management</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_management.php' ? 'active' : ''; ?>" id="teacherManagement">
                <li class="nav-item w-100">
                    <a class="nav-link" href="./teacher_management.php">
                        <i class="fa-solid fa-person-chalkboard"></i>
                        <span class="ml-3 item-text">Teacher Management</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'announcement.php' ? 'active' : ''; ?>" id="announcement">
                <li class="nav-item w-100">
                    <a class="nav-link" href="./announcement.php">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span class="ml-3 item-text">Announcement</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>" id="attendance">
                <li class="nav-item w-100">
                    <a class="nav-link" href="./attendance.php">
                        <i class="fa-solid fa-clipboard-user"></i>
                        <span class="ml-3 item-text">Attendance</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : ''; ?>" id="grades">
                <li class="nav-item w-100">
                    <a class="nav-link" href="./grades.php">
                        <i class="fa-solid fa-newspaper"></i>
                        <span class="ml-3 item-text">Grades</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'ai_recommentdation.php' ? 'active' : ''; ?>" id="aiRecommendation">
                <li class="nav-item w-100">
                    <a class="nav-link" href="./ai_recommendation.php">
                        <i class="fa-solid fa-robot"></i>
                        <span class="ml-3 item-text">AI Recommendation</span>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav flex-fill w-100 mb-2 <?php echo basename($_SERVER['PHP_SELF']) == 'guardian_accounts.php' ? 'active' : ''; ?>" id="aiRecommendation">
                <li class="nav-item w-100">
                    <a class="nav-link" href="./guardian_accounts.php">
                        <i class="fa-solid fa-user-lock"></i>
                        <span class="ml-3 item-text">Guardian Accounts</span>
                    </a>
                </li>
            </ul>
        </ul>
    </nav>
</aside>