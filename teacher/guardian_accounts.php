<?php
session_start();
$pageTitle = "Teacher Dashboard";
// include '../includes/header.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_GET['approve_id'])) {
    $guardianId = $_GET['approve_id'];

    try {
        $stmt = $pdo->prepare("UPDATE guardian_account SET isConfirm = 1 WHERE id = ?");
        $stmt->execute([$guardianId]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Guardian account approved successfully!";
        } else {
            $_SESSION['error'] = "Error: Guardian account not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
    }

    // 🔹 Redirect but keep session messages persistent
    header("Location: ./guardian_accounts.php");
    exit;
}

function checkResidency($firstname, $surname, $checkAsChild = false) {
    $censusURL = 'https://backend-api-5m5k.onrender.com/api/resident';

    try {
        $response = fetchCensusData($censusURL);

        if (!$response || !isset($response['data']) || !is_array($response['data'])) {
            throw new Exception('Invalid API response');
        }

        $inputFirst = strtolower(trim($firstname));
        $inputLast = strtolower(trim($surname));

        foreach ($response['data'] as $item) {
            // If checking as resident (default behavior)
            if (!$checkAsChild) {
                $apiFirst = strtolower(trim($item['firstName']));
                $apiLast = strtolower(trim($item['lastName']));

                if ($apiFirst === $inputFirst && $apiLast === $inputLast) {
                    return true;
                }
            }

            // If checking inside householdMembers as child
            if ($checkAsChild && isset($item['householdMembers']) && is_array($item['householdMembers'])) {
                foreach ($item['householdMembers'] as $member) {
                    if (
                        isset($member['relationship'], $member['firstname'], $member['lastname']) &&
                        strtolower($member['relationship']) === 'child'
                    ) {
                        $childFirst = strtolower(trim($member['firstname']));
                        $childLast = strtolower(trim($member['lastname']));

                        if ($childFirst === $inputFirst && $childLast === $inputLast) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;

    } catch (Exception $e) {
        error_log('Error in checkResidency: ' . $e->getMessage());
        return false;
    }
}




$stmt = $pdo->prepare("SELECT id, guardian_id, email, isConfirm FROM guardian_account WHERE password IS NOT NULL AND password != '' AND isConfirm = 0");
$stmt->execute();
$guardians = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <div class="welcome-section d-flex align-items-center justify-content-between">
            <h3 class="mb-0">Guardian Accounts</h3>
        </div>

        <div class="container-fluid px-4">
            <?php if (empty($guardians)): ?>
                <div class="card shadow-sm mt-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No accounts need to be approve</h4>
                        <p class="text-muted">There are no new accounts created at this time.</p>
                    </div>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm mt-3">
                    <thead class="">
                        <tr class="text-center table-head-columns">
                            <th class="bg-primary text-white" scope="col">Guardian ID</th>
                            <th class="bg-primary text-white" scope="col">Email</th>
                            <th class="bg-primary text-white" scope="col">Residency</th>
                            <th class="bg-primary text-white" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($guardians as $guardian): 
                                $guardianId = $guardian['guardian_id'];
                                $query = "SELECT firstname, lastname FROM guardian_info WHERE id = ?";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute([$guardianId]);
                                $guardianInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                                $isResident = checkResidency($guardianInfo['firstname'], $guardianInfo['lastname']);
                                
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($guardian['guardian_id']); ?></td>
                                    <td><?php echo htmlspecialchars($guardian['email']); ?></td>
                                    <td>
                                        <?php if ($isResident): ?>
                                            <span class="badge bg-success">Quezon City Resident</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger text-white">Not Quezon City Resident</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="guardian_accounts.php?approve_id=<?php echo $guardian['id']; ?>" class='btn btn-primary btn-sm approve-btn'>Approve</a>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div> 
    </div>


</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
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
    });
</script>

<?php
include './includes/footer.php';

?>




