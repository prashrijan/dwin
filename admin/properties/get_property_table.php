<?php
include '../dbConnection/connect.php';

function generatePropertyTable($conn) {
    $sql = "SELECT p.id, p.title, c.name AS country, s.name AS state, ci.name AS city, pt.name AS property_type, p.status, p.price
            FROM properties p
            JOIN countries c ON p.country_id = c.id
            JOIN states s ON p.state_id = s.id
            JOIN cities ci ON p.city_id = ci.id
            JOIN property_types pt ON p.property_type_id = pt.id
            ORDER BY p.id DESC";
    $result = $conn->query($sql);
    
    $output = "<h2>Properties</h2>";
    $output .= "<table class='property-table'>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        $location = $row['city'] . ', ' . $row['state'] . ', ' . $row['country'];
        $status_class = $row['status'] == 'active' ? 'status-active' : ($row['status'] == 'sold' ? 'status-sold' : '');
        $output .= "<tr>
                        <td>{$row['title']}</td>
                        <td>{$location}</td>
                        <td>{$row['property_type']}</td>
                        <td>$" . number_format($row['price'], 2) . "</td>
                        <td><span class='status {$status_class}'>{$row['status']}</span></td>
                        <td>
                            <a href='#' class='action-icon edit-icon' data-id='{$row['id']}'><i class='ri-pencil-line'></i></a>
                            <a href='#' class='action-icon delete-icon' data-id='{$row['id']}'><i class='ri-delete-bin-line'></i></a>
                        </td>
                    </tr>";
    }
    
    $output .= "</tbody></table>";
    $output .= "<a href='#' class='add-property-btn'><i class='ri-add-line'></i> Add Property</a>";
    
    return $output;
}

echo generatePropertyTable($conn);