<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';


// Get guardian name from form or URL parameters
// $guardianFirstName = isset($_POST['guardian_firstname']) ? strtolower(trim($_POST['guardian_firstname'])) : '';
// $guardianMiddleName = isset($_POST['guardian_middlename']) ? strtolower(trim($_POST['guardian_middlename'])) : '';
// $guardianLastName = isset($_POST['guardian_lastname']) ? strtolower(trim($_POST['guardian_lastname'])) : '';

$guardianFirstName = $_SESSION['guardian_firstname'] ?? '';
$guardianMiddleName = $_SESSION['guardian_middlename'] ?? '';
$guardianLastName = $_SESSION['guardian_lastname'] ?? '';

// $guardianFirstName ='Johnpaul';
// $guardianMiddleName = 'Araceli';
// $guardianLastName = 'Daniel';

// Redirect if required fields are empty
// if (empty($guardianFirstName) || empty($guardianLastName)) {
//     header("Location: index.php");
//     exit();
// }

// Prepare the SQL statement
$sql = "SELECT 
            s.student_id AS student_id,
            SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_count,
            SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent_count,
            CONCAT(s.firstname, ' ', COALESCE(s.middlename, ''), ' ', s.lastname, ' ', COALESCE(s.suffix, '')) AS student_name,
            DATE_FORMAT(a.date, '%M %d, %Y') AS date,
            a.status AS status
        FROM 
            attendance a
        INNER JOIN 
            student s ON a.student_id = s.id
        INNER JOIN 
            guardian_info g ON s.id = g.student_id
        WHERE 
            LOWER(g.firstname) LIKE LOWER(:firstname)
            AND LOWER(COALESCE(g.middlename, '')) LIKE LOWER(:middlename)
            AND LOWER(g.lastname) LIKE LOWER(:lastname)
        GROUP BY 
            s.student_id, student_name, date, a.status
        ORDER BY 
            a.date DESC;";

$stmt = $pdo->prepare($sql);

// Bind parameters with wildcard search
$stmt->execute([
    'firstname' => $guardianFirstName,
    'middlename' => empty($guardianMiddleName) ? '' : $guardianMiddleName,
    'lastname' => $guardianLastName
]);

$attendanceRecords = $stmt->fetchAll();
$present = 0;
$absent = 0;


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
        <div class="welcome-section d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Attendance</h3>
            <button class="btn btn-primary btn-sm px-4" onclick="printAttendance()"><i class="fa-solid fa-print"></i> Print</button>

        </div>


        <?php if (!empty($attendanceRecords)): ?>
            <div class="container-fluid px-4 mt-3">
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-bordered table-striped table-sm">
                        <thead class="">
                            <tr class="text-center table-head-columns">
                                <th class="bg-primary text-white" scope="col">Student ID</th>
                                <th class="bg-primary text-white" scope="col">Student Name</th>
                                <th class="bg-primary text-white" scope="col">Date</th>
                                <th class="bg-primary text-white" scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($attendanceRecords as $record): 
                            if ($record['status'] === 'Present') {
                                $present += 1;
                            } else {
                                $absent += 1;
                            }
                            ?>
                                
                                <tr>
                                    <td class="px-2 text-center"><?= htmlspecialchars($record['student_id']) ?></td>
                                    <td><?= htmlspecialchars($record['student_name']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($record['date']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($record['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-block mt-4" >
                    <h6 class="text-center">Attendance Summary</h6>
                    <div class="mx-auto w-25" id="attendanceSummary" data-present="<?php echo $present ?>" data-absent="<?php echo $absent ?>" data-total="<?php echo $attendance_summary['total_students'] ?>"></div>
                </div>
            </div>  
        <?php else: ?>
            <div class="container-fluid px-4 mt-5">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No Attendance available <?php ?></h4>
                        <p class="text-muted">Please contact the administrator if you believe this is an error.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Function to render a pie chart
    function renderPieChart(containerId, seriesData, labelsData, colors) {
        const chartContainer = document.querySelector(`#${containerId}`);

        if (!chartContainer) {
            console.error(`Chart container #${containerId} not found!`);
            return;
        }

        var options = {
            series: seriesData,
            chart: {
                width: 380,
                type: 'pie',
            },
            labels: labelsData,
            colors: colors,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(chartContainer, options);
        chart.render();
    }

    // Render Attendance Summary Chart
        (function () {
            const chartContainer = document.querySelector("#attendanceSummary");
            if (chartContainer) {
                let presentCount = parseInt(chartContainer.dataset.present) || 0;
                let absentCount = parseInt(chartContainer.dataset.absent) || 0;

                renderPieChart(
                    "attendanceSummary",
                    [presentCount, absentCount],
                    [`Present`, `Absent`],
                    ['#28a745', '#dc3545']
                );
            }
        })();
            
    });

    function printAttendance() {
        let printWindow = window.open('', '_blank');
        let tableClone = document.getElementById('attendanceTable').cloneNode(true);

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




