<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

// Prepare SQL statement to fetch user details only if role is "teacher"
try {
    // Prepare SQL statement to fetch user details only if role is "teacher"
    $sql = "SELECT first_name, middle_name, last_name, address, email 
            FROM user 
            WHERE role = 'teacher'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // Fetch user details
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $full_name = trim("{$user['first_name']} {$user['middle_name']} {$user['last_name']}");
        $address = $user['address'];
        $email = $user['email'];
    } else {
        // If no teacher found, display default values
        $full_name = "Not a Teacher";
        $address = "N/A";
        $email = "N/A";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
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
            

    <!-- Page Content Here -->
    <div class="container-fluid py-3">
        <div class="welcome-section">
            <h3 class="mb-0">Teacher's Profile</h3>
        </div>

        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-center" style="gap: 6rem;">
                <img class="rounded img-fluid" src="../assets/images/female.jpg" alt="">
                <div>
                    <h1 class="fw-bold mb-4">Adviser</h1>
                    <h4 class="mb-3"><strong>Full Name:</strong> <?php echo $full_name; ?></h4>
                    <h4 class="mb-3"><strong>Address:</strong> <?php echo $address ?? 'N/A'; ?></h4>
                    <h4 class="mb-3"><strong>Email:</strong> <?php echo $email; ?></h4>
                </div>
            </div>
        </div>  
    </div>
</main>

<?php
include './includes/footer.php';

?>




