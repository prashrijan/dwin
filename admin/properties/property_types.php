<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../signIn_Login/admin_login.php");
    exit();
}

// Add property type logic here
// Update property type logic here
// Fetch and display property types

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Property Types</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Manage Property Types</h1>
    <!-- Add form for adding new property type -->
    <!-- Display list of property types with update options -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>