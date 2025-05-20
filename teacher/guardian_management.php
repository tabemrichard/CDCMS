<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';


// Pagination settings
$itemsPerPage = 10; // Number of guardians per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get filter value for kinder level
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
               FROM guardian_info g
               JOIN student s ON g.student_id = s.id
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

// Get guardians with pagination
$query = "SELECT g.id, 
          CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS student_name,
          g.relationship,
          CONCAT(g.firstname, ' ', IFNULL(g.middlename, ''), ' ', g.lastname) AS guardian_name,
          g.contact_number,
          g.email
          FROM guardian_info g
          JOIN student s ON g.student_id = s.id
          JOIN enrollment e ON s.id = e.student_id
          WHERE 1=1" . $whereClause . " 
          ORDER BY s.lastname, s.firstname
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
$guardians = $stmt->fetchAll();

// Build pagination URL
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);


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
            <h3 class="mb-0">Guardian Management</h3>
        </div>

        <div class="container-fluid px-4">
            <!-- Filter Form -->
            <form method="GET" class="my-3">
                <label for="kinder_filter">Filter by Kinder Level:</label>
                <select name="kinder_level" id="kinder_filter" class="form-control w-auto d-inline">
                    <option value="">All</option>
                    <option value="K1" <?php echo $kinderFilter === 'K1' ? 'selected' : ''; ?>>K1</option>
                    <option value="K2" <?php echo $kinderFilter === 'K2' ? 'selected' : ''; ?>>K2</option>
                    <option value="K3" <?php echo $kinderFilter === 'K3' ? 'selected' : ''; ?>>K3</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm mt-3">
                    <thead class="">
                        <tr class="text-center table-head-columns">
                            <th class="bg-primary text-white" scope="col">Student Name</th>
                            <th class="bg-primary text-white" scope="col">Relationship</th>
                            <th class="bg-primary text-white" scope="col">Guardian Name</th>
                            <th class="bg-primary text-white" scope="col">Contact Number</th>
                            <th class="bg-primary text-white" scope="col">Email</th>
                            <th class="bg-primary text-white" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($guardians) > 0): ?>
                            <?php foreach ($guardians as $guardian): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($guardian['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($guardian['relationship']); ?></td>
                                    <td><?php echo htmlspecialchars($guardian['guardian_name']); ?></td>
                                    <td><?php echo htmlspecialchars($guardian['contact_number']); ?></td>
                                    <td><?php echo htmlspecialchars(maskEmail($guardian['email'])); ?></td>
                                    <td>
                                        <a href="./edit_guardian.php?id=<?php echo $guardian['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-edit"></i> Update
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No guardian records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
    </div>
</main>

<?php
include './includes/footer.php';

?>




