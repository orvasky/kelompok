<?php
session_start();

include 'koneksi.php';

$username = $_POST['user'];
$password = $_POST['pass'];

$password_md5 = md5($password);

$query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE username='$username' AND password='$password_md5'");

$cek = mysqli_num_rows($query);

if($cek > 0) {
    $_SESSION['login'] = true;
    $_SESSION['user'] = $username;
    header("location: home.php");
} else {
    header("location:index.php?p=Username atau Password salah");
}
?>