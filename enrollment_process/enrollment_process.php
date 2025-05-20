<?php
include_once '../includes/functions.php';
include_once '../config/database.php';
session_start();
header('Content-Type: application/json; charset=UTF-8');
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();
ob_start();
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $lName = sanitize($_POST['lName']);
    $fName = sanitize($_POST['fName']);
    $mName = sanitize($_POST['mName'] ?? NULL);
    $suffix = sanitize($_POST['suffix'] ?? NULL);
    $bDay = sanitize($_POST['bDay']);
    $age = (int)$_POST['age'];
    $sex = sanitize($_POST['sex']);
    $healthHistory = sanitize($_POST['healthHistory'] ?? NULL);
    
    // Address
    $addressNumber = sanitize($_POST['addressNumber']);
    $brgy = sanitize($_POST['brgy']);
    $municipality = sanitize($_POST['municipality']);
    
    // Check if it's a single parent household
    $isSingleParent = isset($_POST['single_parent']) && $_POST['single_parent'] === 'yes';
    
    // Check if parents are marked as N/A
    $fatherNA = isset($_POST['fatherNA']) && $_POST['fatherNA'] === 'on';
    $motherNA = isset($_POST['motherNA']) && $_POST['motherNA'] === 'on';
    
    // Father's info - only process if not N/A and not single parent or if father is guardian in single parent case
    $fatherLName = NULL;
    $fatherFName = NULL;
    $fatherMName = NULL;
    $fatherContactNo = NULL;
    $fatherEmail = NULL;
    $fatherOccupation = NULL;
    
    if (!$fatherNA && (!$isSingleParent || (isset($_POST['guardian_type']) && $_POST['guardian_type'] === 'father'))) {
        $fatherLName = sanitize($_POST['fatherLName'] ?? NULL);
        $fatherFName = sanitize($_POST['fatherFName'] ?? NULL);
        $fatherMName = sanitize($_POST['fatherMName'] ?? NULL);
        $fatherContactNo = sanitize($_POST['fatherContactNo'] ?? NULL);
        $fatherEmail = sanitize($_POST['fatherEmail'] ?? NULL);
        $fatherOccupation = sanitize($_POST['fatherOccupation'] ?? NULL);
    }
    
    // Mother's info - only process if not N/A and not single parent or if mother is guardian in single parent case
    $motherLName = NULL;
    $motherFName = NULL;
    $motherMName = NULL;
    $motherContactNo = NULL;
    $motherEmail = NULL;
    $motherOccupation = NULL;
    
    if (!$motherNA && (!$isSingleParent || (isset($_POST['guardian_type']) && $_POST['guardian_type'] === 'mother'))) {
        $motherLName = sanitize($_POST['motherLName'] ?? NULL);
        $motherFName = sanitize($_POST['motherFName'] ?? NULL);
        $motherMName = sanitize($_POST['motherMName'] ?? NULL);
        $motherContactNo = sanitize($_POST['motherContactNo'] ?? NULL);
        $motherEmail = sanitize($_POST['motherEmail'] ?? NULL);
        $motherOccupation = sanitize($_POST['motherOccupation'] ?? NULL);
    }
    
    // Determine guardian information based on form state
    $guardianLName = NULL;
    $guardianFName = NULL;
    $guardianMName = NULL;
    $guardianContactNo = NULL;
    $guardianRelationship = NULL;
    $guardianEmail = NULL;
    $guardianOccupation = NULL;

    // If it's a single parent household or both parents are N/A (which shouldn't happen now), use the guardian info
    if ($isSingleParent || ($fatherNA && $motherNA)) {
        $guardianLName = sanitize($_POST['guardianLName'] ?? NULL);
        $guardianFName = sanitize($_POST['guardianFName'] ?? NULL);
        $guardianMName = sanitize($_POST['guardianMName'] ?? NULL);
        $guardianContactNo = sanitize($_POST['guardianContactNo'] ?? NULL);
        $guardianRelationship = sanitize($_POST['guardianRelationship'] ?? NULL);
        $guardianEmail = sanitize($_POST['guardianEmail'] ?? NULL);
        $guardianOccupation = sanitize($_POST['guardianOccupation'] ?? NULL);
    } else {
        // Not a single parent household, use the selected parent as guardian
        $guardian_type = sanitize($_POST['guardian_type'] ?? 'father'); // Default to father if not specified
        
        if ($guardian_type === 'father' && !$fatherNA) {
            $guardianLName = $fatherLName;
            $guardianFName = $fatherFName;
            $guardianMName = $fatherMName;
            $guardianContactNo = $fatherContactNo;
            $guardianRelationship = 'Father';
            $guardianEmail = sanitize($_POST['fatherEmail'] ?? NULL); // Directly get from POST
            $guardianOccupation = sanitize($_POST['fatherOccupation'] ?? NULL); // Directly get from POST
        } else if ($guardian_type === 'mother' && !$motherNA) {
            $guardianLName = $motherLName;
            $guardianFName = $motherFName;
            $guardianMName = $motherMName;
            $guardianContactNo = $motherContactNo;
            $guardianRelationship = 'Mother';
            $guardianEmail = sanitize($_POST['motherEmail'] ?? NULL); // Directly get from POST
            $guardianOccupation = sanitize($_POST['motherOccupation'] ?? NULL); // Directly get from POST
        }
    }
    
    // Schedule
    $schedule = sanitize($_POST['schedule']);
    if ($schedule === 'Other' && isset($_POST['otherSchedule'])) {
        $schedule = sanitize($_POST['otherSchedule']);
    }
    
    // Basic validation
    $errors = [];
    
    if (empty($lName) || empty($fName)) {
        $errors[] = "Name fields are required";
    }
    
    if (empty($bDay) || $age < 3 || $age > 5) {
        $errors[] = "Valid birthdate is required (age must be 3-5)";
    }
    
    // Validate guardian information
    if (empty($guardianLName) || empty($guardianFName)) {
        $errors[] = "Guardian name is required";
    }
    
    if (empty($guardianContactNo)) {
        $errors[] = "Guardian contact number is required";
    }
    
    if (empty($guardianEmail)) {
        $errors[] = "Guardian email is required";
    } else if (!validateEmail($guardianEmail)) {
        $errors[] = "Guardian email format is invalid";
    }
    
    if (empty($guardianOccupation)) {
        $errors[] = "Guardian occupation is required";
    }
    
    // Handle file uploads
    $psa = $_FILES['psa'] ?? null;
    $immunizationCard = $_FILES['immunizationCard'] ?? null;
    $recentPhoto = $_FILES['recentPhoto'] ?? null;
    $guardianQCID = $_FILES['guardianQCID'] ?? null;
    
    // Validate required files
    if (!isset($psa) || $psa['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "PSA Birth Certificate is required";
    }
    
    if (!isset($guardianQCID) || $guardianQCID['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Guardian's QC ID is required";
    }
    
    // If no errors, process the enrollment
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Generate student ID
            $year = date('Y');
            $stmt = $pdo->prepare("SELECT COUNT(*) as max_id FROM student");
            $stmt->execute();
            $result = $stmt->fetch();
            $next_id = ($result['max_id'] ?? 0) + 1;
            $formatted_id = str_pad($next_id, 2, '0', STR_PAD_LEFT); 
            $student_id = 'AY2425-' . $formatted_id; 
            
            // Insert student
            $stmt = $pdo->prepare("INSERT INTO student (student_id, firstName, lastName, middleName, suffix, birthDate, age, sex, healthHistory) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $student_id, $fName, $lName, $mName, $suffix, $bDay, $age, $sex, $healthHistory
            ]);
            $student_db_id = $pdo->lastInsertId();
            
            // Insert address
            $stmt = $pdo->prepare("INSERT INTO student_address (student_id, address_no, baranggay, municipality) 
                                VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_db_id, $addressNumber, $brgy, $municipality]);

            if (!$fatherNA && !$isSingleParent) {
                // Insert Father 
                $stmt = $pdo->prepare("INSERT INTO father_info (student_id, firstName, middleName, lastName, contact_number) 
                                        VALUES (?, ?, ?, ?, ?) ");
                $stmt->execute([$student_db_id, $fatherFName, $fatherMName, $fatherLName, $fatherContactNo]);
            }

            if (!$motherNA && !$isSingleParent)  {
                // Insert Mother
                $stmt = $pdo->prepare("INSERT INTO mother_info (student_id, firstName, middleName, lastName, contact_number) 
                                        VALUES (?, ?, ?, ?, ?) ");
                $stmt->execute([$student_db_id, $motherFName, $motherMName, $motherLName, $motherContactNo]);
            }
            

            // Insert guardian
            $stmt = $pdo->prepare("INSERT INTO guardian_info (student_id, relationship, firstName, lastName, middleName, contact_number, email, occupation) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $student_db_id, $guardianRelationship, $guardianFName, $guardianLName, $guardianMName, 
                $guardianContactNo, $guardianEmail, $guardianOccupation
            ]);

            $guardian_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO guardian_account (guardian_id, student_id, email) 
                                VALUES (?, ?, ?)");
            $stmt->execute([$guardian_id, $student_id, $guardianEmail]);
            
            
            // Handle file uploads
            $upload_dir = 'uploads/files/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Function to handle file upload
            function uploadFile($file, $upload_dir, $prefix, $firstName, $lastName) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $tmp_name = $file['tmp_name'];
                    $name = basename($file['name']);
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    $cleanFirstName = preg_replace('/\s+/', '_', strtolower($firstName)); // Remove spaces & lowercase
                    $cleanLastName = preg_replace('/\s+/', '_', strtolower($lastName));
                    
                    // Create user directory
                    $user_dir = "{$upload_dir}{$cleanFirstName}_{$cleanLastName}/{$prefix}/";
                    if (!file_exists($user_dir)) {
                        mkdir($user_dir, 0777, true);
                    }

                    // Set file name format: firstname_lastname_filetype.extension
                    $new_name = "{$cleanFirstName}_{$cleanLastName}_{$prefix}.{$extension}";
                    $destination = $user_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        return $destination; // Save full file path for reference
                    }
                }
                return null;
            }
            
            $upload_dir = 'uploads/files/';
            $psa_file = uploadFile($psa, $upload_dir, 'psa', $fName, $lName);
            $immunization_file = isset($immunizationCard) && $immunizationCard['error'] === UPLOAD_ERR_OK ? 
                                uploadFile($immunizationCard, $upload_dir, 'immunization', $fName, $lName) : null;
            $photo_file = isset($recentPhoto) && $recentPhoto['error'] === UPLOAD_ERR_OK ? 
                        uploadFile($recentPhoto, $upload_dir, 'photo', $fName, $lName) : null;
            $qcid_file = uploadFile($guardianQCID, $upload_dir, 'qcid', $fName, $lName);
            
            // Insert requirements
            $stmt = $pdo->prepare("INSERT INTO enrollment (student_id, schedule, psa, immunizationCard, recentPhoto, guardianQCID) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$student_db_id, $schedule, $psa_file, $immunization_file, $photo_file, $qcid_file]);
            $pdo->commit();
            
            // Send email with student ID
            // This would be implemented with a proper email library like PHPMailer
            $response['status'] = 'success';
            $response['message'] = 'Enrollment successful! Please remember your student ID Your Student ID is: ' . $student_id;
            $response['student_id'] = $student_id;
            $_SESSION['activeGuardian'] = false;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $response['status'] = 'error';
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        // Display errors
        $error_message = implode("<br>", $errors);
        $response['status'] = 'error';
        $response['message'] = $error_message;
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
ob_end_flush();
exit;

