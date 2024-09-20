<?php
session_start();
require_once '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../signIn_Login/admin_login.php");
    exit();
}

// Add country logic here
// Update country logic here
// Fetch and display countries

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Countries</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Manage Countries</h1>
    <!-- Add form for adding new country -->
    <!-- Display list of countries with update options -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>