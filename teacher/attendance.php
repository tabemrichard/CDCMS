<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';

// Get selected date (default to today if not specified)
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get filter value for kinder level
$kinderFilter = isset($_GET['kinder_level']) ? $_GET['kinder_level'] : '';

// Pagination settings
$itemsPerPage = 10; // Number of students per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;
$offset = ($currentPage - 1) * $itemsPerPage;

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

// Get students with their attendance status for the selected date
$query = "SELECT s.id, s.student_id as student_number, 
          CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS full_name,
          e.schedule as kinder_level,
          a.status
          FROM student s
          JOIN enrollment e ON s.id = e.student_id
          LEFT JOIN attendance a ON s.id = a.student_id AND DATE(a.date) = :selected_date
          WHERE 1=1" . $whereClause . " 
          ORDER BY s.lastname, s.firstname
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':selected_date', $selectedDate);

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
function buildPaginationUrl($page, $date, $kinderLevel) {
    $params = [
        'page' => $page,
        'date' => $date
    ];
    
    if (!empty($kinderLevel)) {
        $params['kinder_level'] = $kinderLevel;
    }
    
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


    <div class="container-fluid py-3">
        <!-- Welcome Section -->
        <div class="welcome-section d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Attendance</h3>
            <button class="btn btn-primary btn-sm px-4" onclick="printAttendance()"><i class="fa-solid fa-print"></i> Print</button>
        </div>

        <div class="container-fluid px-4">
            <!-- Date Picker and Filter -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <label for="attendance-date" style="margin: 2px 5px 0 8px;">Select Date:</label>
                        <input type="date" id="attendance-date" class="form-control w-50" value="<?php echo $selectedDate; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <form method="GET" id="filter-form" class="d-flex align-items-center justify-content-end" style="gap: 5px;">
                        <input type="hidden" name="date" value="<?php echo $selectedDate; ?>">
                        <label for="kinder_filter" style="margin: 2px 0 0 8px ;">Filter by Kinder Level:</label>
                        <select name="kinder_level" id="kinder_filter" class="form-control w-auto">
                            <option value="">All</option>
                            <option value="K1" <?php echo $kinderFilter === 'K1' ? 'selected' : ''; ?>>K1</option>
                            <option value="K2" <?php echo $kinderFilter === 'K2' ? 'selected' : ''; ?>>K2</option>
                            <option value="K3" <?php echo $kinderFilter === 'K3' ? 'selected' : ''; ?>>K3</option>
                        </select>
                        <button type="submit" class="btn btn-primary ms-2">Filter</button>
                    </form>
                </div>
            </div>

            <div  id="attendanceTableContainer" class="table-responsive">
                <table id="attendanceTable" class="table table-bordered table-striped table-sm mt-3">
                    <thead>
                        <tr>
                            <th class="bg-primary text-white" scope="col">Student ID</th>
                            <th class="bg-primary text-white" scope="col">Name</th>
                            <th class="bg-primary text-white" scope="col">Kinder Level</th>
                            <th class="bg-primary text-white" scope="col">Status</th>
                            <th class="bg-primary text-white action-column" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['kinder_level']); ?></td>
                                    <td id="status-<?php echo $student['id']; ?>">
                                        <?php if ($student['status']): ?>
                                            <span class="badge text-white <?php echo $student['status'] === 'Present' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo htmlspecialchars($student['status']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Marked</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-column">
                                        <button class="btn btn-sm btn-success change-attendance"
                                                data-student="<?php echo $student['id']; ?>"
                                                data-date="<?php echo $selectedDate; ?>"
                                                data-status="Present">
                                            Present
                                        </button>
                                        <button class="btn btn-sm btn-danger change-attendance"
                                                data-student="<?php echo $student['id']; ?>"
                                                data-date="<?php echo $selectedDate; ?>"
                                                data-status="Absent">
                                            Absent
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No students found.</td>
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
                        <a class="page-link" href="<?php echo buildPaginationUrl(1, $selectedDate, $kinderFilter); ?>">&laquo; First</a>
                    </li>
                    <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo buildPaginationUrl($currentPage - 1, $selectedDate, $kinderFilter); ?>">Previous</a>
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
                                <a class="page-link" href="<?php echo buildPaginationUrl($i, $selectedDate, $kinderFilter); ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo buildPaginationUrl($currentPage + 1, $selectedDate, $kinderFilter); ?>">Next</a>
                    </li>
                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo buildPaginationUrl($totalPages, $selectedDate, $kinderFilter); ?>">Last &raquo;</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Date picker change event
        $('#attendance-date').change(function() {
            const selectedDate = $(this).val();
            const kinderLevel = $('#kinder_filter').val();
            
            // Redirect to the same page with the new date
            window.location.href = `attendance.php?date=${selectedDate}&kinder_level=${kinderLevel}`;
        });
        
        // Change attendance status
        $('.change-attendance').click(function() {
            const studentId = $(this).data('student');
            const date = $(this).data('date');
            const status = $(this).data('status');
            const statusCell = $(`#status-${studentId}`);
            
            // Show loading indicator
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request to update attendance
            $.ajax({
                url: './attendance/update_attendance.php',
                type: 'POST',
                data: {
                    student_id: studentId,
                    date: date,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update the status cell
                        const badgeClass = status === 'Present' ? 'bg-success' : 'bg-danger';
                        statusCell.html(`<span class="badge ${badgeClass}">${status}</span>`);
                        
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while updating attendance.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

    function printAttendance() {
        let printWindow = window.open('', '_blank');
        let tableClone = document.getElementById('attendanceTable').cloneNode(true);

        // Remove the "Action" column
        let actionColumns = tableClone.querySelectorAll('.action-column');
        actionColumns.forEach(col => col.remove());

        let selectedDate = document.getElementById('attendance-date').value;
        let formattedDate = new Date(selectedDate).toLocaleDateString('en-US', { 
            year: 'numeric', month: 'long', day: 'numeric' 
        });

        printWindow.document.write(`
            <html>
                <head>
                    <title>Attendance Summary</title>

                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid black; padding: 8px; text-align: left; }
                        th { background-color: #007bff; color: white; }
                    </style>
                </head>
                <body>
                    <h2>Attendance Summary</h2>
                    <p><strong>Date:</strong> ${formattedDate}</p>
                    ${tableClone.outerHTML}
                    <script>
                        window.onload = function() { window.print(); setTimeout(() => window.close(), 500); };
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>

<?php
include './includes/footer.php';
?>




