<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
$pageTitle = "Teacher Dashboard";
require_once '../config/database.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $birthday = trim($_POST['birthday']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);

    if (empty($birthday)) {
        $birthday = null;
    }

    // Server-side validation
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: profile.php");
        exit();
    }

    // Check if email format is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: profile.php");
        exit();
    }

    // Update query (excluding password if not provided)
    $sql = "UPDATE user SET first_name = ?, middle_name = ?, last_name = ?, birthday = ?, email = ?, address = ? WHERE id = ?";
    $params = [$first_name, $middle_name, $last_name, $birthday, $email, $address, $_SESSION['user_id']];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET first_name = ?, middle_name = ?, last_name = ?, birthday = ?, email = ?, address = ?, password = ? WHERE id = ?";
        $params[] = $hashed_password;
    }

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        $_SESSION['success'] = "Profile updated successfully.";
        $_SESSION['first_name'] = $first_name;
        $_SESSION['middle_name'] = $middle_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['birthday'] = $birthday;
        $_SESSION['email'] = $email;
        $_SESSION['address'] = $address;
    } else {
        $_SESSION['error'] = "Failed to update profile.";
    }

    header("Location: profile.php");
    exit();
}

include './includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/user_navbar.css">

<?php 
include './includes/navbar.php';
include './includes/sidebar.php';
?>

<main role="main" class="main-content">
            
    <?php include_once './includes/notification.php' ?> 

    <!-- Page Content Here -->
    <div class="container-fluid py-3">
        <div class="welcome-section">
            <h3 class="mb-0"><?php echo $_SESSION['first_name'] ?>&apos;s Profile</h3>
            <h3 class="text-muted"></h3>
        </div>

        <!-- Profile Form Section -->
        <div class="container-fluid px-4">
            <h5 class="mb-4 font-weight-bold">PROFILE INFORMATION</h5>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" class="needs-validation" novalidate>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $_SESSION['first_name'] ?>" required>
                                            <div class="invalid-feedback">
                                                Please provide your first name.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?= $_SESSION['middle_name'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $_SESSION['last_name'] ?>" required>
                                            <div class="invalid-feedback">
                                                Please provide your last name.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="birthday">Birthday</label>
                                            <input type="date" class="form-control" id="birthday" name="birthday" value="<?= $_SESSION['birthday'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= $_SESSION['email'] ?>" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid email address.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="address" class="form-control" id="address" value="<?= $_SESSION['address'] ?>" name="address">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Client-side validation using JavaScript
(function () {
    'use strict';
    window.addEventListener('load', function () {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php
include './includes/footer.php';

?>
