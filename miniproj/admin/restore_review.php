<?php
// admin/restore_review.php
session_start();
include '../database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['backup_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$backup_id = intval($_POST['backup_id']);
$admin_id = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : null;

$conn->begin_transaction();
try {
    // Fetch backup row
    $stmt = $conn->prepare("SELECT * FROM deleted_reviews WHERE backup_id = ?");
    $stmt->bind_param("i", $backup_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows === 0) {
        $stmt->close();
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Backup not found.']);
        exit;
    }
    $row = $res->fetch_assoc();
    $stmt->close();

    // Insert back into reviews (preserve orig id if possible? We'll insert normally)
    $stmt2 = $conn->prepare("INSERT INTO reviews (student_id, course_id, review, reply, admin_reply, admin_id, instructor_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param(
        "iisssiis",
        $row['student_id'],
        $row['course_id'],
        $row['review'],
        $row['reply'],
        $row['admin_reply'],
        $row['admin_id'],
        $row['instructor_id'],
        $row['created_at']
    );
    $stmt2->execute();
    $new_id = $stmt2->insert_id;
    $stmt2->close();

    // Remove backup row
    $stmt3 = $conn->prepare("DELETE FROM deleted_reviews WHERE backup_id = ?");
    $stmt3->bind_param("i", $backup_id);
    $stmt3->execute();
    $stmt3->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Review restored.', 'restored_id' => $new_id]);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}
