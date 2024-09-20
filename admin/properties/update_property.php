<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $property_type_id = $_POST['property_type_id'];
    $country_id = $_POST['country_id'];
    $state_id = $_POST['state_id'];
    $city_id = $_POST['city_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area = $_POST['area'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    // Ensure title is treated as a string
    $title = strval($title);

    $sql = "UPDATE properties SET 
            property_type_id = ?, 
            country_id = ?, 
            state_id = ?, 
            city_id = ?, 
            title = ?, 
            description = ?, 
            price = ?, 
            bedrooms = ?, 
            bathrooms = ?, 
            area = ?, 
            address = ?, 
            status = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiisssiidssi", $property_type_id, $country_id, $state_id, $city_id, $title, $description, $price, $bedrooms, $bathrooms, $area, $address, $status, $property_id);

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