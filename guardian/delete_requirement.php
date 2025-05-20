<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guardian') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if student_id and type are provided
if (!isset($_GET['student_id']) || !isset($_GET['type'])) {
    header('Location: requirements.php');
    exit;
}

$student_id = (int)$_GET['student_id'];
$requirement_type = sanitize($_GET['type']);

// Validate requirement type
$valid_types = ['immunization_card', 'recent_photo']; // Only allow deletion of non-required documents
if (!in_array($requirement_type, $valid_types)) {
    $_SESSION['alert'] = [
        'message' => 'This requirement cannot be deleted',
        'type' => 'danger'
    ];
    header('Location: requirements.php');
    exit;
}

try {
    // Get current file name
    $stmt = $pdo->prepare("SELECT $requirement_type FROM requirements WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $filename = $stmt->fetchColumn();
    
    if ($filename) {
        // Delete the file
        $file_path = '../uploads/' . $filename;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Update the database
        $stmt = $pdo->prepare("UPDATE requirements SET $requirement_type = NULL WHERE student_id = ?");
        $stmt->execute([$student_id]);
        
        $_SESSION['alert'] = [
            'message' => 'Requirement deleted successfully',
            'type' => 'success'
        ];
    } else {
        $_SESSION['alert'] = [
            'message' => 'Requirement not found',
            'type' => 'warning'
        ];
    }
} catch (PDOException $e) {
    $_SESSION['alert'] = [
        'message' => 'Failed to delete requirement: ' . $e->getMessage(),
        'type' => 'danger'
    ];
}

header('Location: requirements.php');
exit;

