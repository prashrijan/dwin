<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ownerId = $_POST['owner_id'];

    if (empty($ownerId)) {
        echo json_encode(['success' => false, 'error' => 'Owner ID is required']);
        exit();
    }

    $sql = "DELETE FROM owners WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ownerId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error deleting owner: ' . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();