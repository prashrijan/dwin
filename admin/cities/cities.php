<?php
session_start();
require_once '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../sigIn_Login/admin_login.php");
    exit();
}

// Add city logic here
// Update city logic here
// Fetch and display cities

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cities</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Manage Cities</h1>
    <!-- Add form for adding new city -->
    <!-- Display list of cities with update options -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>