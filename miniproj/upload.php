<?php
// Folder to save uploaded videos
$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES['video'])) {
    $fileName = basename($_FILES['video']['name']);
    $targetFile = $targetDir . $fileName;

    // Check file size (optional)
    if ($_FILES['video']['size'] > 200 * 1024 * 1024) { // 200MB
        echo "File too large!";
        exit;
    }

    // Allowed types
    $allowedTypes = array("mp4", "webm", "ogg");
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes)) {
        echo "Only MP4, WebM, or Ogg videos allowed!";
        exit;
    }

    if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
        echo "Video uploaded successfully!<br>";
        echo "<a href='$targetFile'>Play Video</a>";
    } else {
        echo "Error uploading video!";
    }
} else {
    echo "No video selected!";
}
?>
