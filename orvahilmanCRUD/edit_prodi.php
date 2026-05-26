<?php
session_start();

header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("location:index.php?p=Anda harus login terlebih dahulu");
    exit();
}

include 'koneksi.php';

// DISINKRONKAN: Menangkap parameter id sesuai dengan link di file prodi.php
if (!isset($_GET['id'])) {
    header("location:prodi.php");
    exit();
}

$id_prodi = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "SELECT * FROM prodi WHERE id_prodi='$id_prodi'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("location:prodi.php");
    exit();
}

$error = "";

if (isset($_POST['update'])) {
    $kd_prodi = mysqli_real_escape_string($koneksi, trim($_POST['kd_prodi']));
    $nama_prodi = mysqli_real_escape_string($koneksi, trim($_POST['nama_prodi']));

    // TUGAS 1: Validasi input kosong
    if (empty($kd_prodi) || empty($nama_prodi)) {
        $error = "Semua field harus diisi.";
    } else {
        // TUGAS 1: Validasi anti-duplikat (Cek apakah kode prodi baru sudah dipakai prodi lain)
        $cek_kode = mysqli_query($koneksi, "SELECT * FROM prodi WHERE kd_prodi='$kd_prodi' AND id_prodi != '$id_prodi'");
        if (mysqli_num_rows($cek_kode) > 0) {
            $error = "Gagal: Kode Prodi $kd_prodi sudah digunakan oleh program studi lain!";
        } else {
            // PERBAIKAN: Mengubah nama kolom 'nama' menjadi 'nama_prodi' agar sesuai struktur database
            $update = mysqli_query($koneksi, "UPDATE prodi SET kd_prodi='$kd_prodi', nama_prodi='$nama_prodi' WHERE id_prodi='$id_prodi'");
            
            if ($update) {
                header("location:prodi.php?p=Data prodi berhasil diperbarui");
                exit();
            } else {
                $error = "Gagal memperbarui data ke database.";
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
    <title>Edit Data Prodi</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>

<body>

    <?php include 'navigasi.php'; ?>

    <div id="main">
        <div class="container">
            <h2>Edit Data Prodi</h2>
            <hr>

            <?php if (!empty($error)) { ?>
                <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
            <?php } ?>

            <form method="POST">
                <label>Kode Prodi:</label><br>
                <input type="text" name="kd_prodi" value="<?php echo htmlspecialchars($data['kd_prodi']); ?>" required><br><br>

                <label>Nama Prodi:</label><br>
                <input type="text" name="nama_prodi" value="<?php echo htmlspecialchars($data['nama_prodi']); ?>" required><br><br>

                <button type="submit" name="update" class="submit">UPDATE</button>
                <a href="prodi.php" class="batal" style="background-color: #f44336; color: white; padding: 7px 15px; text-decoration: none; display: inline-block; border-radius: 4px; font-size: 14px; margin-left: 5px;">BATAL</a>
            </form>
        </div>
    </div>
</body>

</html>