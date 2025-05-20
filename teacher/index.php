<?php
session_start();
$pageTitle = "Home";
include '../includes/header.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// if (isLoggedIn()) {
//     if (hasRole('teacher')) {
//         header('Location: dashboard.php');
//         exit;
//     }
//     if (hasRole('guardian')) {
//         header('Location: ../guardian/dashboard.php');
//         exit;
//     }
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    initializeLoginUser($_POST['email'], $_POST['password']);
}

unset($_SESSION['code_resent']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title><?php echo $pageTitle ?? 'CDCMS'; ?></title>
</head>
<body class="vertical light">
    <div class="loader-mask">
        <div class="loader">
            <div></div>
            <div></div>
        </div>
    </div>
    
    <div class="wrapper d-flex align-items-center justify-content-center" style="height: 100dvh;">
    
        <main class="container mt-5">
            <div class="row">
                <div class="col-md-6 d-flex flex-column align-items-center justify-content-center bg-primary py-5" style="height: 100%;">
                    <a href="../index.php">
                        <img src="../assets/images/cropMainCDC.png" class="img-fluid " alt="CDCMS Banner">
                    </a>
                    <h4 class="text-center fw-bold text-white mt-5">CHILD DEVELOPMENT CENTER MANAGEMENT SYSTEM</h4>
                </div>
                <div class="col-md-6" >
                    <div class="card shadow p-4 h-100 py-5">
                        <div class="text-center">
                            <img src="../assets/images/logo.png" alt="Logo" width="80">
                            <h4 class="mt-2">Teacher Login</h4>
                        </div>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control" placeholder="Enter e-mail">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input name="password" type="password" class="form-control" placeholder="Enter password">
                            </div>
                            <?php
                            if (isset($_SESSION['registration_success'])): ?>
                                <div class="alert alert-success alert-dismissible small" role="alert">
                                    <div><?php echo $_SESSION['registration_success'] ?></div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php 
                                endif;
                                unset($_SESSION['registration_success']);

                            if (isset($_SESSION['login_error'])): ?>
                                <div class="alert alert-danger alert-dismissible small" role="alert">
                                    <div><?php echo $_SESSION['login_error'] ?></div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php 
                                endif;
                                unset($_SESSION['login_error']);
                            ?>
                            <button type="submit" class="btn btn-success w-100">Login</button>
                        </form>
                        <!-- <p class="text-center mt-3">Already have an account? <a href="./registration.php">Register Here</a></p> -->
                    </div>

                </div>
            </div>
        </main>
<?php include 'includes/footer.php'; ?>

