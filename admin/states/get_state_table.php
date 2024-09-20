<?php
include '../dbConnection/connect.php';

$sql = "SELECT s.id, s.name, c.name AS country_name 
        FROM states s 
        JOIN countries c ON s.country_id = c.id 
        ORDER BY c.name, s.name";
$result = $conn->query($sql);

$output = "<table class='state-table property-table'>
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>State</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

while ($row = $result->fetch_assoc()) {
    $output .= "<tr data-state-id='{$row['id']}'>
                    <td>{$row['country_name']}</td>
                    <td>{$row['name']}</td>
                    <td>
                        <a href='#' class='action-icon edit-state' data-id='{$row['id']}'><i class='ri-pencil-line'></i></a>
                        <a href='#' class='action-icon delete-state' data-id='{$row['id']}'><i class='ri-delete-bin-line'></i></a>
                    </td>
                </tr>";
}

$output .= "</tbody></table>";

echo $output;