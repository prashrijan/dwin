<?php
include '../dbConnection/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country_name = $_POST['country_name'];
    
    $sql = "INSERT INTO countries (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $country_name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}