<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';



// Get filter value for kinder level
$kinderFilter = isset($_GET['kinder_level']) ? $_GET['kinder_level'] : '';

// Get all kinder levels for the filter dropdown
$kinderLevelsQuery = "SELECT DISTINCT schedule FROM enrollment ORDER BY schedule";
$stmt = $pdo->prepare($kinderLevelsQuery);
$stmt->execute();
$kinderLevels = $stmt->fetchAll();

// Build the query
$whereClause = "";
$params = [];

if (!empty($kinderFilter)) {
    $whereClause = " AND e.schedule = :kinder_level";
    $params[':kinder_level'] = $kinderFilter;
}

// Get students with their evaluation scores
$query = "SELECT s.id, s.student_id as student_number, 
          CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', s.lastname, ' ', IFNULL(s.suffix, '')) AS full_name,
          e.schedule as kinder_level,
          (SELECT SUM(gross_motor_score + fine_motor_score + self_help_score + 
                      receptive_language_score + expressive_language_score + 
                      cognitive_score + socio_emotional_score) 
           FROM student_evaluation 
           WHERE student_id = s.id AND evaluation_period = '1st') as first_evaluation_total,
          (SELECT SUM(gross_motor_score + fine_motor_score + self_help_score + 
                      receptive_language_score + expressive_language_score + 
                      cognitive_score + socio_emotional_score) 
           FROM student_evaluation 
           WHERE student_id = s.id AND evaluation_period = '2nd') as second_evaluation_total
          FROM student s
          JOIN enrollment e ON s.id = e.student_id
          WHERE 1=1" . $whereClause . " 
          ORDER BY s.lastname, s.firstname";

$stmt = $pdo->prepare($query);

// Bind filter parameter if it exists
if (!empty($params)) {
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
}

$stmt->execute();
$students = $stmt->fetchAll();

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
        <div class="welcome-section d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Grades</h3>
            <form class="mr-3" method="get">
                <label for="kinder_level">Filter by Kinder Level:</label>
                <select name="kinder_level" id="kinder_level" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                    <option value="">All Levels</option>
                    <?php foreach ($kinderLevels as $level): ?>
                        <option value="<?php echo htmlspecialchars($level['schedule']); ?>" 
                                <?php echo $kinderFilter === $level['schedule'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level['schedule']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="container-fluid px-4">
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th class="bg-primary text-white" scope="col">Student ID</th>
                        <th class="bg-primary text-white" scope="col">Kinder Level</th>
                        <th class="bg-primary text-white" scope="col">Full Name</th>
                        <th class="bg-primary text-white" scope="col">1st Evaluation (SS)</th>
                        <th class="bg-primary text-white" scope="col">2nd Evaluation (SS)</th>
                        <th class="bg-primary text-white" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['kinder_level']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td>
                                    <?php if ($student['first_evaluation_total']): ?>
                                        <span class="badge bg-success"><?php echo $student['first_evaluation_total']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Not Evaluated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($student['second_evaluation_total']): ?>
                                        <span class="badge bg-success"><?php echo $student['second_evaluation_total']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Not Evaluated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="./view_grades.php?id=<?php echo $student['id']; ?>" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                    <a href="./update_grades.php?id=<?php echo $student['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-edit"></i> Update
                                    </a>
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
    

</main>T

<?php
include './includes/footer.php';

?>




