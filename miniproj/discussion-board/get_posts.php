<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "miniprojects_db";

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all posts
$sql = "SELECT id, user, text, created_at FROM discussion_board ORDER BY created_at DESC";
$result = $conn->query($sql);

$posts = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($posts);

$conn->close();
?>
