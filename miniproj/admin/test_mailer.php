<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "PHP is working!<br>";

// Test database
include '../database.php';
$result = $conn->query("SELECT * FROM students LIMIT 1");
if($result){
    $row = $result->fetch_assoc();
    echo "Student: " . $row['name'] . " - " . $row['email'] . "<br>";
}else{
    echo "DB query failed!<br>";
}

// Test PHPMailer include
require '../PHPMailer/class.phpmailer.php';
require '../PHPMailer/class.smtp.php';

echo "PHPMailer included successfully!";
?>
