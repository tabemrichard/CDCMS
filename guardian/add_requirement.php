<?php
session_start();
$pageTitle = "Add Requirement";
include '../includes/header.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('guardian')) {
    header('Location: ../login.php');
    exit;
}

// Check if student_id and type are provided
if (!isset($_GET['student_id']) || !isset($_GET['type'])) {
    header('Location: requirements.php');
    exit;
}

$student_id = (int)$_GET['student_id'];
$requirement_type = sanitize($_GET['type']);

// Validate requirement type
$valid_types = ['psa', 'immunization_card', 'recent_photo', 'guardian_qcid'];
if (!in_array($requirement_type, $valid_types)) {

    header('Location: requirements.php');
    exit;
}

// Get requirement name for display
$requirement_names = [
    'psa' => 'PSA Birth Certificate',
    'immunization_card' => 'Immunization Card',
    'recent_photo' => 'Recent Photo',
    'guardian_qcid' => 'Guardian QC ID'
];
$requirement_name = $requirement_names[$requirement_type] ?? 'Requirement';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $tmp_name = $_FILES['file']['tmp_name'];
        $name = basename($_FILES['file']['name']);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $new_name = $requirement_type . '_' . $student_id . '_' . uniqid() . '.' . $extension;
        $destination = $upload_dir . $new_name;
        
        if (move_uploaded_file($tmp_name, $destination)) {
            try {
                // Check if requirement record exists
                $stmt = $pdo->prepare("SELECT id FROM requirements WHERE student_id = ?");
                $stmt->execute([$student_id]);
                $requirement_id = $stmt->fetchColumn();
                
                if ($requirement_id) {
                    // Update existing requirement
                    $stmt = $pdo->prepare("UPDATE requirements SET $requirement_type = ? WHERE student_id = ?");
                    $stmt->execute([$new_name, $student_id]);
                } else {
                    // Insert new requirement
                    $stmt = $pdo->prepare("INSERT INTO requirements (student_id, $requirement_type) VALUES (?, ?)");
                    $stmt->execute([$student_id, $new_name]);
                }
                
                header("Location: requirements.php");
                exit;
            } catch (PDOException $e) {
                echo $e;
            }
        } else {
            echo 'An error occur';
        }
    } else {
        echo 'An error occur';
    }
}

include 'includes/sidebar.php';
?>

<div class="welcome-section">
    <h3 class="mb-0"><?php echo $requirement_name; ?></h3>
</div>

<div class="container-fluid px-4 mt-3">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="file" class="form-label">Upload <?php echo $requirement_name; ?></label>
            <input type="file" class="form-control" name="file" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="requirements.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

