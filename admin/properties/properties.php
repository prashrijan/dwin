<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../signIn_Login/admin_login.php");
    exit();
}

// Fetch and display properties

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Properties</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>View Properties</h1>
    <!-- Display list of properties -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>