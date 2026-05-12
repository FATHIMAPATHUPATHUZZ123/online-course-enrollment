<?php
session_start();
include '../database.php';

if (!isset($_SESSION['student_id'])) exit;

$student_id = $_SESSION['student_id'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "SELECT * FROM courses 
          WHERE title LIKE '%$search%' 
             OR description LIKE '%$search%' 
             OR duration LIKE '%$search%' 
             OR content LIKE '%$search%'";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    while($course = mysqli_fetch_assoc($result)){
        $stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_id=? AND course_id=?");
        $stmt->bind_param("ii", $student_id, $course['id']);
        $stmt->execute();
        $isEnrolled = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        $course_image = !empty($course['image']) ? 'images/courses/'.$course['image'] : 'https://via.placeholder.com/400x180?text=Course';

        echo '<div class="course-card">';
        echo '<img src="'.$course_image.'" alt="Course">';
        echo '<div class="course-card-body">';
        echo '<h5>'.htmlspecialchars($course['title']).'</h5>';
        echo '<p>'.htmlspecialchars($course['description']).'</p>';
        echo '<p><strong>Duration:</strong> '.$course['duration'].'</p>';
        echo '<p><strong>Content:</strong> '.$course['content'].'</p>';
        echo '<a href="course_details.php?course_id='.$course['id'].'" class="btn btn-primary btn-sm">View Details</a>';
        if(!$isEnrolled){
            echo '<a href="enroll_course.php?course_id='.$course['id'].'" class="btn btn-success btn-sm">Enroll</a>';
        }
        echo '</div></div>';
    }
} else {
    echo '<p style="grid-column:1/-1; text-align:center; color:#333;">No courses found.</p>';
}
?>
