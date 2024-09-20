<?php
session_start();
include '../dbConnection/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_email'] = $row['email'];
            header("Location: ../dashboard/admin_dashboard.php");
            exit();
        } else {
            echo "Invalid password";
        }
    } else {
        echo "Admin not found";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Core Real Estate</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Core Real Estate</h1>
    </header>
    <div class="container">
        <div class="login-container">
            <h2>Admin Login</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form action="" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class="ri-eye-line toggle-password" onclick="togglePassword()"></i>
                </div>
                <button type="submit">Login</button>
            </form>
            <p><a href="admin_recover_password.php">Forgot Password?</a></p>
            <p>New admin? <a href="admin_register.php">Sign Up</a></p>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = document.querySelector(".toggle-password");
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("ri-eye-line");
                toggleIcon.classList.add("ri-eye-off-line");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("ri-eye-off-line");
                toggleIcon.classList.add("ri-eye-line");
            }
        }
    </script>
</body>
</html>