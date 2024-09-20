<?php
include '../dbConnection/connect.php';

$sql = "SELECT id, name FROM countries ORDER BY name";
$result = $conn->query($sql);

$output = "<table class='country-table property-table'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

while ($row = $result->fetch_assoc()) {
    $output .= "<tr data-country-id='{$row['id']}'>
                    <td>{$row['name']}</td>
                    <td>
                        <a href='#' class='action-icon edit-country' data-id='{$row['id']}'><i class='ri-pencil-line'></i></a>
                        <a href='#' class='action-icon delete-country' data-id='{$row['id']}'><i class='ri-delete-bin-line'></i></a>
                    </td>
                </tr>";
}

$output .= "</tbody></table>";

echo $output;