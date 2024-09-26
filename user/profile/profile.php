<?php
require_once '../connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Core Real Estate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #a81e35;
            color: white;
            text-align: center;
            padding: 1rem;
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 1rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            flex: 1;
        }

        .profile-container {
            max-width: 800px;
            width: 90%;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #a81e35;
        }

        .profile-info {
            margin-top: 1rem;
        }

        .profile-info p {
            margin: 0.75rem 0;
            color: #333;
        }

        .footer-container {
            background-color: #a81e35;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .footer-container p {
            margin: 0;
        }

        .edit-button {
            background-color: #a81e35;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
            transition: background-color 0.3s ease;
        }

        .edit-button:hover {
            background-color: #8a1a2c;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <h1>Core Real Estate</h1>
            <ul>
                <li><a href="../login/index.php">Home</a></li>
                <li><a href="../login/index.php#properties">Properties</a></li>
                <li><a href="../login/index.php#about">About Us</a></li>
                <li><a href="../login/index.php#contact">Contact</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../login/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <h1>User Profile</h1>
            <div class="profile-info">
                <h2>Personal Information</h2>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <!-- Add more user information as needed -->
                <button class="edit-button" onclick="location.href='edit_profile.php'">Edit Profile</button>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; 2024 Core Real Estate. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
