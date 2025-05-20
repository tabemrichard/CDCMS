<?php
header("Content-Type: application/json");
require_once "../../config/database.php"; // Adjust path if needed

try {
    // Fetch latest 12 notifications
    $stmt = $pdo->prepare("SELECT id, name, action, content, file_path, is_read, date_created FROM notification WHERE action IN ('update', 'add') ORDER BY date_created DESC LIMIT 12");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count unread notifications
    $stmt = $pdo->prepare("SELECT COUNT(*) AS unread_count FROM notification WHERE is_read = FALSE");
    $stmt->execute();
    $unread_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

    echo json_encode([
        "unread_count" => $unread_count,
        "notifications" => $notifications
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
