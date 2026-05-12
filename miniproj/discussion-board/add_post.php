<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "miniprojects_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$user = $conn->real_escape_string($data['user']);
$text = $conn->real_escape_string($data['text']);

$sql = "INSERT INTO discussion_board (user, text) VALUES ('$user', '$text')";
if ($conn->query($sql) === TRUE) {
    $id = $conn->insert_id;
    $newPost = [
        "id" => $id,
        "user" => $user,
        "text" => $text,
        "created_at" => date("Y-m-d H:i:s")
    ];
    header('Content-Type: application/json');
    echo json_encode($newPost);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to insert post"]);
}

$conn->close();
?>
