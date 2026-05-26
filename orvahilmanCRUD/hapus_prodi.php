<?php
session_start();

header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if(!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("location:index.php?p=Anda harus login terlebih dahulu");
    exit();
}

include 'koneksi.php';

// Pastikan parameter id_prodi dikirim lewat URL
if(!isset($_GET['id'])) {
    header("location:prodi.php");
    exit();
}

$id_prodi = mysqli_real_escape_string($koneksi, $_GET['id']);

// 1. Ambil data prodi yang mau dihapus berdasarkan primary key (id_prodi)
$q = mysqli_query($koneksi, "SELECT * FROM prodi WHERE id_prodi='$id_prodi'");
$data = mysqli_fetch_assoc($q);

if(!$data) {
    header("location:prodi.php");
    exit();
}

$kd_prodi = $data['kd_prodi'];

// TUGAS 1 (Validasi): Cek apakah kode prodi ini masih digunakan oleh data mahasiswa
// Pengecekan dialihkan ke tabel mahasiswa
$cek = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE kd_prodi='$kd_prodi'");

if(mysqli_num_rows($cek) > 0) {
    // Jika masih ada mahasiswa yang pakai prodi ini, gagalkan penghapusan
    header("location:prodi.php?p=Data tidak dapat dihapus karena masih digunakan oleh mahasiswa");
    exit();
} else {
    // 2. Jika aman tidak digunakan, lakukan penghapusan di tabel prodi (BUKAN tabel mahasiswa)
    $hapus = mysqli_query($koneksi, "DELETE FROM prodi WHERE id_prodi='$id_prodi'");
    
    if ($hapus) {
        // Jika berhasil menghapus
        header("location:prodi.php?p=Data prodi berhasil dihapus");
        exit();
    } else {
        // Jika gagal menghapus karena kendala database
        header("location:prodi.php?p=Gagal menghapus data prodi");
        exit();
    }
}
?>