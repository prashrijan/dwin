<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../signIn_Login/admin_login.php");
    exit();
}

// Add logic for searching properties by ID, name, or mobile number

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Property</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Search Property</h1>
    <!-- Add search form -->
    <!-- Display search results -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>