<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

// 1. Check passwords match
if ($password !== $confirm) {
    header("Location: register.php?error=nomatch");
    exit();
}

// 2. Check if email already exists
$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if (mysqli_num_rows($check) > 0) {
    header("Location: register.php?error=exists");
    exit();
}

// 3. Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 4. Insert user
$sql = "INSERT INTO users (full_name, email, password)
VALUES ('$name', '$email', '$hashedPassword')";

if (mysqli_query($conn, $sql)) {
    header("Location: login.php?success=registered");
    exit();
} else {
    header("Location: register.php?error=failed");
    exit();
}
?>