<?php
session_start();
include '../dbConnection/connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../signIn_Login/admin_login.php");
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT firstName, lastName, profileImage FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    // Handle the case where no admin is found
    session_destroy();
    header("Location: ../signIn_Login/admin_login.php");
    exit();
}

// Function to load content based on action
function loadContent($action, $conn) {
    switch ($action) {
        case 'dashboard':
            return generatePropertyTable($conn);
        case 'property_type':
            return generatePropertyTypeContent($conn);
        case 'country':
            return generateCountryContent($conn);
        case 'state':
            return generateStateContent($conn);
        case 'city':
            return generateCityContent($conn);
        case 'owner':
            return generateOwnerTable($conn);
        case 'agents':
            return "<h2>Agent Details</h2><p>View agent details here.</p>";
        case 'user':
            return generateUserTable($conn);
        case 'properties':
            return "<h2>Listed Properties</h2><p>View property details here.</p>";
        case 'reviews':
            return "<h2>Reviews Management</h2><p>Approve, disapprove, or delete reviews here.</p>";
        case 'pages':
            return generatePageManagementContent($conn);
        case 'search':
            return "<h2>Search Property</h2><p>Search for properties by ID, name, or mobile number.</p>";
        case 'profile':
            return "<h2>Update Profile</h2><p>Update your profile information here.</p>";
        case 'change_password':
            return "<h2>Change Password</h2><p>Change your password here.</p>";
        default:
            return generatePropertyTable($conn);
    }
}

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
        $status_class = getStatusColor($row['status']);
        $output .= "<tr data-property-id='{$row['id']}'>
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

function generatePropertyTypeContent($conn) {
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
    
    return $output;
}

