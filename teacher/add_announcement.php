<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';


// Initialize variables
$message = '';
$messageType = '';
$showAlert = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $posted_by = 'Teacher Theres';
        // $posted_by = trim($_POST['posted_by']);
        
        // Basic validation
        if (empty($title) || empty($description) || empty($posted_by)) {
            throw new Exception("Title and description are required");
        }
        
        // Handle file upload
        $picture = '';
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/announcements/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Get file info
            $fileName = $_FILES['picture']['name'];
            $fileSize = $_FILES['picture']['size'];
            $fileTmp = $_FILES['picture']['tmp_name'];
            $fileType = $_FILES['picture']['type'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Allowed file extensions
            $allowedExts = array('jpg', 'jpeg', 'png');
            
            // Validate file
            if (!in_array($fileExt, $allowedExts)) { 
                throw new Exception("Only JPG, JPEG, & PNG files are allowed");
            }
            
            if ($fileSize > 5000000) { // 5MB max
                throw new Exception("File size must be less than 5MB");
            }
            
            // Generate unique filename
            $newFileName = $title . '.' . $fileExt;
            $destination = $uploadDir . $newFileName;
            
            // Move uploaded file
            if (move_uploaded_file($fileTmp, $destination)) {
                $picture = $newFileName;
            } else {
                throw new Exception("Failed to upload file");
            }
        }
        
        // Insert announcement into database
        $query = "INSERT INTO announcement (posted_by, title, picture, description) 
                  VALUES (:posted_by, :title, :picture, :description)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':posted_by', $posted_by);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':picture', $picture);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        
        // Redirect to announcements page with success message
        header("Location: announcement.php?message=Announcement added successfully&type=success");
        exit;
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
        $showAlert = true;
    }
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
        <!-- Welcome Section -->
        <div class="welcome-section d-flex align-items-center justify-content-between">
            <h3 class="mb-0">Announcement</h3>
            <!-- <a href="./add_announcement.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Announcement</a> -->
        </div>

        <div class="container-fluid px-4 mt-5">
            <div class="row">
                <div class="col-md-8 offset-md-2 mx-auto">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="mb-0 text-white">Add New Announcement</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <!-- <div class="mb-3">
                                    <label for="posted_by" class="form-label">Posted By:</label>
                                    <input type="text" class="form-control" id="posted_by" name="posted_by" required>
                                </div> -->
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="picture" class="form-label">Picture:</label>
                                    <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                                    <div class="form-text mt-2">Supported formats: JPG, JPEG, PNG, GIF (Max size: 5MB)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div id="imagePreview" class="mt-2 d-none">
                                        <img src="/placeholder.svg" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="announcement.php" class="btn btn-secondary">Back to Announcements</a>
                                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Add Announcement</button>
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
        document.querySelector('#guardian_management').classList.add("active");
        // Image preview
        document.getElementById('picture').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const previewImg = preview.querySelector('img');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('d-none');
            }
        });
        
        // Show alert if message exists
        <?php if ($showAlert): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<?php echo $messageType === "success" ? "Success!" : "Error!"; ?>',
                text: '<?php echo addslashes($message); ?>',
                icon: '<?php echo $messageType; ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '<?php echo $messageType === "success" ? "#28a745" : "#dc3545"; ?>'
            });
        });
        <?php endif; ?>
    </script>
<?php
include './includes/footer.php';

?>




