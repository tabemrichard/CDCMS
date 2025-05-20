<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
$pageTitle = "Guardian Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';



include './includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/user_navbar.css">

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

// Get student information and grades based on guardian
try {
    $sql = "SELECT 
                s.id AS student_id,
                CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname) AS fullname,
                MAX(e.schedule) AS kinder_level,
                MAX(CASE WHEN se.evaluation_period = '1st' THEN 
                    (se.gross_motor_score + se.fine_motor_score + se.self_help_score + 
                    se.receptive_language_score + se.expressive_language_score + 
                    se.cognitive_score + se.socio_emotional_score)
                END) AS first_evaluation,
                MAX(CASE WHEN se.evaluation_period = '2nd' THEN 
                    (se.gross_motor_score + se.fine_motor_score + se.self_help_score + 
                    se.receptive_language_score + se.expressive_language_score + 
                    se.cognitive_score + se.socio_emotional_score)
                END) AS second_evaluation
            FROM student s
            JOIN enrollment e ON s.id = e.student_id
            JOIN guardian_info g ON s.id = g.student_id
            LEFT JOIN student_evaluation se ON s.id = se.student_id
            WHERE g.firstname LIKE :firstname 
            AND (g.middlename LIKE :middlename OR (g.middlename IS NULL AND :middlename = ''))
            AND g.lastname LIKE :lastname
            GROUP BY s.id, s.firstname, s.middlename, s.lastname";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':firstname', $guardianFirstName, PDO::PARAM_STR);
    $stmt->bindValue(':middlename', empty($guardianMiddleName) ? '' : $guardianMiddleName, PDO::PARAM_STR);
    $stmt->bindValue(':lastname', $guardianLastName, PDO::PARAM_STR);
    $stmt->execute();
    $students_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}

?>

<main role="main" class="main-content">
            
    <!-- Page Content Here -->
    <div class="container-fluid py-3">
        <div class="welcome-section d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Grades</h3>
        </div>

        <div class="container-fluid px-4 mt-3">
            <?php if (empty($students_grades)): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No grades available</h4>
                    <p class="text-muted">There are no grades available for your student(s) at this time.</p>
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
                            <th class="bg-primary text-white" scope="col">1st Evaluation (SS)</th>
                            <th class="bg-primary text-white" scope="col">2nd Evaluation (SS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students_grades as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['kinder_level']); ?></td>
                                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td><?php echo $row['first_evaluation'] ? htmlspecialchars($row['first_evaluation']) : 'Not yet evaluated'; ?></td>
                                <td><?php echo $row['second_evaluation'] ? htmlspecialchars($row['second_evaluation']) : 'Not yet evaluated'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    

</main>T

<?php
include './includes/footer.php';

?>




