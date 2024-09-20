<?php
session_start();
require_once '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./signIn_login/admin_login.php");
    exit();
}

// Add logic for changing admin password

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <h1>Change Password</h1>
    <!-- Add form for changing password -->
    <a href="./dashboard/admin_dashboard.php">Back to Dashboard</a>
</body>
</html>