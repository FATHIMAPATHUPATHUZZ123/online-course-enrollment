<?php
$folder = "../uploads";

// Check if folder exists
if (!is_dir($folder)) {
    echo "Uploads folder does NOT exist.<br>";
} else {
    echo "Uploads folder exists.<br>";
}

// Check if folder is writable
if (is_writable($folder)) {
    echo "Uploads folder is writable ✅";
} else {
    echo "Uploads folder is NOT writable ❌";
}

// Optional: Try creating a test file
$testFile = $folder . "/test.txt";
if (file_put_contents($testFile, "test")) {
    echo "<br>Successfully created a test file.";
    unlink($testFile); // delete the test file
} else {
    echo "<br>Failed to create a test file.";
}
?>
