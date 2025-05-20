<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
$pageTitle = "Email Confirmation";
include './includes/header.php';
require_once './config/database.php';
require_once 'includes/functions.php';
require_once 'includes/email_functions.php';
include_once 'includes/confirm.php';
include './includes/navbar.php';
?>

<main class="container mt-5">
    <div class="row">
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center bg-primary py-5" style="height: 100%;">
            <img src="./assets/images/cropMainCDC.png" class="img-fluid " alt="CDCMS Banner">
            <h4 class="text-center fw-bold text-white mt-5">CHILD DEVELOPMENT CENTER MANAGEMENT SYSTEM</h4>
        </div>
        <div class="col-md-6">
            <div class="card shadow p-4 h-100 py-5">
                <div class="text-center">
                    <img src="./assets/images/logo.png" alt="Logo" width="80">
                    <h4 class="mt-2">Email Confirmation</h4>
                </div>
                
                <p class="text-center mb-4">
                    We've sent a confirmation code to <strong><?php echo $_SESSION['user_pending_confirmation']['email']; ?></strong>. 
                    Please enter the code below to verify your email address.
                </p>
                
                <form method="post">
                    <div class="mb-4">
                        <input name="confirmation_code" type="text" class="form-control form-control-lg text-center" placeholder="Enter code" required>
                    </div>

                    <!-- Display success or error messages -->
                    <?php 
                      if(isset($_SESSION['confirmation_success'])): ?>
                        <div class="alert alert-success alert-dismissible small" role="alert">
                            <div><?php echo $_SESSION['confirmation_success']; ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php unset($_SESSION['confirmation_success']); endif; 
                    
                      if(isset($_SESSION['confirmation_error'])): ?>
                        <div class="alert alert-danger alert-dismissible small" role="alert">
                            <div><?php echo $_SESSION['confirmation_error']; ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php unset($_SESSION['confirmation_error']); endif; 
                    ?>

                    <button type="submit" class="btn btn-success w-100 mb-3">Verify Email</button>
                </form>
                
                <?php if (!isset($_SESSION['code_resent'])): ?>
                    <p class="text-center mt-3">Didn't receive the code? <a href="confirmation.php?resend=true">Resend Code</a></p>
                    <p class="text-center text-muted small">The confirmation code will expire in 5 minutes.</p>
                <?php endif ?>
                
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
