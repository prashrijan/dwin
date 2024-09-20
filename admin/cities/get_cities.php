<?php
include '../dbConnection/connect.php';

$state_id = isset($_GET['state_id']) ? $_GET['state_id'] : null;

if ($state_id) {
    $sql = "SELECT id, name FROM cities WHERE state_id = ? ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $state_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, name FROM cities ORDER BY name";
    $result = $conn->query($sql);
}

$cities = [];
while ($row = $result->fetch_assoc()) {
    $cities[] = $row;
}

echo json_encode($cities);