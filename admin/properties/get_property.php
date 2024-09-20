<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit();
}

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    
    $sql = "SELECT * FROM properties WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($property = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'property' => $property]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Property not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'No property ID provided']);
}

$conn->close();
?>