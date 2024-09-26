<?php
require_once '../connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstName'] = $user['firstName'];
            $_SESSION['lastName'] = $user['lastName'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: login.php");
        exit();
    }
}