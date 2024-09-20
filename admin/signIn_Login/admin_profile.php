<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Add logic for updating admin profile

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Admin Profile</h1>
    <!-- Add form for updating admin profile -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>