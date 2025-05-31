<?php

session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    // Verifikasi apakah password dan konfirmasi password sama
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $_SESSION['register_error'] = "Password dan konfirmasi password tidak sama.";
        $_SESSION['active_form'] = 'register';
        header("Location: login.php");
        exit();
    }
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        $_SESSION['active_form'] = 'register';
    } else {
        $insertUser = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
        $insertUser->bind_param("sss", $username, $email, $password);
        $insertUser->execute();
    }

    header("Location: login.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password.";
        }
    } else {
        $_SESSION['login_error'] = "Email not found.";
    }
    header("Location: login.php");
    exit();
}

?>