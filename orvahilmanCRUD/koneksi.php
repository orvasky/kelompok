<?php 

$host = "localhost";
$username = "root";
$password = "";
$database = "orvahilman_penilaian";

$koneksi = mysqli_connect($host, $username, $password, $database);

if ($koneksi) {
    echo "Koneksi berhasil";

    $pilih_db = mysqli_select_db($koneksi, $database);
    if($pilih_db) {
        echo "Database berhasil dipilih";
    } else {
        echo "Database gagal dipilih: " ;
    }
}


?>