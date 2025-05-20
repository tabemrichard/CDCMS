<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
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


// Get guardian information from session
$guardianFirstName = $_SESSION['guardian_firstname'] ?? '';
$guardianMiddleName = $_SESSION['guardian_middlename'] ?? '';
$guardianLastName = $_SESSION['guardian_lastname'] ?? '';

// $guardianFirstName ='Johnpaul';
// $guardianMiddleName = 'Araceli';
// $guardianLastName = 'Daniel';

// Get student information based on guardian
try {
    $sql = "SELECT 
                s.id AS student_id,
                CONCAT_WS(' ', s.firstname, s.middlename, s.lastname) AS fullname,
                MAX(e.schedule) AS kinder_level,
                MAX(CASE WHEN r.evaluation_period = '1st' THEN r.recommendation END) AS recommendation_1st,
                MAX(CASE WHEN r.evaluation_period = '2nd' THEN r.recommendation END) AS recommendation_2nd
            FROM 
                student s
            JOIN 
                enrollment e ON s.id = e.student_id
            JOIN 
                guardian_info g ON s.id = g.student_id
            LEFT JOIN 
                recommendation r ON s.id = r.student_id
            WHERE g.firstname LIKE :firstname 
            AND (g.middlename LIKE :middlename OR (g.middlename IS NULL AND :middlename = ''))
            AND g.lastname LIKE :lastname
            GROUP BY s.id, s.firstname, s.middlename, s.lastname";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':firstname', $guardianFirstName, PDO::PARAM_STR);
    $stmt->bindValue(':middlename', empty($guardianMiddleName) ? '' : $guardianMiddleName, PDO::PARAM_STR);
    $stmt->bindValue(':lastname', $guardianLastName, PDO::PARAM_STR);
    $stmt->execute();
    $students_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}

?>

<main role="main" class="main-content">
    <div class="container-fluid py-3">
        <div class="welcome-section">
            <h3 class="mb-0">A.I. Recommendation</h3>
        </div>

        <div class="container-fluid px-4 mt-3">
            <?php if (empty($students_info)): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No recommendations available</h4>
                    <p class="text-muted">There are no AI recommendations available for your student(s) at this time.</p>
                </div>
            </div>
            <?php else: ?>
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
                        <?php foreach ($students_info as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['kinder_level']); ?></td>
                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($row['recommendation_1st'] ?? 'No recommendation available'); ?></td>
                                <td><?php echo htmlspecialchars($row['recommendation_2nd']?? 'No recommendation available'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
