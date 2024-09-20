<?php
session_start();
require_once '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch and display users

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>View Users</h1>

    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>