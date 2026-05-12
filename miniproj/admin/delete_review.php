<?php
// admin/delete_review.php (AJAX JSON response with backup and soft-delete behavior)
session_start();
include '../database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['review_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$review_id = intval($_POST['review_id']);
$admin_id = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : null;

// Start transaction to keep backup + delete atomic
$conn->begin_transaction();

try {
    // Create backup table if not exists (simple structure — adjust types if needed)
    $create_sql = "
    CREATE TABLE IF NOT EXISTS deleted_reviews (
        backup_id INT AUTO_INCREMENT PRIMARY KEY,
        orig_id INT,
        student_id INT,
        course_id INT,
        review TEXT,
        reply TEXT,
        admin_reply TEXT,
        admin_id INT,
        instructor_id INT,
        created_at DATETIME,
        deleted_at DATETIME,
        deleted_by INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $conn->query($create_sql);

    // Select the review row to backup
    $stmt = $conn->prepare("SELECT id, student_id, course_id, review, reply, admin_reply, admin_id, instructor_id, created_at FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res || $res->num_rows === 0) {
        $stmt->close();
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Review not found.']);
        exit;
    }
    $row = $res->fetch_assoc();
    $stmt->close();

    // Insert into deleted_reviews
    $stmt2 = $conn->prepare("INSERT INTO deleted_reviews (orig_id, student_id, course_id, review, reply, admin_reply, admin_id, instructor_id, created_at, deleted_at, deleted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    $stmt2->bind_param(
        "iiisssiiis",
        $row['id'],
        $row['student_id'],
        $row['course_id'],
        $row['review'],
        $row['reply'],
        $row['admin_reply'],
        $row['admin_id'],
        $row['instructor_id'],
        $row['created_at'],
        $admin_id
    );
    $stmt2->execute();
    $backup_id = $stmt2->insert_id;
    $stmt2->close();

    // Delete original review
    $stmt3 = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt3->bind_param("i", $review_id);
    $stmt3->execute();
    $stmt3->close();

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Review moved to trash.', 'backup_id' => $backup_id]);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}
