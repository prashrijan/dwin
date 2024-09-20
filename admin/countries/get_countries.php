<?php
header('Content-Type: application/json');
include '../dbConnection/connect.php';

$sql = "SELECT id, name FROM countries";
$result = $conn->query($sql);

$countries = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }
}

echo json_encode($countries);