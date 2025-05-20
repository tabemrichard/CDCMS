<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';

// Change from student_id to guardian name parameters
// $guardianFirstName = isset($_POST['guardian_firstname']) ? strtolower(trim($_POST['guardian_firstname'])) : '';
// $guardianMiddleName = isset($_POST['guardian_middlename']) ? strtolower(trim($_POST['guardian_middlename'])) : '';
// $guardianLastName = isset($_POST['guardian_lastname']) ? strtolower(trim($_POST['guardian_lastname'])) : '';

// $guardianFirstName ='Johnpaul';
// $guardianMiddleName = 'Araceli';
// $guardianLastName = 'Daniel';

$guardianFirstName = $_SESSION['guardian_firstname'] ?? '';
$guardianMiddleName = $_SESSION['guardian_middlename'] ?? '';
$guardianLastName = $_SESSION['guardian_lastname'] ?? '';


// Get student information based on guardian information
$query = "SELECT s.id, s.student_id as student_number, 
          CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS full_name,
          s.firstname, s.lastname,
          e.psa, e.immunizationcard, e.recentphoto, e.guardianqcid
          FROM student s
          JOIN enrollment e ON s.id = e.student_id
          JOIN guardian_info g ON s.id = g.student_id
          WHERE LOWER(g.firstname) LIKE :firstname
          AND (LOWER(g.middlename) LIKE :middlename OR (g.middlename IS NULL AND :middlename = ''))
          AND LOWER(g.lastname) LIKE :lastname
          LIMIT 1";

$stmt = $pdo->prepare($query);

// Add wildcards for partial matching
$firstNameParam = $guardianFirstName;
$middleNameParam = empty($guardianMiddleName) ? '' : $guardianMiddleName;
$lastNameParam = $guardianLastName;

$stmt->bindParam(':firstname', $firstNameParam, PDO::PARAM_STR);
$stmt->bindParam(':middlename', $middleNameParam, PDO::PARAM_STR);
$stmt->bindParam(':lastname', $lastNameParam, PDO::PARAM_STR);
$stmt->execute();
$student = $stmt->fetch();

if ($student) {
    // Define requirements
    $requirements = [
        'psa' => [
            'name' => 'PSA Birth Certificate',
            'file' => $student['psa'],
            'deletable' => false
        ],
        'immunizationcard' => [
            'name' => 'Immunization Card',
            'file' => $student['immunizationcard'],
            'deletable' => true
        ],
        'recentphoto' => [
            'name' => 'Recent Photo',
            'file' => $student['recentphoto'],
            'deletable' => true
        ],
        'guardianqcid' => [
            'name' => 'Guardian QC ID',
            'file' => $student['guardianqcid'],
            'deletable' => false
        ]
    ];

    // Store student ID for use in the rest of the code
    $studentId = $student['id'];

    // Process form submission for adding/updating requirements
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['requirement'])) {
        $action = $_POST['action'];
        $requirement = $_POST['requirement'];
        
        // Validate requirement type
        if (!array_key_exists($requirement, $requirements)) {
            die("Invalid requirement type");
        }
        
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            if ($action === 'add' || $action === 'update') {
                // Check if file was uploaded
                if (!isset($_FILES['requirement_file']) || $_FILES['requirement_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Please select a file to upload");
                }
                
                // Validate file type (allow only images and PDFs)
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                $fileType = $_FILES['requirement_file']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Only JPEG, PNG, GIF images and PDF files are allowed");
                }
                
                // Handle file uploads
                $upload_dir = '../enrollment_process/uploads/files/';


                // Ensure base directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Generate unique filename
                $student_name = strtolower($student['firstname']) . '_' . strtolower($student['lastname']);
                $extension = pathinfo($_FILES['requirement_file']['name'], PATHINFO_EXTENSION);
                $newFilename = $student_name . '_' . $requirement . '.' . $extension;

                // Create student-specific directory
                $userDir = $upload_dir . $student_name . '/' . $requirement . '/';
                
                if (!is_dir($userDir)) {
                    if (!mkdir($userDir, 0777, true) && !is_dir($userDir)) {
                        throw new Exception("Failed to create directory: $userDir");
                    }
                }

                // Full file path
                $uploadPath = $userDir . $newFilename;

                // Move uploaded file
                if (!move_uploaded_file($_FILES['requirement_file']['tmp_name'], $uploadPath)) {
                    throw new Exception("Failed to upload file");
                }
                
                // Update enrollment record
                $query = "UPDATE enrollment SET $requirement = :file_path WHERE student_id = :student_id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':file_path', $uploadPath);
                $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
                $stmt->execute();
                
                // Create notification
                $notificationName = $student['full_name'];
                $notificationContent = "Guardian has " . ($action === 'add' ? 'added' : 'updated') . " the " . $requirements[$requirement]['name'] . " for " . $student['full_name'];
                
                $query = "INSERT INTO notification (name, action, content, file_path) 
                        VALUES (:name, :action, :content, :file_path)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':name', $notificationName);
                $stmt->bindParam(':action', $action);
                $stmt->bindParam(':content', $notificationContent);
                $stmt->bindParam(':file_path', $uploadPath);
                $stmt->execute();
                
                // Commit transaction
                $pdo->commit();
                
                // Set success message
                $successMessage = "Requirement " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
                
            } elseif ($action === 'delete' && $requirements[$requirement]['deletable']) {
                // Get current file path
                $currentFile = $student[$requirement];
                
                if (!empty($currentFile)) {
                    // Delete file if it exists
                    $filePath = '../enrollment_process/' . $currentFile;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    
                    // Update enrollment record
                    $query = "UPDATE enrollment SET $requirement = NULL WHERE student_id = :student_id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    // Create notification
                    $notificationName = $student['full_name'];
                    $notificationContent = "Guardian has deleted the " . $requirements[$requirement]['name'] . " for " . $student['full_name'];
                    
                    $query = "INSERT INTO notification (name, action, content, file_path) 
                            VALUES (:name, :action, :content, :file_path)";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':name', $notificationName);
                    $action = 'delete';
                    $stmt->bindParam(':action', $action);
                    $stmt->bindParam(':content', $notificationContent);
                    $stmt->bindParam(':file_path', $currentFile);
                    $stmt->execute();
                    
                    // Commit transaction
                    $pdo->commit();
                    
                    // Set success message
                    $successMessage = "Requirement deleted successfully!";
                } else {
                    throw new Exception("No file to delete");
                }
            } else {
                throw new Exception("Invalid action");
            }
            
            // Refresh student data after changes
            $query = "SELECT s.id, s.student_id as student_number, 
                    CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS full_name,
                    e.psa, e.immunizationcard, e.recentphoto, e.guardianqcid
                    FROM student s
                    JOIN enrollment e ON s.id = e.student_id
                    WHERE s.id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            $student = $stmt->fetch();
            
            // Update requirements array
            foreach ($requirements as $key => $value) {
                $requirements[$key]['file'] = $student[$key];
            }
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $errorMessage = $e->getMessage();
        }
    }

}

