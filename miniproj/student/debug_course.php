<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../database.php';

echo "<h2>Debug Course Details</h2>";

if (!isset($_GET['id'])) {
    die("<p style='color:red;'>No course ID provided in URL.</p>");
}

$id = intval($_GET['id']);
echo "<p>Course ID: $id</p>";

if (!$conn) {
    die("<p style='color:red;'>Database connection failed.</p>");
}

$query = "SELECT * FROM courses WHERE id = $id";
echo "<p>Query: $query</p>";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("<p style='color:red;'>Query failed: " . mysqli_error($conn) . "</p>");
}

if (mysqli_num_rows($result) == 0) {
    echo "<p style='color:red;'>No course found with ID = $id</p>";
} else {
    $course = mysqli_fetch_assoc($result);
    echo "<h3>Course Found:</h3>";
    echo "<pre>";
    print_r($course);
    echo "</pre>";
}
?>
