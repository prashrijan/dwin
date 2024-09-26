<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
<style>
    body {
        background-color: #f9f9f9;
    }
    .container {
        max-width: 400px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .container h2 {
        color: #a81e35;
    }
    .container form {
        margin-top: 20px;
    }
    .container form input[type="email"], .container form input[type="password"] {
        width: 100%;
        height: 40px;
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    .container form button[type="submit"] {
        width: 100%;
        height: 40px;
        background-color: #a81e35;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .container form button[type="submit"]:hover {
        background-color: #8c1c2f;
    }
    .container p {
        margin-top: 20px;
    }
    .container p a {
        color: #a81e35;
    }
</style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="login_process.php" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>