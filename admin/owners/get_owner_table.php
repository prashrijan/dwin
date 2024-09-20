<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo "Unauthorized access";
    exit();
}

$sql = "SELECT id, firstName, lastName, email, phone FROM owners ORDER BY lastName, firstName";
$result = $conn->query($sql);

$output = "<h2>Owner Management</h2>";
$output .= "<table class='owner-table property-table'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

while ($row = $result->fetch_assoc()) {
    $output .= "<tr data-owner-id='{$row['id']}'>
                    <td>{$row['lastName']}, {$row['firstName']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>
                        <a href='#' class='action-icon edit-owner' data-id='{$row['id']}'><i class='ri-pencil-line'></i></a>
                        <a href='#' class='action-icon delete-owner' data-id='{$row['id']}'><i class='ri-delete-bin-line'></i></a>
                    </td>
                </tr>";
}

$output .= "</tbody></table>";
$output .= "<a href='#' class='add-owner-btn'><i class='ri-add-line'></i> Add Owner</a>";

echo $output;

$conn->close();