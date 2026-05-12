<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

// Fixed admin credentials
$admin_email = "admin1234@gmail.com";
$admin_password = "1234"; // For production, use password_hash()

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($email === $admin_email && $password === $admin_password) {
        // Correct admin credentials
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;

        // Fix: force session to save before redirect
        session_write_close();

        header("Location: dashboard.php");
        exit;
    } else {
        // Any other person trying to login
        $error = "Only the admin can login!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
<style>
    body {
        background: linear-gradient(to right, #a8d8ff, #d6ecff);
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
        background: white;
        padding: 35px;
        width: 360px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
    }
    h2 { margin-bottom: 20px; color: #003366; }
    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #b3d9ff;
        font-size: 14px;
    }
    button {
        width: 100%;
        padding: 12px;
        border: none;
        background: #4da3ff;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 10px;
        font-size: 16px;
    }
    button:hover { background: #3c8fe6; }
    .error { color: red; margin-bottom: 10px; font-size: 14px; font-weight: bold; }
    a { text-decoration: none; color: #4da3ff; }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="login-box">
    <h2>Admin Login</h2>

    <!-- Display error if login fails -->
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="username" placeholder="Enter Email" required autocomplete="off">
        <input type="password" name="password" placeholder="Enter Password" required autocomplete="off">
        <button type="submit">Login</button>
    </form>

    <p style="margin-top:15px;">
        <a href="../index.html">Back to Home</a>
    </p>
</div>
</body>
</html>
