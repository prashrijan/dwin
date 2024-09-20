<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Add logic for managing About Us and Contact Us pages

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Manage Pages</h1>
    <!-- Add forms for editing About Us and Contact Us pages -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>