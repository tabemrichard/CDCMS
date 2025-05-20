<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';

// Pagination settings
$itemsPerPage = 10; // Number of students per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get filter value for kinder level (from schedule field)
$kinderFilter = isset($_GET['kinder_level']) ? $_GET['kinder_level'] : '';

// Build the query
$whereClause = "";
$params = [];

if (!empty($kinderFilter)) {
    $whereClause = " AND e.schedule LIKE :kinder_level";
    $params[':kinder_level'] = '%' . $kinderFilter . '%';
}

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total 
               FROM student s 
               JOIN enrollment e ON s.id = e.student_id
               WHERE 1=1" . $whereClause;
$stmt = $pdo->prepare($countQuery);

if (!empty($params)) {
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
}

$stmt->execute();
$totalItems = $stmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Ensure current page doesn't exceed total pages
if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $itemsPerPage;
}

// Get students with pagination
$query = "SELECT s.id, s.student_id, 
          CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS full_name, 
          CONCAT(g.firstname, ' ', IFNULL(g.middlename, ''), ' ', g.lastname) AS guardian_name, 
          s.healthhistory, 
          e.schedule AS kinder_level,
          CASE 
            WHEN e.psa IS NOT NULL AND e.immunizationcard IS NOT NULL AND 
                 e.recentphoto IS NOT NULL AND e.guardianqcid IS NOT NULL 
            THEN 'Complete' 
            ELSE 'Incomplete' 
          END AS requirements_status
          FROM student s
          JOIN enrollment e ON s.id = e.student_id
          JOIN guardian_info g ON s.id = g.student_id
          WHERE 1=1" . $whereClause . " 
          ORDER BY e.schedule 
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);

// Bind pagination parameters
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Bind filter parameter if it exists
if (!empty($params)) {
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
}

$stmt->execute();
$students = $stmt->fetchAll();

// Build pagination URL
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

// Account Deletion
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $studentId = (int) $_GET['id'];

    // Prepare the SQL statement to delete the student
    $deleteQuery = "DELETE FROM student WHERE id = :id";
    $stmt = $pdo->prepare($deleteQuery);

    try {
        // Execute the deletion
        $stmt->bindValue(':id', $studentId, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the student management page with a success message
        $_SESSION['success'] = "Student record deleted successfully.";
        header("Location: student_management.php");
        exit;
    } catch (PDOException $e) {
        // Handle any errors
        $_SESSION['error'] = "Error deleting student record: " . $e->getMessage();
        header("Location: student_management.php");
        exit;
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
        <div class="welcome-section">
            <h3 class="mb-0">Student Management</h3>
        </div>

        <!-- Student Table -->
        <div class="container-fluid px-4">
            <!-- Filter Form -->
            <form method="GET" class="my-3">
                <label for="kinder_filter">Filter by Kinder Level:</label>
                <select name="kinder_level" id="kinder_filter" class="form-control w-auto d-inline">
                    <option value="">All</option>
                    <option value="K1" <?php echo $kinderFilter === 'K1' ? 'selected' : ''; ?>>Kinder 1</option>
                    <option value="K2" <?php echo $kinderFilter === 'K2' ? 'selected' : ''; ?>>Kinder 2</option>
                    <option value="K3" <?php echo $kinderFilter === 'K3' ? 'selected' : ''; ?>>Kinder 3</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm mt-3">
                    <thead>
                        <tr class="text-center table-head-columns">
                            <th class="bg-primary text-white">Student I.D.</th>
                            <th class="bg-primary text-white">Full Name</th>
                            <th class="bg-primary text-white">Guardian</th>
                            <th class="bg-primary text-white">Health History</th>
                            <th class="bg-primary text-white">Kinder Level</th>
                            <th class="bg-primary text-white">Requirements</th>
                            <th class="bg-primary text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['guardian_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['healthhistory'] ?? 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($student['kinder_level']); ?></td>
                                    <td class="<?php echo $student['requirements_status'] === 'Complete' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo htmlspecialchars($student['requirements_status']); ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDeletion(<?php echo $student['id']; ?>)">
                                            <i class="bi bi-trash"></i> Delete Application
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo buildPaginationUrl(1); ?>">&laquo; First</a>
                    </li>
                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo buildPaginationUrl($currentPage - 1); ?>">Previous</a>
                    </li>

                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <?php if ($i === $currentPage): ?>
                                <span class="page-link"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a class="page-link" href="<?php echo buildPaginationUrl($i); ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo buildPaginationUrl($currentPage + 1); ?>">Next</a>
                    </li>
                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo buildPaginationUrl($totalPages); ?>">Last &raquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>

<?php
include './includes/footer.php';
?>

<script>
    function confirmDeletion(studentId) {
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
                // Redirect to the PHP script with the student ID and action
                window.location.href = `student_management.php?id=${studentId}&action=delete`;
            }
        });
    }

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




