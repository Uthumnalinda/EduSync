<?php
// API endpoint to mark notifications as read

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';

try {
    $dbObj = new Database();
    $db = $dbObj->getConnection();
    if ($db !== null) {
        // Mark all unread notifications as read
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'All notifications marked as read',
            'affected' => $stmt->rowCount()
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Database connection failed']);
