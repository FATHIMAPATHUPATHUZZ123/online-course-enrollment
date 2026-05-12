<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
include '../database.php';
$conn->query("DELETE FROM courses WHERE status='inactive'");
header("Location: view_courses.php"); exit;
?>
