<?php
session_start();

// Anti cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Cek login
if(!isset($_SESSION['login']) || $_SESSION['login'] != true) {
    header("location:index.php?p=Anda harus login terlebih dahulu");
    exit();
}

include 'koneksi.php';

// TUGAS 3: Menangkap kata kunci pencarian prodi jika tombol cari ditekan
$keyword_prodi = "";
if (isset($_POST['cari_prodi'])) {
    $keyword_prodi = mysqli_real_escape_string($koneksi, trim($_POST['keyword_prodi']));
}

// TUGAS 3: Mengubah query SQL untuk menyaring data prodi
if ($keyword_prodi != "") {
    // Jika user mengetikkan sesuatu di kolom pencarian
    $query = mysqli_query($koneksi, "SELECT * FROM prodi WHERE kd_prodi LIKE '%$keyword_prodi%' OR nama_prodi LIKE '%$keyword_prodi%' ORDER BY id_prodi DESC");
} else {
    // Jika tampil normal (tanpa pencarian)
    $query = mysqli_query($koneksi, "SELECT * FROM prodi ORDER BY id_prodi DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Program Studi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navigasi.php"; ?>

    <div id="main">
        <div class="container">
            <h2>Data Program Studi</h2>
            <hr>

            <?php if (isset($_GET['p'])) { ?>
                <p style="color: blue; font-weight: bold;"><?php echo htmlspecialchars($_GET['p']); ?></p>
            <?php } ?>

            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                <a href="tambah_prodi.php" class="btn" style="background-color: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; font-weight: bold; border-radius: 4px; font-size: 14px;">+ Tambah Prodi</a>
                
                <form action="" method="POST" style="display: flex; gap: 5px;">
                    <input type="text" name="keyword_prodi" value="<?php echo htmlspecialchars($keyword_prodi); ?>" placeholder="Cari Kode / Nama Prodi..." style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; width: 180px;">
                    <button type="submit" name="cari_prodi" style="padding: 6px 12px; background-color: #008CBA; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cari</button>
                    
                    <?php if ($keyword_prodi != "") { ?>
                        <a href="prodi.php" style="padding: 6px 12px; background-color: #e7e7e7; color: black; text-decoration: none; border-radius: 4px; font-size: 13px; border: 1px solid #ccc;">Reset</a>
                    <?php } ?>
                </form>
            </div>

            <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
                <tr style="background-color: #f2f2f2;">
                    <th width="10%">No</th>
                    <th>Kode Prodi</th>
                    <th>Nama Program Studi</th>
                    <th width="25%">Aksi</th>
                </tr>
                <?php
                $no = 1;
                if (mysqli_num_rows($query) > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td align="center"><strong><?php echo htmlspecialchars($data['kd_prodi']); ?></strong></td>
                    <td><?php echo htmlspecialchars($data['nama_prodi']); ?></td>
                    <td align="center">
                        <a href="edit_prodi.php?id=<?php echo $data['id_prodi']; ?>" style="background-color: #008CBA; color: white; padding: 4px 8px; text-decoration: none; border-radius: 3px; font-size: 13px;">Edit</a> | 
                        <a href="hapus_prodi.php?id=<?php echo $data['id_prodi']; ?>" onclick="return confirm('Apakah yakin ingin menghapus prodi <?php echo $data['nama_prodi']; ?>?')" style="background-color: #f44336; color: white; padding: 4px 8px; text-decoration: none; border-radius: 3px; font-size: 13px;">Hapus</a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' align='center' style='color: red; font-style: italic; padding: 15px;'>Data prodi tidak ditemukan.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>