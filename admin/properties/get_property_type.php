<?php
include '../dbConnection/connect.php';

if (isset($_GET['id'])) {
    $propertyTypeId = $_GET['id'];

    $sql = "SELECT id, name FROM property_types WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $propertyTypeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'propertyType' => $row]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Property type not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>