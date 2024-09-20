<?php
include '../dbConnection/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $propertyTypeName = $_POST['property_type_name'];

    $sql = "INSERT INTO property_types (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $propertyTypeName);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();
?>