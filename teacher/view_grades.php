<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';
// require_once '../includes/functions.php';

// if (!isLoggedIn() || !hasRole('teacher')) {
//     header('Location: ../login.php');
//     exit;
// }

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: grades.php");
    exit;
}

$studentId = (int)$_GET['id'];

// Get student information
$query = "SELECT s.id, s.student_id as student_number, 
          CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS full_name,
          e.schedule as kinder_level
          FROM student s
          JOIN enrollment e ON s.id = e.student_id
          WHERE s.id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $studentId, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch();

// If student not found, redirect back to list
if (!$student) {
    header("Location: grades.php");
    exit;
}

// Get first evaluation scores
$query = "SELECT * FROM student_evaluation 
          WHERE student_id = :student_id AND evaluation_period = '1st'";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
$stmt->execute();
$firstEvaluation = $stmt->fetch();

// Get second evaluation scores
$query = "SELECT * FROM student_evaluation 
          WHERE student_id = :student_id AND evaluation_period = '2nd'";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
$stmt->execute();
$secondEvaluation = $stmt->fetch();

// Get recommendations
$query = "SELECT * FROM recommendation 
          WHERE student_id = :student_id 
          ORDER BY evaluation_period";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
$stmt->execute();
$recommendations = $stmt->fetchAll();

// Calculate totals
$firstEvaluationTotal = 0;
$secondEvaluationTotal = 0;

if ($firstEvaluation) {
    $firstEvaluationTotal = 
        $firstEvaluation['gross_motor_score'] + 
        $firstEvaluation['fine_motor_score'] + 
        $firstEvaluation['self_help_score'] + 
        $firstEvaluation['receptive_language_score'] + 
        $firstEvaluation['expressive_language_score'] + 
        $firstEvaluation['cognitive_score'] + 
        $firstEvaluation['socio_emotional_score'];
}

if ($secondEvaluation) {
    $secondEvaluationTotal = 
        $secondEvaluation['gross_motor_score'] + 
        $secondEvaluation['fine_motor_score'] + 
        $secondEvaluation['self_help_score'] + 
        $secondEvaluation['receptive_language_score'] + 
        $secondEvaluation['expressive_language_score'] + 
        $secondEvaluation['cognitive_score'] + 
        $secondEvaluation['socio_emotional_score'];
}

// Function to get recommendation by period
function getRecommendation($recommendations, $period) {
    foreach ($recommendations as $rec) {
        if ($rec['evaluation_period'] === $period) {
            return $rec['recommendation'];
        }
    }
    return null;
}

$firstRecommendation = getRecommendation($recommendations, '1st');
$secondRecommendation = getRecommendation($recommendations, '2nd');



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
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Student Evaluation Report</h2>
                <a href="grades.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back to Grades
                </a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Student Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_number']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Kinder Level:</strong> <?php echo htmlspecialchars($student['kinder_level']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">1st Evaluation</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($firstEvaluation): ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Gross Motor Skills</th>
                                        <td><?php echo $firstEvaluation['gross_motor_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fine Motor Skills</th>
                                        <td><?php echo $firstEvaluation['fine_motor_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Self-Help Skills</th>
                                        <td><?php echo $firstEvaluation['self_help_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Receptive Language</th>
                                        <td><?php echo $firstEvaluation['receptive_language_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Expressive Language</th>
                                        <td><?php echo $firstEvaluation['expressive_language_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Cognitive Skills</th>
                                        <td><?php echo $firstEvaluation['cognitive_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Socio-Emotional Skills</th>
                                        <td><?php echo $firstEvaluation['socio_emotional_score']; ?></td>
                                    </tr>
                                    <tr class="table-success">
                                        <th>Total Score</th>
                                        <td><strong><?php echo $firstEvaluationTotal; ?></strong></td>
                                    </tr>
                                </table>
                                
                                <?php if ($firstRecommendation): ?>
                                    <div class="mt-3">
                                        <h5>AI Recommendation:</h5>
                                        <div class="p-3 bg-light rounded">
                                            <?php echo nl2br(htmlspecialchars($firstRecommendation)); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No evaluation data available for the 1st evaluation period.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0">2nd Evaluation</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($secondEvaluation): ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Gross Motor Skills</th>
                                        <td><?php echo $secondEvaluation['gross_motor_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fine Motor Skills</th>
                                        <td><?php echo $secondEvaluation['fine_motor_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Self-Help Skills</th>
                                        <td><?php echo $secondEvaluation['self_help_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Receptive Language</th>
                                        <td><?php echo $secondEvaluation['receptive_language_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Expressive Language</th>
                                        <td><?php echo $secondEvaluation['expressive_language_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Cognitive Skills</th>
                                        <td><?php echo $secondEvaluation['cognitive_score']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Socio-Emotional Skills</th>
                                        <td><?php echo $secondEvaluation['socio_emotional_score']; ?></td>
                                    </tr>
                                    <tr class="table-info">
                                        <th>Total Score</th>
                                        <td><strong><?php echo $secondEvaluationTotal; ?></strong></td>
                                    </tr>
                                </table>
                                
                                <?php if ($secondRecommendation): ?>
                                    <div class="mt-3">
                                        <h5>AI Recommendation:</h5>
                                        <div class="p-3 bg-light rounded">
                                            <?php echo nl2br(htmlspecialchars($secondRecommendation)); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No evaluation data available for the 2nd evaluation period.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="grades.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back to Grades
                </a>
                <a href="update_grades.php?id=<?php echo $student['id']; ?>" class="btn btn-primary">
                    <i class="fa-solid fa-edit"></i> Update Grades
                </a>
            </div>
        </div>
    </div>
</main>

<script>
    document.querySelector('#grades').classList.add("active");
</script>

<?php
include './includes/footer.php';

?>




