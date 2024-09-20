<?php
include '../dbConnection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $state_id = $_POST['state_id'];
    $city_name = $_POST['city_name'];
    
    $sql = "INSERT INTO cities (state_id, name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $state_id, $city_name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}