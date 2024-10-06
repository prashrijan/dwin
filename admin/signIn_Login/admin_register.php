<?php
include '../dbConnection/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Handle file upload
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "../uploads";
    
    // Create the directory if it doesn't exist
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            $error = "Failed to create upload directory.";
        }
    }
    
    $targetFile = $targetDir . basename($_FILES["profileImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profileImage"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $error = "File is not an image.";
        $uploadOk = 0;
    }
    
    // Check file size
    if ($_FILES["profileImage"]["size"] > 500000000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    // If everything is ok, try to upload file and insert into database
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            $relativeFilePath = "/DWINGroupProject/uploads/" . basename($_FILES["profileImage"]["name"]);
            $sql = "INSERT INTO admins (firstName, lastName, email, password, profileImage, role) VALUES (?, ?, ?, ?, ?, 'admin')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $firstName, $lastName, $email, $password, $relativeFilePath);
            
            try {
                if ($stmt->execute()) {
                    $admin_id = $stmt->insert_id;
                    $_SESSION['admin_id'] = $admin_id;
                    $_SESSION['admin_email'] = $email;
                    header("Location: ../dashboard/admin_dashboard.php");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $emailError = "This email is already registered.";
                } else {
                    $error = "Error: " . $e->getMessage();
                }
            }
            $stmt->close();
        } else {
            $error = "Sorry, there was an error uploading your file. Error: " . error_get_last()['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Core Real Estate</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Core Real Estate</h1>
    </header>
    <div class="container">
        <div class="register-container">
            <h2>Admin Registration</h2>
            <?php 
            if (isset($error)) echo "<p class='error'>$error</p>";
            if (isset($success)) echo "<p class='success'>$success</p>";
            ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="firstName" placeholder="First Name" required>
                <input type="text" name="lastName" placeholder="Last Name" required>
                <div class="input-group <?php echo isset($emailError) ? 'error' : ''; ?>">
                    <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <?php if (isset($emailError)) echo "<span class='error-message'>$emailError</span>"; ?>
                </div>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class="ri-eye-line toggle-password" onclick="togglePassword()"></i>
                </div>
                <div class="file-input">
                    <label for="profileImage">Profile Image:</label>
                    <input type="file" name="profileImage" id="profileImage" accept="image/*" required>
                </div>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="admin_login.php">Login</a></p>
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