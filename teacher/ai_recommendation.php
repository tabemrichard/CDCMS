<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);
session_start();
$pageTitle = "Teacher Dashboard";
require_once '../config/database.php';
include './includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/user_navbar.css">
<link rel="stylesheet" href="../assets/css/ai_recommendation.css">

<?php 
include './includes/navbar.php';
include './includes/sidebar.php';


try {

    $sql = "SELECT DISTINCT schedule as kinder_level FROM enrollment";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $kinder_levels = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$filter_level = $_GET['kinder_level'] ?? 'all';
if (!in_array($filter_level, $kinder_levels)) {
    $filter_level = 'all';
}

$query_filter = '';
if ($filter_level !== 'all') {
    $query_filter = " AND e.schedule = '$filter_level'";
}

function levelSelected($value) {
    global $filter_level;
    if ($value === $filter_level) {
        echo 'selected';
    }
}

$perPage = 10; // Number of records per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Count total records
$sqlCount = "SELECT COUNT(DISTINCT s.id) FROM student_evaluation se
             JOIN enrollment e ON se.student_id = e.student_id
             JOIN student s ON se.student_id = s.id
             WHERE 1=1" . $query_filter;
$totalRecords = $pdo->query($sqlCount)->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);



try {
    $sql = "SELECT DISTINCT
                s.id AS student_id,
                CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname) AS fullname,
                MAX(e.schedule) AS kinder_level,
                GROUP_CONCAT(DISTINCT se.evaluation_period ORDER BY se.evaluation_period ASC) AS evaluation_periods,
                MAX(se.gross_motor_score) AS gross_motor_score,
                MAX(se.fine_motor_score) AS fine_motor_score,
                MAX(se.self_help_score) AS self_help_score,
                MAX(se.receptive_language_score) AS receptive_language_score,
                MAX(se.expressive_language_score) AS expressive_language_score,
                MAX(se.cognitive_score) AS cognitive_score,
                MAX(se.socio_emotional_score) AS socio_emotional_score,
                MAX(se.gross_motor_score + se.fine_motor_score + se.self_help_score + 
                    se.receptive_language_score + se.expressive_language_score + 
                    se.cognitive_score + se.socio_emotional_score) AS total_score,
                GROUP_CONCAT(DISTINCT r.recommendation ORDER BY r.evaluation_period ASC SEPARATOR '|') AS recommendations
            FROM student_evaluation se
            JOIN enrollment e ON se.student_id = e.student_id
            JOIN student s ON se.student_id = s.id
            LEFT JOIN recommendation r ON se.evaluation_period = r.evaluation_period AND s.id = r.student_id
            WHERE 1=1 $query_filter
            GROUP BY s.id, s.firstname, s.middlename, s.lastname
            LIMIT $perPage OFFSET $offset;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $students_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($students_info as &$student) {
        // Split recommendations into an array
        $recommendations = explode('|', $student['recommendations']);
        
        // Assign first and second recommendations (if available)
        $student['recommendation_1'] = isset($recommendations[0]) ? $recommendations[0] : 'No recommendation';
        $student['recommendation_2'] = isset($recommendations[1]) ? $recommendations[1] : 'No recommendation';
    }


} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


?>

<main role="main" class="main-content">
    <?php include_once './includes/notification.php' ?> 

    <div class="container-fluid py-3">
        <div class="welcome-section">
            <h3 class="mb-0">A.I. Recommendation</h3>
        </div>

        <div class="container-fluid px-4 mt-3">
            <form method="get" action="">
                <div class="row">
                    <div class="col-md-4">
                        <label for="kinder_level">Kinder Level:</label>
                        <select name="kinder_level" id="kinder_level" class="form-control">
                            <option value="all" class="kinder_level_option" <?php levelSelected('all') ?> >All</option>
                            <?php foreach ($kinder_levels as $level) { ?>
                            <option value="<?php echo $level ?>" class="kinder_level_option" <?php levelSelected($level) ?> >
                                <?php echo $level ?>
                            </option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary mt-4">Filter</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped table-sm mt-3">
                    <thead>
                        <tr>
                            <th class="bg-primary text-white" scope="col">Student ID</th>
                            <th class="bg-primary text-white" scope="col">Kinder Level</th>
                            <th class="bg-primary text-white" scope="col">Full Name</th>
                            <th class="bg-primary text-white" scope="col">1st Recommendation</th>
                            <th class="bg-primary text-white" scope="col">2nd Recommendation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if (!empty($students_info)) {
                                foreach ($students_info as $row) {
                                    $recommendations = explode('/vA#}v&SEP{#Av/', $row['recommendations']);

                                    echo "<tr class=\"recommendation_row\">
                                        <td>{$row['student_id']}</td>
                                        <td>{$row['kinder_level']}</td>
                                        <td>{$row['fullname']}</td>
                                        <td>" . $row['recommendation_1'] . "</td>
                                        <td>" . $row['recommendation_2'] . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td class='text-center' colspan='5'>No data available</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>


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

<script>
document.addEventListener('click', event => {
    const selectedLevel = document.querySelector('.kinder_level_option[selected]');
    const clickedLevel = event.target.closest('.kinder_level_option');
    if (clickedLevel && selectedLevel.value !== clickedLevel.value) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('kinder_level', clickedLevel.value);
        window.location.href = `?${urlParams.toString()}`
    }
});
</script>

<?php include './includes/footer.php'; ?>
