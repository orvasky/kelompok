<?php
session_start();

header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("location:index.php?p=Anda harus login terlebih dahulu");
    exit();
}

include 'koneksi.php';

$error = "";

if (isset($_POST['simpan'])) {
    // Trim input untuk validasi (Tugas 1)
    $kd_prodi = mysqli_real_escape_string($koneksi, trim($_POST['kd_prodi']));
    $nama_prodi = mysqli_real_escape_string($koneksi, trim($_POST['nama_prodi']));

    // TUGAS 1: Validasi form kosong
    if (empty($kd_prodi) || empty($nama_prodi)) {
        $error = "Semua field harus diisi!";
    } else {
        // TUGAS 1: Validasi Kode Prodi Duplikat
        $cek = mysqli_query($koneksi, "SELECT * FROM prodi WHERE kd_prodi='$kd_prodi'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Kode Prodi sudah ada. Gunakan kode lain!";
        } else {
            // Proses simpan data ke database
            $simpan = mysqli_query($koneksi, "INSERT INTO prodi (kd_prodi, nama_prodi) VALUES ('$kd_prodi', '$nama_prodi')");
            
            if ($simpan) {
                // Jika berhasil, redirect ke halaman tampil data prodi
                header("location: prodi.php");
                exit();
            } else {
                $error = "Gagal menyimpan data ke database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Prodi</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>

<body>

    <?php include 'navigasi.php'; ?>

    <div id="main">
        <div class="container">
            <h2>Tambah Prodi</h2>
            <hr>

            <?php if (!empty($error)) { ?>
                <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
            <?php } ?>

            <form method="POST">
                <label>Kode Prodi:</label><br>
                <input type="text" name="kd_prodi" value="<?php echo isset($_POST['kd_prodi']) ? htmlspecialchars($_POST['kd_prodi']) : ''; ?>" required><br><br>

                <label>Nama Prodi:</label><br>
                <input type="text" name="nama_prodi" value="<?php echo isset($_POST['nama_prodi']) ? htmlspecialchars($_POST['nama_prodi']) : ''; ?>" required><br><br>

                <button type="submit" name="simpan" class="submit">SIMPAN</button>
                <a href="prodi.php" class="batal" style="background-color: #f44336; color: white; padding: 7px 15px; text-decoration: none; display: inline-block; border-radius: 4px; font-size: 14px; margin-left: 5px;">BATAL</a>
            </form>

        </div>
    </div>
</body>

</html>