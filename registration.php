<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
$pageTitle = "Home";
include './includes/header.php';
require_once './config/database.php';
require_once 'includes/functions.php';
require_once 'includes/register.php';

include './includes/navbar.php';
unset($_SESSION['code_resent']);
?>

<main class="container mt-5">
    <div class="row">
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center bg-primary py-5" style="height: 100%;">
            <img src="./assets/images/cropMainCDC.png" class="img-fluid " alt="CDCMS Banner">
            <h4 class="text-center fw-bold text-white mt-5">CHILD DEVELOPMENT CENTER MANAGEMENT SYSTEM</h4>
        </div>
        <div class="col-md-6" >
            <div class="card shadow p-4 h-100 py-5">
                <div class="text-center">
                    <img src="./assets/images/logo.png" alt="Logo" width="80">
                    <h4 class="mt-2">Register</h4>
                </div>
                <form method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input name="first_name" type="text" class="form-control" placeholder="Maria Theresa" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input name="last_name" type="text" class="form-control" placeholder="Juan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Enter password" required>
                    </div>

                    <!-- Display if user successfully registered or errors -->
                    <?php 
                      if(isset($_SESSION['registration_errors'])): 
                        foreach ($_SESSION['registration_errors'] as $field => $error_msg): ?>
                          <div class="alert alert-danger alert-dismissible small" role="alert">
                              <div><?php echo $error_msg ?></div>
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                          </div>
                        <?php
                        endforeach;
                        unset($_SESSION['registration_errors']);
                        ?>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-success w-100">Create</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="./login.php">Login Here</a></p>

            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>

