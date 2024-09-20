<?php
include '../dbConnection/connect.php';

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_type_id = $_POST['property_type_id'];
    $country_id = $_POST['country_id'];
    $state_id = $_POST['state_id'];
    $city_id = $_POST['city_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $bedrooms = $_POST['bedrooms'] ?? null;
    $bathrooms = $_POST['bathrooms'] ?? null;
    $area = $_POST['area'] ?? null;
    $address = $_POST['address'] ?? null;
    $status = $_POST['status'];

    $sql = "INSERT INTO properties (property_type_id, country_id, state_id, city_id, title, description, price, bedrooms, bathrooms, area, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiissdiidss", $property_type_id, $country_id, $state_id, $city_id, $title, $description, $price, $bedrooms, $bathrooms, $area, $address, $status);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = $stmt->error;
    }

    $stmt->close();
}

echo json_encode($response);