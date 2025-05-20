<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';


// Get all announcements
$query = "SELECT * FROM announcement ORDER BY date_posted DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$announcements = $stmt->fetchAll();

// Check for messages from other pages
$message = isset($_GET['message']) ? $_GET['message'] : '';
$messageType = isset($_GET['type']) ? $_GET['type'] : '';
$showAlert = !empty($message);

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
            <a href="./add_announcement.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Announcement</a>
        </div>

        <div class="container-fluid px-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm mt-3">
                    <thead class="">
                        <tr class="text-center table-head-columns">
                            <th class="bg-primary text-white" scope="col">Picture</th>
                            <th class="bg-primary text-white" scope="col">Title</th>
                            <th class="bg-primary text-white" scope="col">Date and Time</th>
                            <th class="bg-primary text-white" scope="col">Description</th>
                            <th class="bg-primary text-white" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($announcements) > 0): ?>
                                <?php foreach ($announcements as $announcement): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if (!empty($announcement['picture'])): ?>
                                                <img src="../uploads/announcements/<?php echo htmlspecialchars($announcement['picture']); ?>" 
                                                    alt="<?php echo htmlspecialchars($announcement['title']); ?>" 
                                                    width="100" class="img-thumbnail">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                                        <td><?php echo date('F j, Y g:i A', strtotime($announcement['date_posted'])); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($announcement['description'])); ?></td>
                                        <td class="text-start">
                                            <a href="./edit_announcement.php?id=<?php echo $announcement['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-edit"></i>  Update
                                            </a>
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $announcement['id']; ?>">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No announcements found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div> 
    </div>


</main>


<script>
    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const announcementId = this.getAttribute('data-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `./delete_announcement.php?id=${announcementId}`;
                }
            });
        });
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
        }).then(() => {
                // Remove parameters from URL after clicking OK
            window.history.replaceState(null, null, "./announcement.php");
            // window.location.href = "./announcement.php";
        });
    });
    <?php endif; ?>
</script>

<?php
include './includes/footer.php';

?>