function generateCountryContent($conn) {
    $output = "<h2>Country Management</h2>";
    
    // Add country form
    $output .= "
    <form id='addCountryForm' class='mb-4'>
        <input type='text' name='country_name' placeholder='Enter country name' required>
        <button type='submit'>Add Country</button>
    </form>";
    
    // Fetch existing countries
    $sql = "SELECT id, name FROM countries ORDER BY name";
    $result = $conn->query($sql);
    
    $output .= "<table class='country-table property-table'>
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
    
    // Add JavaScript for form submission and table updates
    $output .= "
    <script>
    document.getElementById('addCountryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('../countries/add_country.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Country added successfully!');
                refreshCountryTable();
                this.reset();
            } else {
                alert('Error adding country: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    function refreshCountryTable() {
        fetch('../countries/get_country_table.php')
            .then(response => response.text())
            .then(html => {
                document.querySelector('.country-table').outerHTML = html;
            })
            .catch(error => {
                console.error('Error refreshing country table:', error);
            });
    }

    // Add event listeners for edit and delete actions
    document.querySelector('.country-table').addEventListener('click', function(e) {
        if (e.target.closest('.edit-country')) {
            e.preventDefault();
            const countryId = e.target.closest('.edit-country').dataset.id;
            const countryName = e.target.closest('tr').querySelector('td').textContent;
            const newName = prompt('Enter new name for ' + countryName, countryName);
            if (newName && newName !== countryName) {
                updateCountry(countryId, newName);
            }
        } else if (e.target.closest('.delete-country')) {
            e.preventDefault();
            const countryId = e.target.closest('.delete-country').dataset.id;
            if (confirm('Are you sure you want to delete this country?')) {
                deleteCountry(countryId);
            }
        }
    });

    function updateCountry(id, newName) {
        fetch('../countries/update_country.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id + '&name=' + encodeURIComponent(newName)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Country updated successfully!');
                refreshCountryTable();
            } else {
                alert('Error updating country: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function deleteCountry(id) {
        fetch('../countries/delete_country.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Country deleted successfully!');
                refreshCountryTable();
            } else {
                alert('Error deleting country: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    </script>";
    
    return $output;
}

function generateStateContent($conn) {
    $output = "<h2>State Management</h2>";
    
    // Add state form
    $output .= "
    <form id='addStateForm' class='mb-4'>
        <select name='country_id' required>
            <option value=''>Select Country</option>
            " . getCountryOptions($conn) . "
        </select>
        <input type='text' name='state_name' placeholder='Enter state name' required>
        <button type='submit'>Add State</button>
    </form>";
    
    // Fetch existing states
    $sql = "SELECT s.id, s.name, c.name AS country_name 
            FROM states s 
            JOIN countries c ON s.country_id = c.id 
            ORDER BY c.name, s.name";
    $result = $conn->query($sql);
    
    $output .= "<table class='state-table property-table'>
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
    
    // Add JavaScript for form submission and table updates
    $output .= "
    <script>
    document.getElementById('addStateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('../states/add_state.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('State added successfully!');
                refreshStateTable();
                this.reset();
            } else {
                alert('Error adding state: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    function refreshStateTable() {
        fetch('../states/get_state_table.php')
            .then(response => response.text())
            .then(html => {
                document.querySelector('.state-table').outerHTML = html;
            })
            .catch(error => {
                console.error('Error refreshing state table:', error);
            });
    }

    // Add event listeners for edit and delete actions
    document.querySelector('.state-table').addEventListener('click', function(e) {
        if (e.target.closest('.edit-state')) {
            e.preventDefault();
            const stateId = e.target.closest('.edit-state').dataset.id;
            const stateName = e.target.closest('tr').querySelectorAll('td')[1].textContent;
            const newName = prompt('Enter new name for ' + stateName, stateName);
            if (newName && newName !== stateName) {
                updateState(stateId, newName);
            }
        } else if (e.target.closest('.delete-state')) {
            e.preventDefault();
            const stateId = e.target.closest('.delete-state').dataset.id;
            if (confirm('Are you sure you want to delete this state?')) {
                deleteState(stateId);
            }
        }
    });

    function updateState(id, newName) {
        fetch('../states/update_state.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id + '&name=' + encodeURIComponent(newName)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('State updated successfully!');
                refreshStateTable();
            } else {
                alert('Error updating state: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function deleteState(id) {
        fetch('../states/delete_state.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('State deleted successfully!');
                refreshStateTable();
            } else {
                alert('Error deleting state: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    </script>";
    
    return $output;
}

// Helper function to get country options
function getCountryOptions($conn) {
    $sql = "SELECT id, name FROM countries ORDER BY name";
    $result = $conn->query($sql);
    $options = "";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row['id']}'>{$row['name']}</option>";
    }
    return $options;
}

function generateCityContent($conn) {
    $output = "<h2>City Management</h2>";
    
    // Add city form
    $output .= "
    <form id='addCityForm' class='mb-4'>
        <select name='country_id' id='cityCountrySelect' required>
            <option value=''>Select Country</option>
            " . getCountryOptions($conn) . "
        </select>
        <select name='state_id' id='cityStateSelect' required>
            <option value=''>Select State</option>
        </select>
        <input type='text' name='city_name' placeholder='Enter city name' required>
        <button type='submit'>Add City</button>
    </form>";
    
    // Fetch existing cities
    $sql = "SELECT ci.id, ci.name, s.name AS state_name, c.name AS country_name 
            FROM cities ci 
            JOIN states s ON ci.state_id = s.id 
            JOIN countries c ON s.country_id = c.id 
            ORDER BY c.name, s.name, ci.name";
    $result = $conn->query($sql);
    
    $output .= "<table class='city-table property-table'>
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
    
    // Add JavaScript for form submission and table updates
    $output .= <<<EOT
    <script>
    function initializeCityManagement() {
        const countrySelect = document.getElementById('cityCountrySelect');
        const stateSelect = document.getElementById('cityStateSelect');

        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            stateSelect.innerHTML = '<option value="">Select State</option>';
            if (countryId) {
                fetch('../states/get_states.php?country_id=' + countryId)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            stateSelect.appendChild(option);
                        });
                    });
            }
        });

        document.getElementById('addCityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../cities/add_city.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('City added successfully!');
                    refreshCityTable();
                    this.reset();
                } else {
                    alert('Error adding city: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // Add event listeners for edit and delete actions
        document.querySelector('.city-table').addEventListener('click', function(e) {
            if (e.target.closest('.edit-city')) {
                e.preventDefault();
                const cityId = e.target.closest('.edit-city').dataset.id;
                const cityName = e.target.closest('tr').querySelectorAll('td')[2].textContent;
                const newName = prompt('Enter new name for ' + cityName, cityName);
                if (newName && newName !== cityName) {
                    updateCity(cityId, newName);
                }
            } else if (e.target.closest('.delete-city')) {
                e.preventDefault();
                const cityId = e.target.closest('.delete-city').dataset.id;
                if (confirm('Are you sure you want to delete this city?')) {
                    deleteCity(cityId);
                }
            }
        });
    }

    function refreshCityTable() {
        fetch('../cities/get_city_table.php')
            .then(response => response.text())
            .then(html => {
                document.querySelector('.city-table').outerHTML = html;
                initializeCityManagement();
            })
            .catch(error => {
                console.error('Error refreshing city table:', error);
            });
    }

    function updateCity(id, newName) {
        fetch('../cities/update_city.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id + '&name=' + encodeURIComponent(newName)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('City updated successfully!');
                refreshCityTable();
            } else {
                alert('Error updating city: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function deleteCity(id) {
        fetch('../cities/delete_city.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('City deleted successfully!');
                refreshCityTable();
            } else {
                alert('Error deleting city: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Initialize the city management functionality
    initializeCityManagement();
    </script>
EOT;
    
    return $output;
}

function generateOwnerTable($conn) {
    error_log("Generating owner table"); // Add this line for debugging
    $sql = "SELECT id, firstName, lastName, email, phone FROM owners ORDER BY lastName, firstName";
    $result = $conn->query($sql);
    
    if (!$result) {
        error_log("SQL Error: " . $conn->error); // Add this line for debugging
        return "Error fetching owners: " . $conn->error;
    }
    
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
    
    return $output;
}

function generateUserTable($conn) {
    $sql = "SELECT id, CONCAT(firstName, ' ', lastName) AS name, 'Admin' AS role FROM admins
            UNION ALL
            SELECT id, CONCAT(firstName, ' ', lastName) AS name, 'User' AS role FROM users
            ORDER BY name";
    $result = $conn->query($sql);
    
    $output = "<h2>User Management</h2>";
    $output .= "<table class='user-table property-table'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";
    
    while ($row = $result->fetch_assoc()) {
        $type_class = ($row['role'] === 'Admin') ? 'bg-blue-500 text-white' : 'bg-green-500 text-white';
        $output .= "<tr data-user-id='{$row['id']}' data-user-type='{$row['role']}'>
                        <td>{$row['name']}</td>
                        <td><span class='status {$type_class}'>{$row['role']}</span></td>
                        <td>
                            <a href='#' class='action-icon edit-user' data-id='{$row['id']}' data-type='{$row['role']}'><i class='ri-pencil-line'></i></a>
                            <a href='#' class='action-icon delete-user' data-id='{$row['id']}' data-type='{$row['role']}'><i class='ri-delete-bin-line'></i></a>
                        </td>
                    </tr>";
    }
    
    $output .= "</tbody></table>";
    $output .= "<a href='#' class='add-user-btn add-property-btn'><i class='ri-add-line'></i> Add User</a>";
    
    return $output;
}

function generatePageManagementContent($conn) {
    $output = "<h2>Page Management</h2>";

    // About Us section
    $aboutUsContent = getAboutUsContent($conn);
    $output .= "<h3>About Us</h3>";
    $output .= "<form id='aboutUsForm'>";
    $output .= "<textarea name='about_us_content' rows='10' cols='80'>" . htmlspecialchars($aboutUsContent) . "</textarea><br>";
    $output .= "<button type='submit'>Update About Us</button>";
    $output .= "</form>";

    // Add JavaScript to handle form submission
    $output .= "
    <script>
    document.getElementById('aboutUsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('../pages/update_about_us.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('About Us content updated successfully!');
            } else {
                alert('Error updating About Us content: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the About Us content.');
        });
    });
    </script>";

    return $output;
}

function getAboutUsContent($conn) {
    $sql = "SELECT content FROM pages WHERE slug = 'about-us' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['content'];
    } else {
        // If no content found, insert a default entry
        $defaultContent = "Welcome to our About Us page. Content coming soon!";
        $defaultTitle = "About Us";
        $defaultSlug = "about-us";
        $currentDate = date('Y-m-d H:i:s');
        
        $insertSql = "INSERT INTO pages (title, content, slug, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ssss", $defaultTitle, $defaultContent, $defaultSlug, $currentDate);
        $stmt->execute();
        $stmt->close();
        return $defaultContent;
    }
}

function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'active':
            return 'bg-green-500 text-white';
        case 'sold':
            return 'bg-red-500 text-white';
        case 'rented':
            return 'bg-yellow-500 text-white';
        default:
            return 'bg-gray-500 text-white';
    }
}

$content = loadContent($_GET['action'] ?? 'dashboard', $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Core Real Estate</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

    <div id="addPropertyModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Property</h2>
            <form id="addPropertyForm">
                <select name="property_type_id" required>
                    <option value="">Select Property Type</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <select name="country_id" required>
                    <option value="">Select Country</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <select name="state_id" required>
                    <option value="">Select State</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <select name="city_id" required>
                    <option value="">Select City</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <input type="text" name="title" placeholder="Property Title" required>
                <textarea name="description" placeholder="Property Description" required></textarea>
                <input type="number" name="price" placeholder="Price" required>
                <input type="number" name="bedrooms" placeholder="Number of Bedrooms">
                <input type="number" name="bathrooms" placeholder="Number of Bathrooms">
                <input type="number" name="area" placeholder="Area (sq ft/m²)" step="0.01">
                <input type="text" name="address" placeholder="Address">
                <select name="status" required>
                    <option value="active">Active</option>
                    <option value="sold">Sold</option>
                    <option value="rented">Rented</option>
                </select>
                <button type="submit">Add Property</button>
            </form>
        </div>
    </div>


    <div id="editPropertyModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Property</h2>
            <form id="editPropertyForm">
                <input type="hidden" name="property_id" id="editPropertyId">
                <select name="property_type_id" id="editPropertyTypeId" required>
                    <option value="">Select Property Type</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <select name="country_id" id="editCountryId" required>
                    <option value="">Select Country</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <select name="state_id" id="editStateId" required>
                    <option value="">Select State</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <select name="city_id" id="editCityId" required>
                    <option value="">Select City</option>
                    <!-- We'll populate this dynamically -->
                </select>
                <input type="text" name="title" id="editTitle" placeholder="Property Title" required>
                <textarea name="description" id="editDescription" placeholder="Property Description" required></textarea>
                <input type="number" name="price" id="editPrice" placeholder="Price" required>
                <input type="number" name="bedrooms" id="editBedrooms" placeholder="Number of Bedrooms">
                <input type="number" name="bathrooms" id="editBathrooms" placeholder="Number of Bathrooms">
                <input type="number" name="area" id="editArea" placeholder="Area (sq ft/m²)" step="0.01">
                <input type="text" name="address" id="editAddress" placeholder="Address">
                <select name="status" id="editStatus" required>
                    <option value="active">Active</option>
                    <option value="sold">Sold</option>
                    <option value="rented">Rented</option>
                </select>
                <button type="submit">Update Property</button>
            </form>
        </div>
    </div>

    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this property?</p>
            <button id="confirmDelete">Yes, Delete</button>
            <button id="cancelDelete">Cancel</button>
        </div>
    </div>

    <div id="propertyTypeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="propertyTypeModalTitle">Add Property Type</h2>
            <form id="propertyTypeForm">
                <input type="hidden" name="property_type_id" id="propertyTypeId">
                <input type="text" name="property_type_name" id="propertyTypeName" placeholder="Property Type Name" required>
                <button type="submit">Save Property Type</button>
            </form>
        </div>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="userModalTitle">Add User</h2>
            <form id="userForm">
                <input type="hidden" name="user_id" id="userId">
                <input type="text" name="firstName" id="userFirstName" placeholder="First Name" required>
                <input type="text" name="lastName" id="userLastName" placeholder="Last Name" required>
                <input type="email" name="email" id="userEmail" placeholder="Email" required>
                <select name="type" id="userType" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <input type="password" name="password" id="userPassword" placeholder="Password" required>
                <button type="submit">Save User</button>
            </form>
        </div>
    </div>

    <header style="background-color: #a81e35; color: white; padding: 1rem; text-align: center;">
        <h1>Core Real Estate Admin Dashboard</h1>
    </header>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="admin-profile">
                <img src="<?php echo $admin['profileImage']; ?>" alt="Admin Profile" class="profile-image" onerror="this.src='path/to/default-image.jpg';">
                <p>Hello, <?php echo $admin['firstName']; ?></p>
                <p class="online-status"><span class="online-indicator"></span> Online now</p>
            </div>
            <nav>
                <ul>
                    <li><a href="?action=dashboard">Dashboard</a></li>
                    <li><a href="?action=property_type">Property Type</a></li>
                    <li><a href="?action=country">Country</a></li>
                    <li><a href="?action=state">State</a></li>
                    <li><a href="?action=city">City</a></li>
                    <li><a href="?action=owner">Owner</a></li>
                    <li><a href="?action=agents">Agents</a></li>
                    <li><a href="?action=user">User</a></li>
                    <li><a href="?action=properties">List of Properties</a></li>
                    <li><a href="?action=reviews">Reviews</a></li>
                    <li><a href="?action=pages">Pages</a></li>
                    <li><a href="?action=search">Search Property</a></li>
                    <li><a href="?action=profile">Update Profile</a></li>
                    <li><a href="?action=change_password">Change Password</a></li>
                    <li><a href="../signIn_Login/admin_logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="dashboard-content">
            <?php echo $content; ?>
        </main>
    </div>

    

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addPropertyBtn = document.querySelector('.add-property-btn');
            const addUserBtn = document.querySelector('.add-user-btn');
            const propertyModal = document.getElementById('addPropertyModal');
            const userModal = document.getElementById('userModal');
            const userForm = document.getElementById('userForm');
            const userModalTitle = document.getElementById('userModalTitle');

            // Open modal for adding new property
            if (addPropertyBtn) {
                addPropertyBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    propertyModal.style.display = "block";
                    populateDropdowns();
                });
            }

            // Open modal for adding new user
            if (addUserBtn) {
                addUserBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openUserModal();
                });
            }

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                propertyModal.style.display = "none";
                userModal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == propertyModal) {
                    propertyModal.style.display = "none";
                }
                if (event.target == userModal) {
                    userModal.style.display = "none";
                }
            }

            function populateDropdowns(prefix = '') {
                const selectors = ['property_type_id', 'country_id', 'state_id', 'city_id'];
                const urls = {
                    'property_type_id': '../properties/get_property_types.php',
                    'country_id': '../countries/get_countries.php',
                    'state_id': '../states/get_states.php',
                    'city_id': '../cities/get_cities.php'
                };

                selectors.forEach(selector => {
                    const select = document.querySelector(`${prefix ? '#' + prefix : '#addPropertyModal'} select[name="${selector}"]`);
                    fetch(urls[selector])
                        .then(response => response.json())
                        .then(data => {
                            select.innerHTML = `<option value="">Select ${selector.replace('_id', '').replace(/^\w/, c => c.toUpperCase())}</option>`;
                            data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.id;
                                option.textContent = item.name;
                                select.appendChild(option);
                            });
                        });
                });

                // Add event listeners for country and state dropdowns
                const countrySelect = document.querySelector(`${prefix ? '#' + prefix : '#addPropertyModal'} select[name="country_id"]`);
                const stateSelect = document.querySelector(`${prefix ? '#' + prefix : '#addPropertyModal'} select[name="state_id"]`);
                const citySelect = document.querySelector(`${prefix ? '#' + prefix : '#addPropertyModal'} select[name="city_id"]`);

                countrySelect.addEventListener('change', function() {
                    populateStates(this.value, prefix);
                });

                stateSelect.addEventListener('change', function() {
                    populateCities(this.value, prefix);
                });
            }

            function populateStates(countryId, prefix = '') {
                const stateSelect = document.querySelector(`${prefix ? '#' + prefix : '#addPropertyModal'} select[name="state_id"]`);
                const citySelect = document.querySelector(`${prefix ? '#' + prefix : '#addPropertyModal'} select[name="city_id"]`);

                // Reset state and city dropdowns
                stateSelect.innerHTML = '<option value="">Select State</option>';
                citySelect.innerHTML = '<option value="">Select City</option>';

                if (countryId) {
                    fetch(`../states/get_states.php?country_id=${countryId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(state => {
                                const option = document.createElement('option');
                                option.value = state.id;
                                option.textContent = state.name;
                                stateSelect.appendChild(option);
                            });
                        });
                }
            }

            function populateCities(stateId, prefix = '') {
                const citySelect = document.querySelector(`${prefix ? '#' + prefix : '#addPropertyModal'} select[name="city_id"]`);

                // Reset city dropdown
                citySelect.innerHTML = '<option value="">Select City</option>';

                if (stateId) {
                    fetch(`../cities/get_cities.php?state_id=${stateId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.textContent = city.name;
                                citySelect.appendChild(option);
                            });
                        });
                }
            }

            // Call this function for both add and edit forms
            populateDropdowns();
            populateDropdowns('editPropertyModal');

            document.getElementById('addPropertyForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('../properties/add_property.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Property added successfully!');
                        propertyModal.style.display = "none";
                        // Refresh the property table without reloading the page
                        refreshPropertyTable();
                        // Reset the form
                        this.reset();
                    } else {
                        alert('Error adding property: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });

            // Add this new function to refresh the property table
            function refreshPropertyTable() {
                fetch('../properties/get_property_table.php')
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('.dashboard-content').innerHTML = html;
                        // Reattach event listener to the new "Add Property" button
                        attachAddPropertyButtonListener();
                    })
                    .catch(error => {
                        console.error('Error refreshing property table:', error);
                    });
            }

            // Add this function to attach the event listener to the "Add Property" button
            function attachAddPropertyButtonListener() {
                const addPropertyBtn = document.querySelector('.add-property-btn');
                if (addPropertyBtn) {
                    addPropertyBtn.onclick = function() {
                        propertyModal.style.display = "block";
                        populateDropdowns();
                    }
                }
            }

            // Call this function when the page loads
            attachAddPropertyButtonListener();

            let propertyToDelete = null;

            document.querySelector('.property-table').addEventListener('click', function(e) {
                if (e.target.closest('.delete-icon')) {
                    e.preventDefault();
                    propertyToDelete = e.target.closest('tr').dataset.propertyId;
                    document.getElementById('deleteConfirmModal').style.display = 'block';
                }
            });

            document.getElementById('confirmDelete').addEventListener('click', function() {
                if (propertyToDelete) {
                    deleteProperty(propertyToDelete);
                }
            });

            document.getElementById('cancelDelete').addEventListener('click', function() {
                document.getElementById('deleteConfirmModal').style.display = 'none';
                propertyToDelete = null;
            });

            function deleteProperty(propertyId) {
                fetch('../properties/delete_property.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'property_id=' + propertyId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        document.querySelector(`tr[data-property-id="${propertyId}"]`).remove();
                        alert('Property deleted successfully!');
                    } else {
                        alert('Error deleting property: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the property.');
                })
                .finally(() => {
                    document.getElementById('deleteConfirmModal').style.display = 'none';
                    propertyToDelete = null;
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const propertyTypeModal = document.getElementById('propertyTypeModal');
                const propertyTypeForm = document.getElementById('propertyTypeForm');
                const propertyTypeModalTitle = document.getElementById('propertyTypeModalTitle');
                const propertyTypeNameInput = document.getElementById('propertyTypeName');
                const propertyTypeIdInput = document.getElementById('propertyTypeId');

                // Open modal for adding new property type
                document.body.addEventListener('click', function(e) {
                    if (e.target.closest('.add-property-type-btn')) {
                        e.preventDefault();
                        openPropertyTypeModal();
                    }
                });

                // Close property type modal
                propertyTypeModal.querySelector('.close').onclick = function() {
                    propertyTypeModal.style.display = 'none';
                };

                // Handle property type form submission
                propertyTypeForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const isEdit = formData.get('property_type_id') !== '';

                    fetch(isEdit ? '../properties/update_property_type.php' : '../properties/add_property_type.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(isEdit ? 'Property type updated successfully!' : 'Property type added successfully!');
                            propertyTypeModal.style.display = 'none';
                            refreshPropertyTypeTable();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });

                function openPropertyTypeModal(propertyTypeId = null) {
                    propertyTypeModalTitle.textContent = propertyTypeId ? 'Edit Property Type' : 'Add Property Type';
                    propertyTypeForm.reset();
                    document.getElementById('propertyTypeId').value = propertyTypeId || '';

                    if (propertyTypeId) {
                        // Fetch property type details if editing
                        fetch(`../properties/get_property_type.php?id=${propertyTypeId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('propertyTypeName').value = data.propertyType.name;
                                } else {
                                    alert('Error fetching property type details: ' + data.error);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }

                    propertyTypeModal.style.display = 'block';
                }

                function refreshPropertyTypeTable() {
                    fetch('../properties/get_property_type_table.php')
                        .then(response => response.text())
                        .then(html => {
                            document.querySelector('.dashboard-content').innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error refreshing property type table:', error);
                        });
                }

                // Handle edit and delete actions for property types
                document.body.addEventListener('click', function(e) {
                    if (e.target.closest('.edit-property-type')) {
                        e.preventDefault();
                        const id = e.target.closest('.edit-property-type').dataset.id;
                        openPropertyTypeModal(id);
                    } else if (e.target.closest('.delete-property-type')) {
                        e.preventDefault();
                        const id = e.target.closest('.delete-property-type').dataset.id;
                        if (confirm('Are you sure you want to delete this property type?')) {
                            deletePropertyType(id);
                        }
                    }
                });

                function deletePropertyType(id) {
                    fetch('../properties/delete_property_type.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Property type deleted successfully!');
                            refreshPropertyTypeTable();
                        } else {
                            alert('Error deleting property type: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
            const editPropertyModal = document.getElementById('editPropertyModal');
            const editPropertyForm = document.getElementById('editPropertyForm');

            // Handle edit icon click
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.edit-icon')) {
                    e.preventDefault();
                    const propertyId = e.target.closest('.edit-icon').dataset.id;
                    openEditPropertyModal(propertyId);
                }
            });

            // Close edit modal
            editPropertyModal.querySelector('.close').onclick = function() {
                editPropertyModal.style.display = 'none';
            };

            // Handle edit form submission
            editPropertyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('../properties/update_property.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Property updated successfully!');
                        editPropertyModal.style.display = 'none';
                        refreshPropertyTable();
                    } else {
                        alert('Error updating property: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });

            function openEditPropertyModal(propertyId) {
                fetch(`../properties/get_property.php?id=${propertyId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const property = data.property;
                            document.getElementById('editPropertyId').value = property.id;
                            document.getElementById('editPropertyTypeId').value = property.property_type_id;
                            document.getElementById('editCountryId').value = property.country_id;
                            document.getElementById('editStateId').value = property.state_id;
                            document.getElementById('editCityId').value = property.city_id;
                            document.getElementById('editTitle').value = property.title;
                            document.getElementById('editDescription').value = property.description;
                            document.getElementById('editPrice').value = property.price;
                            document.getElementById('editBedrooms').value = property.bedrooms;
                            document.getElementById('editBathrooms').value = property.bathrooms;
                            document.getElementById('editArea').value = property.area;
                            document.getElementById('editAddress').value = property.address;
                            document.getElementById('editStatus').value = property.status;

                            // Populate dropdowns
                            populateDropdowns('editPropertyModal');

                            // Set the selected values after populating dropdowns
                            setTimeout(() => {
                                document.getElementById('editPropertyTypeId').value = property.property_type_id;
                                document.getElementById('editCountryId').value = property.country_id;
                                populateStates(property.country_id, 'editPropertyModal');
                                setTimeout(() => {
                                    document.getElementById('editStateId').value = property.state_id;
                                    populateCities(property.state_id, 'editPropertyModal');
                                    setTimeout(() => {
                                        document.getElementById('editCityId').value = property.city_id;
                                    }, 100);
                                }, 100);
                            }, 100);

                            editPropertyModal.style.display = 'block';
                        } else {
                            alert('Error fetching property details: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            function populateStates(countryId, prefix = '') {
                fetch(`../states/get_states.php?country_id=${countryId}`)
                    .then(response => response.json())
                    .then(data => {
                        const select = document.querySelector(`select[name="state_id"]${prefix ? '#' + prefix + 'StateId' : ''}`);
                        select.innerHTML = '<option value="">Select State</option>';
                        data.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            select.appendChild(option);
                        });
                    });
            }

            function populateCities(stateId, prefix = '') {
                fetch(`../cities/get_cities.php?state_id=${stateId}`)
                    .then(response => response.json())
                    .then(data => {
                        const select = document.querySelector(`select[name="city_id"]${prefix ? '#' + prefix + 'CityId' : ''}`);
                        select.innerHTML = '<option value="">Select City</option>';
                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            select.appendChild(option);
                        });
                    });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const userModal = document.getElementById('userModal');
            const userForm = document.getElementById('userForm');
            const userModalTitle = document.getElementById('userModalTitle');

            // Open modal for adding new user
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.add-user-btn')) {
                    e.preventDefault();
                    openUserModal();
                }
            });

            // Handle edit and delete actions
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.edit-user')) {
                    e.preventDefault();
                    const userId = e.target.closest('.edit-user').dataset.id;
                    const userType = e.target.closest('.edit-user').dataset.type;
                    openUserModal(userId, userType);
                } else if (e.target.closest('.delete-user')) {
                    e.preventDefault();
                    const userId = e.target.closest('.delete-user').dataset.id;
                    const userType = e.target.closest('.delete-user').dataset.type;
                    if (confirm('Are you sure you want to delete this user?')) {
                        deleteUser(userId, userType);
                    }
                }
            });

            // Close modal
            userModal.querySelector('.close').onclick = function() {
                userModal.style.display = 'none';
            };

            // Handle form submission
            userForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const isEdit = formData.get('user_id') !== '';

                fetch(isEdit ? '../users/update_user.php' : '../users/add_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(isEdit ? 'User updated successfully!' : 'User added successfully!');
                        userModal.style.display = 'none';
                        refreshUserTable();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });

            function deleteUser(userId, userType) {
                fetch('../users/delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}&type=${userType}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully!');
                        refreshUserTable();
                    } else {
                        alert('Error deleting user: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            function refreshUserTable() {
                fetch('../users/get_user_table.php')
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('.dashboard-content').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error refreshing user table:', error);
                    });
            }

            function openUserModal(userId = null, userType = null) {
                userModalTitle.textContent = userId ? 'Edit User' : 'Add User';
                userForm.reset();
                document.getElementById('userId').value = userId || '';
                document.getElementById('userPassword').required = !userId;
                document.getElementById('userType').value = userType ? userType.toLowerCase() : 'user';

                if (userId) {
                    fetch(`../users/get_user.php?id=${userId}&type=${userType}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const user = data.user;
                                document.getElementById('userFirstName').value = user.firstName;
                                document.getElementById('userLastName').value = user.lastName;
                                document.getElementById('userEmail').value = user.email;
                            } else {
                                alert('Error fetching user details: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }

                userModal.style.display = 'block';
            }
        });
    });


        const userModal = document.getElementById('userModal');
        const userForm = document.getElementById('userForm');
        const userModalTitle = document.getElementById('userModalTitle');

        // Open modal for adding new user
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.add-user-btn')) {
                e.preventDefault();
                openUserModal();
            }
        });

        // Handle edit and delete actions for users
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.edit-user')) {
                e.preventDefault();
                const id = e.target.closest('.edit-user').dataset.id;
                const type = e.target.closest('.edit-user').dataset.type;
                openUserModal(id, type);
            } else if (e.target.closest('.delete-user')) {
                e.preventDefault();
                const id = e.target.closest('.delete-user').dataset.id;
                const type = e.target.closest('.delete-user').dataset.type;
                if (confirm('Are you sure you want to delete this user?')) {
                    deleteUser(id, type);
                }
            }
        });

        // Close modal
        userModal.querySelector('.close').onclick = function() {
            userModal.style.display = 'none';
        };

        // Handle form submission
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const isEdit = formData.get('user_id') !== '';

            fetch(isEdit ? '../users/update_user.php' : '../users/add_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(isEdit ? 'User updated successfully!' : 'User added successfully!');
                    userModal.style.display = 'none';
                    refreshUserTable();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        function openUserModal(userId = null, userType = null) {
            userModalTitle.textContent = userId ? 'Edit User' : 'Add User';
            userForm.reset();
            document.getElementById('userId').value = userId || '';
            document.getElementById('userPassword').required = !userId;
            document.getElementById('userType').value = userType ? userType.toLowerCase() : 'user';

            if (userId) {
                fetch(`../users/get_user.php?id=${userId}&type=${userType}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.user;
                            document.getElementById('userFirstName').value = user.firstName;
                            document.getElementById('userLastName').value = user.lastName;
                            document.getElementById('userEmail').value = user.email;
                        } else {
                            alert('Error fetching user details: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            userModal.style.display = 'block';
        }

        function deleteUser(userId, userType) {
            fetch('../users/delete_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}&type=${userType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User deleted successfully!');
                    refreshUserTable();
                } else {
                    alert('Error deleting user: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function refreshUserTable() {
            fetch('../users/get_user_table.php')
                .then(response => response.text())
                .then(html => {
                    document.querySelector('.dashboard-content').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error refreshing user table:', error);
                });
        }

    </script>
</body>
</html>