<?php
session_start();

// Anti cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Cek login
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("location:index.php?p=Anda harus login terlebih dahulu");
    exit();
}

include "koneksi.php";

// TUGAS 3: Logika Menangkap Kata Kunci Pencarian Mahasiswa
$keyword = "";
if (isset($_POST['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, trim($_POST['keyword']));
}

// TUGAS 3: Mengubah Query SQL sesuai dengan input pencarian
if ($keyword != "") {
    // Jika sedang mencari sesuatu
    $query = mysqli_query($koneksi, "SELECT mahasiswa.*, prodi.nama_prodi FROM mahasiswa 
                                    LEFT JOIN prodi ON mahasiswa.kd_prodi = prodi.kd_prodi 
                                    WHERE mahasiswa.npm LIKE '%$keyword%' OR mahasiswa.nama LIKE '%$keyword%' 
                                    ORDER BY mahasiswa.id DESC");
} else {
    // Jika normal tanpa pencarian
    $query = mysqli_query($koneksi, "SELECT mahasiswa.*, prodi.nama_prodi FROM mahasiswa 
                                    LEFT JOIN prodi ON mahasiswa.kd_prodi = prodi.kd_prodi 
                                    ORDER BY mahasiswa.id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navigasi.php"; ?>

    <div id="main">
        <div class="container">
            <h2>Data Mahasiswa</h2>
            <hr>

            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                <a href="tambah_mahasiswa.php" class="btn" style="background-color: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; font-weight: bold; border-radius: 4px;">+ Tambah Mahasiswa</a>
                
                <form action="" method="POST" style="display: flex; gap: 5px;">
                    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari Nama atau NPM..." style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; width: 200px;">
                    <button type="submit" name="cari" style="padding: 6px 12px; background-color: #008CBA; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cari</button>
                    <?php if ($keyword != "") { ?>
                        <a href="mahasiswa.php" style="padding: 6px 12px; background-color: #e7e7e7; color: black; text-decoration: none; border-radius: 4px; font-size: 13px;">Reset</a>
                    <?php } ?>
                </form>
            </div>

            <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>Foto</th> <th>NPM</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Semester</th>
                    <th>Prodi</th>
                    <th>Gender</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $no = 1;
                if (mysqli_num_rows($query) > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                ?>
                <tr>
                    <td align="center"><?php echo $no++; ?></td>
                    <td align="center">
                        <img src="uploads/<?php echo !empty($data['foto']) ? $data['foto'] : 'default.png'; ?>" width="40" height="40" style="border-radius: 50%; object-fit: cover; border: 1px solid #ccc;">
                    </td>
                    <td><?php echo htmlspecialchars($data['npm']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama']); ?></td>
                    <td><?php echo htmlspecialchars($data['kelas']); ?></td>
                    <td align="center"><?php echo htmlspecialchars($data['semester']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama_prodi']); ?></td>
                    <td><?php echo htmlspecialchars($data['jenis_kelamin']); ?></td>
                    <td align="center">
                        <a href="edit_mahasiswa.php?id=<?php echo $data['id']; ?>" style="background-color: #008CBA; color: white; padding: 4px 8px; text-decoration: none; border-radius: 3px;">Edit</a> | 
                        <a href="hapus_mahasiswa.php?id=<?php echo $data['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data <?php echo $data['nama']; ?>?')" style="background-color: #f44336; color: white; padding: 4px 8px; text-decoration: none; border-radius: 3px;">Hapus</a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='9' align='center' style='color: red; font-style: italic; padding: 15px;'>Data mahasiswa tidak ditemukan.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>