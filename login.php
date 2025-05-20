<?php
session_start();
$pageTitle = "Home";
include './includes/header.php';
require_once './config/database.php';
require_once './includes/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $email = $_POST['email'];
    $studentID = $_POST['studentID'];
    $confirmPassword = isset($_POST['confirmPassword']) ? sanitize($_POST['confirmPassword']) : null;

    // Check if this is a login or registration attempt
    if (empty($confirmPassword)) {
        // This is a login attempt
        try {
            $stmt = $pdo->prepare("SELECT id, guardian_id, student_id, role, email, password, isConfirm FROM guardian_account WHERE email = :email AND student_id = :studentID");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':studentID', $studentID, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['password'])) {

                if ((int)$row['isConfirm'] == 0) {
                    $_SESSION['error'] = 'Account is not activated. Please contact the administrator';
                    header('Location: ./login.php');
                    exit;
                }

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['guardian_id'] = $row['guardian_id'];
                $_SESSION['student_id'] = $row['student_id'];
                $_SESSION['role'] = strtolower($row['role']);
                $_SESSION['email'] = $row['email'];

                $stmt = $pdo->prepare("SELECT * FROM guardian_info WHERE id = :guardianID");
                $stmt->bindParam(':guardianID', $row['guardian_id'], PDO::PARAM_STR);
                $stmt->execute();
                $guardianRow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($guardianRow) {
                    $_SESSION['guardian_firstname'] = $guardianRow['firstname'];
                    $_SESSION['guardian_middlename'] = $guardianRow['middlename'];
                    $_SESSION['guardian_lastname'] = $guardianRow['lastname'];
                }

                $_SESSION['success'] = 'Login Successfully';
                header('Location: ./guardian/dashboard.php');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid email or password.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
            header('Location: login.php');
            exit;
        }
    } else {
        // This is a registration/password setup attempt
        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: login.php');
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM guardian_account WHERE student_id = ? AND email = ?");
            $stmt->execute([$studentID, $email]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$account) {
                // No matching record found
                $_SESSION['error'] = 'Invalid student ID or email. Please check your information and try again.';
                header('Location: login.php');
                exit;
            }
            // Call your function to initialize the guardian user
            try {
                $pdo->beginTransaction();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("UPDATE guardian_account SET password = ? WHERE email = ?");
                $stmt->execute([$hashedPassword, $email]);
                
                // Check if the update was successful
                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    $_SESSION['success'] = 'Account successfully registered. Your account will go verification within 24hrs.';
                    header('Location: guardian/dashboard.php');
                    exit;
                } else {
                    // No rows were updated
                    $_SESSION['error'] = 'No account was updated. Please check your email address.';
                    header('Location: login.php');
                    exit;
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                // Handle database errors
                $_SESSION['error'] = 'Database error: ' . $e->getMessage();
                header('Location: login.php');
                error_log('Database error in guardian account update: ' . $e->getMessage());
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                // Handle any other unexpected errors
                $_SESSION['error'] =  'An unexpected error occurred: ' . $e->getMessage();
                header('Location: login.php');
                error_log('Unexpected error in guardian account update: ' . $e->getMessage());
                exit;
            }

        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
            header('Location: login.php');
            exit;
        }
    }
}

// If we reach here, it's a GET request to display the login form
?>

<?php include './includes/navbar.php'; ?>

<main class="container mt-5">
    <div class="row align-items-stretch">
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center bg-primary" style="height: 100%; padding: 5rem 1rem;">
            <img src="./assets/images/cropMainCDC.png" class="img-fluid" alt="CDCMS Banner">
            <h4 class="text-center fw-bold text-white mt-5">CHILD DEVELOPMENT CENTER MANAGEMENT SYSTEM</h4>
        </div>
        <div class="col-md-6">
            <div class="card shadow p-4 h-100 py-4">
                <div class="text-center">
                    <img src="./assets/images/logo.png" alt="Logo" width="80">
                    <h4 class="mt-2">Guardian Login</h4>
                </div>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="Guardian Email" required>
                    </div>
                    <?php 
                        if (!isset($_SESSION['activeGuardian']) || $_SESSION['activeGuardian'] ):     
                    ?>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input name="password" type="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Create Password</label>
                            <input name="password" type="password" class="form-control" placeholder="Enter password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input name="confirmPassword" type="password" class="form-control" placeholder="Confirm password" required>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Student ID</label>
                        <input name="studentID" type="text" class="form-control" placeholder="Enter student ID" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <?php 
                        if (!isset($_SESSION['activeGuardian']) || $_SESSION['activeGuardian'] ):     
                        ?>
                        Login
                        <?php else: ?>
                        Create Account
                        <?php endif; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    <?php if(isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($_SESSION['error']); ?>'
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo addslashes($_SESSION['success']); ?>'
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>

