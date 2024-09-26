<?php
require_once '../connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Fetch current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update user information
        $sql = "UPDATE users SET firstName = ?, lastName = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $firstName, $lastName, $email, $user_id);
        $stmt->execute();

        // Update password if provided
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();
        $message = "Profile updated successfully!";
        
        // Refresh user data
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // Update session variables
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['email'] = $email;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error = "An error occurred while updating your profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Core Real Estate</title>
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

        .edit-profile-container {
            max-width: 800px;
            width: 90%;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #a81e35;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 1rem;
            font-weight: bold;
        }

        input {
            padding: 0.5rem;
            margin-top: 0.25rem;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button {
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

        button:hover {
            background-color: #8a1a2c;
        }

        .message {
            text-align: center;
            margin-top: 1rem;
            padding: 10px;
            border-radius: 5px;
        }

        .message.success {
            color: #4CAF50;
            background-color: #e8f5e9;
            border: 1px solid #4CAF50;
        }

        .message.error {
            color: #f44336;
            background-color: #ffebee;
            border: 1px solid #f44336;
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
        <div class="edit-profile-container">
            <h1>Edit Profile</h1>
            <?php if (!empty($message)): ?>
                <p class="message success"><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <p class="message error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="edit_profile.php" method="post">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required>

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="password">New Password (leave blank to keep current password):</label>
                <input type="password" id="password" name="password">

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; 2024 Core Real Estate. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>