<?php
header('Content-Type: application/json');
include '../dbConnection/connect.php';

$sql = "SELECT id, name FROM property_types";
$result = $conn->query($sql);

$property_types = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $property_types[] = $row;
    }
}

echo json_encode($property_types);