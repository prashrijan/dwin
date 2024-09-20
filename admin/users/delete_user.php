<?php
include '../dbConnection/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['type'])) {
    $userId = $_POST['user_id'];
    $type = $_POST['type'];

    if ($type === 'Admin' || $type === 'admin') {
        $sql = "DELETE FROM admins WHERE id = ?";
    } else if ($type === 'User' || $type === 'user') {
        $sql = "DELETE FROM users WHERE id = ?";
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid user type']);
        exit;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

$conn->close();
?>