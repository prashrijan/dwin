<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch and display reviews
// Add logic for approving, disapproving, and deleting reviews

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Manage Reviews</h1>
    <!-- Display list of reviews with options to approve, disapprove, or delete -->
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>