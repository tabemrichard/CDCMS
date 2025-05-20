<?php
header("Content-Type: application/json");
require_once "../../config/database.php"; // Adjust path if needed

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $stmt = $pdo->prepare("UPDATE notification SET is_read = TRUE WHERE is_read = FALSE");
        $stmt->execute();
        echo json_encode(["message" => "Notifications marked as read"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
