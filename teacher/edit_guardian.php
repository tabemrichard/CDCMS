<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';


// Initialize variables
$guardian = [];
$student = [];
$message = '';
$messageType = '';

// Initialize variables
$guardian = [];
$student = [];
$message = '';
$messageType = '';
$showAlert = false;

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: guardian-management.php");
    exit;
}

$guardianId = (int)$_GET['id'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $firstname = trim($_POST['firstname']);
        $middlename = trim($_POST['middlename']);
        $lastname = trim($_POST['lastname']);
        $relationship = trim($_POST['relationship']);
        $contact_number = trim($_POST['contact_number']);
        $occupation = trim($_POST['occupation']);
        $email = trim($_POST['email']);
        
        // Basic validation
        if (empty($firstname) || empty($lastname) || empty($relationship) || 
            empty($contact_number) || empty($occupation) || empty($email)) {
            throw new Exception("All required fields must be filled out");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Update guardian information
        $query = "UPDATE guardian_info SET 
                  firstname = :firstname,
                  middlename = :middlename,
                  lastname = :lastname,
                  relationship = :relationship,
                  contact_number = :contact_number,
                  occupation = :occupation,
                  email = :email
                  WHERE id = :id";
                  
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':middlename', $middlename);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':relationship', $relationship);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':occupation', $occupation);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $guardianId, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $message = "Guardian information updated successfully!";
        $messageType = "success";
        $showAlert = true;
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
        $showAlert = true;
    }
}

// Get guardian information
$query = "SELECT g.*, 
          CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS student_name
          FROM guardian_info g
          JOIN student s ON g.student_id = s.id
          WHERE g.id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $guardianId, PDO::PARAM_INT);
$stmt->execute();
$guardian = $stmt->fetch();

// If guardian not found, redirect back to list
if (!$guardian) {
    header("Location: guardian-management.php");
    exit;
}

// Define relationship options
$relationshipOptions = [
    'Mother' => 'Mother',
    'Father' => 'Father',
    'Grandmother' => 'Grandmother',
    'Grandfather' => 'Grandfather',
    'Sister' => 'Sister',
    'Brother' => 'Brother',
    'Legal Guardian' => 'Legal Guardian',
    'Other' => 'Other'
];

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
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h3 class="mb-0">Guardian Management</h3>
        </div>
        <div class="container-fluid px-4">
            <div class="row">
                <div class="col-md-8 offset-md-2 mx-auto" >
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="mb-0 text-white">Edit Guardian Information</h4>
                        </div>
                        <div class="card-body">
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Student Name:</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($guardian['student_name']); ?>" readonly>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="firstname" class="form-label">First Name:</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($guardian['firstname']); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="middlename" class="form-label">Middle Name:</label>
                                        <input type="text" class="form-control" id="middlename" name="middlename" value="<?php echo htmlspecialchars($guardian['middlename'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="lastname" class="form-label">Last Name:</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($guardian['lastname']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="relationship" class="form-label">Relationship:</label>
                                        <select class="form-control" id="relationship" name="relationship" required>
                                            <?php foreach ($relationshipOptions as $value => $label): ?>
                                                <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $guardian['relationship'] === $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact_number" class="form-label">Contact Number:</label>
                                        <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($guardian['contact_number']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="occupation" class="form-label">Occupation:</label>
                                        <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo htmlspecialchars($guardian['occupation']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($guardian['email']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="./guardian_management.php" class="btn btn-secondary">Back to List</a>
                                    <button type="submit" class="btn btn-primary">Update Guardian</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</main>

<?php if ($showAlert): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            Swal.fire({
                title: <?php echo json_encode($messageType === "success" ? "Success!" : "Error!"); ?>,
                text: <?php echo json_encode($message); ?>,
                icon: <?php echo json_encode($messageType); ?>,
                confirmButtonText: "OK",
                confirmButtonColor: <?php echo json_encode($messageType === "success" ? "#28a745" : "#dc3545"); ?>
            }).then(() => {
                // Remove parameters from URL after clicking OK
                window.history.replaceState(null, null, "./announcement.php");
                window.location.href = "./announcement.php";
            });
        });
    </script>
    <?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('#guardian_management').classList.add("active");

    })
</script>
<?php
include './includes/footer.php';

?>




