<?php
include '../dbConnection/connect.php';

$country_id = isset($_GET['country_id']) ? $_GET['country_id'] : null;

if ($country_id) {
    $sql = "SELECT id, name FROM states WHERE country_id = ? ORDER BY name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $country_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, name FROM states ORDER BY name";
    $result = $conn->query($sql);
}

$states = [];
while ($row = $result->fetch_assoc()) {
    $states[] = $row;
}

echo json_encode($states);