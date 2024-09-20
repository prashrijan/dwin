<?php
include '../dbConnection/connect.php';

$sql = "SELECT ci.id, ci.name, s.name AS state_name, c.name AS country_name 
        FROM cities ci 
        JOIN states s ON ci.state_id = s.id 
        JOIN countries c ON s.country_id = c.id 
        ORDER BY c.name, s.name, ci.name";
$result = $conn->query($sql);

$output = "<table class='city-table property-table'>
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

while ($row = $result->fetch_assoc()) {
    $output .= "<tr data-city-id='{$row['id']}'>
                    <td>{$row['country_name']}</td>
                    <td>{$row['state_name']}</td>
                    <td>{$row['name']}</td>
                    <td>
                        <a href='#' class='action-icon edit-city' data-id='{$row['id']}'><i class='ri-pencil-line'></i></a>
                        <a href='#' class='action-icon delete-city' data-id='{$row['id']}'><i class='ri-delete-bin-line'></i></a>
                    </td>
                </tr>";
}

$output .= "</tbody></table>";

echo $output;