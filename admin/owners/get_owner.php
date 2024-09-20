<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

if (isset($_GET['id'])) {
    $ownerId = $_GET['id'];

    $sql = "SELECT id, firstName, lastName, email, phone FROM owners WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ownerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($owner = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'owner' => $owner]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Owner not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Owner ID is required']);
}

$conn->close();