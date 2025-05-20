<?php

include_once '../../config/database.php';

// Check if required parameters are provided
if (!isset($_POST['student_id']) || !isset($_POST['date']) || !isset($_POST['status'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Get parameters
$studentId = (int)$_POST['student_id'];
$date = $_POST['date'];
$status = $_POST['status'];

// Validate status
if ($status !== 'Present' && $status !== 'Absent') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status value'
    ]);
    exit;
}

try {
    // Check if attendance record already exists for this student and date
    $query = "SELECT id FROM attendance WHERE student_id = :student_id AND DATE(date) = :date";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    $existingRecord = $stmt->fetch();
    
    if ($existingRecord) {
        // Update existing record
        $query = "UPDATE attendance SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $existingRecord['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Attendance status updated successfully',
            'updated' => true
        ]);
    } else {
        // Insert new record
        $query = "INSERT INTO attendance (student_id, date, status) VALUES (:student_id, :date, :status)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Attendance status recorded successfully',
            'updated' => false
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
