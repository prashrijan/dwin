<?php
include '../dbConnection/connect.php';

$sql = "SELECT id, CONCAT(firstName, ' ', lastName) AS name, 'Admin' AS role FROM admins
        UNION ALL
        SELECT id, CONCAT(firstName, ' ', lastName) AS name, 'User' AS role FROM users
        ORDER BY name";

$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$output = "<h2>User Management</h2>";
$output .= "<table class='user-table property-table'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

while ($row = $result->fetch_assoc()) {
    $role_class = ($row['role'] === 'Admin') ? 'bg-blue-500 text-white' : 'bg-green-500 text-white';
    $output .= "<tr data-user-id='{$row['id']}' data-user-role='{$row['role']}'>
                    <td>{$row['name']}</td>
                    <td><span class='status {$role_class}'>{$row['role']}</span></td>
                    <td>
                        <a href='#' class='action-icon edit-user' data-id='{$row['id']}' data-role='{$row['role']}'><i class='ri-pencil-line'></i></a>
                        <a href='#' class='action-icon delete-user' data-id='{$row['id']}' data-role='{$row['role']}'><i class='ri-delete-bin-line'></i></a>
                    </td>
                </tr>";
}

$output .= "</tbody></table>";
$output .= "<a href='#' class='add-user-btn add-property-btn'><i class='ri-add-line'></i> Add User</a>";

echo $output;

$conn->close();
?>