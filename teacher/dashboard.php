<?php
session_start();
$pageTitle = "Teacher Dashboard";
require_once '../config/database.php';

try {
    // Fetch total number of guardians
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_guardians FROM guardian_account WHERE isConfirm = 1");
    $stmt->execute();
    $total_guardians = $stmt->fetch(PDO::FETCH_ASSOC)['total_guardians'];

    // Fetch total number of enrolled students
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_enrolled FROM student");
    $stmt->execute();
    $total_enrolled = $stmt->fetch(PDO::FETCH_ASSOC)['total_enrolled'];

    // Fetch total number of Parent Portal users
    // $stmt = $pdo->prepare("SELECT COUNT(*) AS total_parents FROM parent_portal");
    // $stmt->execute();
    // $total_parents = $stmt->fetch(PDO::FETCH_ASSOC)['total_parents'];

    // Fetch requirements status
     // Fetch requirements status: check if any of the required fields are NULL or empty
    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN psa IS NULL OR psa = '' OR 
                        recentphoto IS NULL OR recentphoto = '' OR 
                        immunizationcard IS NULL OR immunizationcard = '' OR 
                        guardianqcid IS NULL OR guardianqcid = '' THEN 1 ELSE 0 END) AS incomplete_count,
            SUM(CASE WHEN psa IS NOT NULL AND psa <> '' AND 
                        recentphoto IS NOT NULL AND recentphoto <> '' AND 
                        immunizationcard IS NOT NULL AND immunizationcard <> '' AND 
                        guardianqcid IS NOT NULL AND guardianqcid <> '' THEN 1 ELSE 0 END) AS complete_count
        FROM enrollment
    ");
    $stmt->execute();
    $requirements_status = $stmt->fetch(PDO::FETCH_ASSOC);;

    // Fetch attendance summary
    $stmt = $pdo->prepare("SELECT 
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_count,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_count,
        COUNT(*) AS total_students
    FROM attendance
    WHERE DATE(date) = CURDATE()
    ");
    $stmt->execute();
    $attendance_summary = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = $e->getMessage();
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
            <h3 class="mb-0">Welcome to Child Development Center Management System</h3>
            <h3 class="text-muted"></h3>
        </div>

        <!-- Dashboard Section -->
        <div class="container-fluid px-4">
            <h5 class="mb-4 font-weight-bold">DASHBOARD</h5>
            
            <div class="row">
                <!-- Guardian Card -->
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card h-100">
                        <h5 class="mb-0"><i class="bi bi-people fs-3"></i> GUARDIAN</h5>
                        <h3 class="mb-0 pe-4 text-primary"><?php echo $total_guardians ?></h3>
                    </div>
                </div>
                
                <!-- Enrolled Card -->
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card h-100">
                        <h5 class="mb-0"><i class="bi bi-person-check fs-3"></i> ENROLLED</h5>
                        <h3 class="mb-0 pe-4 text-primary"><?php echo $total_enrolled ?></h3>
                    </div>
                </div>
                
                <!-- Parent Portal Card -->
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card h-100">
                        <h5 class="mb-0"><i class="bi bi-display fs-3"></i> Parent Portal</h5>
                        <h3 class="mb-0 pe-4 text-primary">5</h3>
                    </div>
                </div>
            </div>
            
            <!-- Student Analytics Card -->
            <div class="row">
                <div class="col-12">
                    <div class="analytics-card">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-bar-chart fs-4 me-2"></i> Student Analytics</h5>
                        </div>
                        <div class="analytics-content">
                            <!-- Analytics content would go here -->
                            <p class="text-secondary">Student performance analytics and metrics will be displayed here.</p>
                        
                            <div class="mt-5 d-flex justify-content-around">
                                <div>
                                    <h6>Requirements Status of the Students</h6>
                                    <div id="requirementsChart" data-complete="<?php echo $requirements_status['complete_count'] ?>" data-incomplete="<?php echo $requirements_status['incomplete_count'] ?>"></div>
                                </div>

                                <div>
                                    <h6>Attendance Summary</h6>
                                    <div id="attendanceSummary" data-present="<?php echo $attendance_summary['present_count'] ?>" data-absent="<?php echo $attendance_summary['absent_count'] ?>" data-total="<?php echo $attendance_summary['total_students'] ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    // Render Requirements Chart
    (function () {
        const chartContainer = document.querySelector("#requirementsChart");
        if (chartContainer) {
            let completeCount = parseInt(chartContainer.dataset.complete) || 0;
            let incompleteCount = parseInt(chartContainer.dataset.incomplete) || 0;

            renderPieChart(
                "requirementsChart",
                [completeCount, incompleteCount],
                [`${completeCount} Complete`, `${incompleteCount} Incomplete`],
                ['#191d67', '#dc3545']
            );
        }
    })();

    // Render Attendance Summary Chart
    (function () {
        const chartContainer = document.querySelector("#attendanceSummary");
        if (chartContainer) {
            let presentCount = parseInt(chartContainer.dataset.present) || 0;
            let absentCount = parseInt(chartContainer.dataset.absent) || 0;

            renderPieChart(
                "attendanceSummary",
                [presentCount, absentCount],
                [`${presentCount} Present`, `${absentCount} Absent`],
                ['#28a745', '#dc3545']
            );
        }
    })();
        
    });
</script>

<?php
include './includes/footer.php';

?>