// Function to get file URL
function getFileUrl($filename) {
    if (empty($filename)) return '';
    return '../enrollment_process/' . $filename;
}

// Function to get file extension
function getFileExtension($filename) {
    if (empty($filename)) return '';
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Function to check if file is an image
function isImage($filename) {
    if (empty($filename)) return false;
    $ext = getFileExtension($filename);
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
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
            <h3 class="mb-0">Requirements</h3>
        </div>

        <?php if (!$student): ?>
            <div class="container-fluid px-4 mt-5">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">You don't have any enrolled student</h4>
                        <p class="text-muted">Please contact the administrator if you believe this is an error.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted">Student: <?php echo htmlspecialchars($student['full_name']); ?> (<?php echo htmlspecialchars($student['student_number']); ?>)</p>
            
            <div class="container-fluid px-4 mt-3">
            <?php foreach ($requirements as $key => $value): ?>
                <div class="card mb-4 py-4 d-flex align-items-center justify-content-center flex-column shadow-sm">
                    <?php if ($value['file'] && isImage($value['file'])): ?>
                        <img src="../enrollment_process/<?php echo getFileUrl($value['file']); ?>" class="card-img-top img-container img-fluid rounded mx-auto" 
                             style="height: 500px; width: 500px; object-fit: contain;" alt="<?php echo htmlspecialchars($value['name']); ?>">

                    <?php else: ?>
                        <img src="../assets/images/noDocument.jpg" class="card-img-top img-container img-fluid rounded mx-auto"
                             style="height: 500px; width: 500px; object-fit: contain;" alt="No Document">
                    <?php endif; ?>
                    
                    <div class="card-body w-50 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($value['name']); ?></h5>
                            <?php if ($value['file']): ?>
                                <div>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $key; ?>">
                                        <i class="fa-solid fa-edit"></i> Update
                                    </button>
                                    
                                    <?php if ($value['deletable']): ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $key; ?>">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal<?php echo $key; ?>">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Add Modal -->
                <div class="modal fade" id="addModal<?php echo $key; ?>" tabindex="-1" aria-labelledby="addModalLabel<?php echo $key; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addModalLabel<?php echo $key; ?>">Add <?php echo htmlspecialchars($value['name']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="requirement_file" class="form-label">Upload File</label>
                                        <input type="file" class="form-control" id="requirement_file" name="requirement_file" required>
                                        <div class="form-text">Accepted formats: JPEG, PNG, GIF, PDF</div>
                                    </div>
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="requirement" value="<?php echo $key; ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Update Modal -->
                <div class="modal fade" id="updateModal<?php echo $key; ?>" tabindex="-1" aria-labelledby="updateModalLabel<?php echo $key; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel<?php echo $key; ?>">Update <?php echo htmlspecialchars($value['name']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="requirement_file" class="form-label">Upload New File</label>
                                        <input type="file" class="form-control" id="requirement_file" name="requirement_file" required>
                                        <div class="form-text">Accepted formats: JPEG, PNG, GIF, PDF</div>
                                    </div>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="requirement" value="<?php echo $key; ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <?php if ($value['deletable']): ?>
                <div class="modal fade" id="deleteModal<?php echo $key; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $key; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $key; ?>">Delete <?php echo htmlspecialchars($value['name']); ?>?</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this <?php echo htmlspecialchars($value['name']); ?>?</p>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="requirement" value="<?php echo $key; ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Show success or error message
    <?php if (isset($successMessage)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Success!',
            text: '<?php echo addslashes($successMessage); ?>',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = window.location.pathname; // Reload page without message
            }
        });
    });
    <?php unset($successMessage); endif; ?>
    
    <?php if (isset($errorMessage)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Error!',
            text: '<?php echo addslashes($errorMessage); ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = window.location.pathname; // Reload page without message
            }
        });
    });
    <?php unset($errorMessage); endif; ?>
</script>

<?php
include './includes/footer.php';

?>




