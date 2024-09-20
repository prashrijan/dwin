<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Add state logic here
// Update state logic here
// Fetch and display states

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage States</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Manage States</h1>
    <!-- Add form for adding new state -->
    <!-- Display list of states with update options -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>