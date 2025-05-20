<?php


require_once '../config/database.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: announcement.php?message=Invalid announcement ID&type=error");
    exit;
}

$announcementId = (int)$_GET['id'];

try {
    // Get announcement information to delete the image file
    $query = "SELECT picture FROM announcement WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $announcementId, PDO::PARAM_INT);
    $stmt->execute();
    $announcement = $stmt->fetch();
    
    if (!$announcement) {
        throw new Exception("Announcement not found");
    }
    
    // Delete the image file if it exists
    if (!empty($announcement['picture'])) {
        $picturePath = 'uploads/announcements/' . $announcement['picture'];
        if (file_exists($picturePath)) {
            unlink($picturePath);
        }
    }
    
    // Delete the announcement from the database
    $query = "DELETE FROM announcement WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $announcementId, PDO::PARAM_INT);
    $stmt->execute();
    
    header("Location: announcement.php?message=Announcement deleted successfully&type=success");
    exit;
    
} catch (Exception $e) {
    header("Location: announcement.php?message=Error: " . urlencode($e->getMessage()) . "&type=error");
    exit;
}