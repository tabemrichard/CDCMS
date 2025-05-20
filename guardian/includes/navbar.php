
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
      .avatar-initials {
        width: 165px;
        height: 165px;
        border-radius: 50%;
        display: flex;
        margin-left: 8px;
        justify-content: center;
        align-items: center;
        font-size: 50px;
        font-weight: bold;
        color: #fff;
      }

      .avatar-initials-min {
        width: 40px;
        height: 40px;
        background: #75e6da;
        border-radius: 50%;
        display: flex;
        margin-left: 8px;
        justify-content: center;
        align-items: center;
        font-size: 14px;
        font-weight: bold;
        color: #fff;
      }

      .upload-icon {
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        font-size: 24px;
        color: #fff;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        background-color: #333;
        padding: 10px;
        border-radius: 50%;
        z-index: 1;
      }

      .avatar-img:hover .upload-icon {
        opacity: 1;
      }

      .avatar-img {
        position: relative;
        transition: background-color 0.3s ease-in-out;
      }

      .avatar-img:hover {
        background-color: #a0f0e6;
      }

      .navBar-Button {
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .navBar-Button > p{
        color: white;
        font-size: 1rem;
        margin: auto 0;
      }

  </style>
  
  </head>

  <div class="loader-mask">
      <div class="loader">
          <div></div>
          <div></div>
      </div>
  </div>
    
 
  <body class="vertical  light">
    <div class="wrapper">

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
                    <?php include './chat.php' ?>
                </li>



                <li class="nav-item dropdown">
                <span class="nav-link text-muted pr-0 avatar-icon" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="avatar avatar-sm mt-2">
                        <div class="avatar-img rounded-circle avatar-initials-min text-center position-relative">
                        </div>
                    </span>
                </span>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                    <!-- <a class="dropdown-item" href="profile.php"><i class="fe fe-user"></i>&nbsp;&nbsp;&nbsp;Profile</a> -->
                    <!-- <a class="dropdown-item" href="#"><i class="fe fe-settings"></i>&nbsp;&nbsp;&nbsp;Settings</a>  -->
                    <a class="dropdown-log-out" href="logout.php"><i class="fe fe-log-out"></i>&nbsp;&nbsp;&nbsp;Log Out</a>
                </div>    
                </li>
            </ul>
        </nav>
