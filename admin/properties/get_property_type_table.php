<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo "Unauthorized access";
    exit();
}

$sql = "SELECT id, name FROM property_types ORDER BY name";
$result = $conn->query($sql);

$output = "<h2>Property Type Management</h2>";
$output .= "<table class='property-type-table property-table'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

while ($row = $result->fetch_assoc()) {
    $output .= "<tr data-property-type-id='{$row['id']}'>
                    <td>{$row['name']}</td>
                    <td>
                        <a href='#' class='action-icon edit-property-type' data-id='{$row['id']}'><i class='ri-pencil-line'></i></a>
                        <a href='#' class='action-icon delete-property-type' data-id='{$row['id']}'><i class='ri-delete-bin-line'></i></a>
                    </td>
                </tr>";
}

$output .= "</tbody></table>";
$output .= "<a href='#' class='add-property-type-btn'><i class='ri-add-line'></i> Add Property Type</a>";

echo $output;

$conn->close();
?>