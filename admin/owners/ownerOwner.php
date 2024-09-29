<?php
session_start();
include '../dbConnection/connect.php';

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$owner_name = $_POST['owner_name'];
$owner_phone = $_POST['owner_phone'];
$owner_email = $_POST['owner_email'];
$owner_address = $_POST['owner_address'];
$property_id = $_POST['property_id'];

// Prepare SQL statement
$sql = "INSERT INTO your_owners_table (name, phone, email, address, property_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("sssss", $owner_name, $owner_phone, $owner_email, $owner_address, $property_id);

// Execute statement
if ($stmt->execute()) {
    echo "New owner added successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>