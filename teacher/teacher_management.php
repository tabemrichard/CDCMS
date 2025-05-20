<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';
include './includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/user_navbar.css">
<?php 
include './includes/navbar.php';
include './includes/sidebar.php';

// Pagination setup
$recordsPerPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;

// Query to get teachers with pagination
$sql = "SELECT id, first_name, last_name, middle_name, email, address, role, birthday
        FROM `user` 
        WHERE role = 'teacher' 
        ORDER BY last_name ASC 
        LIMIT $offset, $recordsPerPage";
$result = $pdo->query($sql);

// Count total number of teachers
$countSql = "SELECT COUNT(*) as total FROM `user` WHERE role = 'teacher'";
$countResult = $pdo->query($countSql);
$totalRecords = $countResult->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
?>
<main role="main" class="main-content">
            
    <?php include_once './includes/notification.php' ?>
    <!-- Page Content Here -->
    <div class="container-fluid py-3">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h3 class="mb-0">Teacher Management</h3>
        </div>
        <div class="container-fluid px-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm mt-3">
                    <thead class="">
                        <tr class="text-center table-head-columns">
                            <th class="bg-primary text-white" scope="col">Teacher's Name</th>
                            <th class="bg-primary text-white" scope="col">Email</th>
                            <th class="bg-primary text-white" scope="col">Address</th>
                            <th class="bg-primary text-white" scope="col">Birthday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->rowCount() > 0) {
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $middleName = !empty($row['middle_name']) ? ' ' . substr($row['middle_name'], 0, 1) . '.' : '';
                                $fullName = $row['first_name'] . $middleName . ' ' . $row['last_name'];
                        ?>
                            <tr>
                                <td><?php echo $fullName; ?></td>
                                <td><?php echo maskEmail($row['email']); ?></td>
                                <td><?php echo $row['address'] ?? 'Not provided'; ?></td>
                                <td><?php echo $row['birthday'] ?? 'Not provided'; ?></td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">No teachers found</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>    
    </div>
    
</main>

<?php
include './includes/footer.php';
?>
