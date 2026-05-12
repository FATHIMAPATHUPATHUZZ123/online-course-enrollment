<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include '../database.php';

// Delete all inactive instructors
$conn->query("DELETE FROM instructors WHERE status='inactive'");

header("Location: view_instructors.php");
exit;
?>
