<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['user'];
    $password = $_POST['pass'];
    $confirm_password = $_POST['confirm_pass'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Password tidak sesuai!'); window.location='../index.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Masukkan data ke database
    $query = "INSERT INTO admin (username, password) VALUES ('$username', '$hashed_password')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='../index.php';</script>";
    } else {
        echo "<script>alert('Registrasi gagal!'); window.location='../index.php';</script>";
    }
} else {
    header('location:../index.php');
}
?>
