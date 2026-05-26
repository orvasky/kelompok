<?php
session_start();

//anti cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

//cek login
if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("location:index.php?p=Anda harus login terlebih dahulu");
    exit();
}

include "koneksi.php";

//validasi id
if(!isset($_GET['id'])) {
    header("location:mahasiswa.php");
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET["id"]);

//ambil data mahasiswa
$query = mysqli_query($koneksi,"SELECT * FROM mahasiswa WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if(!$data) {
    header("location:mahasiswa.php");
    exit();
}

//ambil data prodi
$prodi = mysqli_query($koneksi,"SELECT * FROM prodi");
$error = "";

//proses update
if(isset($_POST['update'])) {
    $npm = mysqli_real_escape_string($koneksi, trim($_POST['npm']));
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $semester = isset($_POST['semester']) ? $_POST['semester'] : '';
    $kd_prodi = mysqli_real_escape_string($koneksi, $_POST['kd_prodi']);
    $jk = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';

    // TUGAS 1: Validasi field kosong
    if(empty($npm) || empty($nama) || empty($kd_prodi) || empty($semester) || empty($jk)) {
        $error = "Semua field wajib diisi!";
    } else {
        // TUGAS 1: Validasi NPM Duplikat (Cek apakah NPM diinput sudah dipakai mahasiswa lain)
        $cek_npm = mysqli_query($koneksi, "SELECT npm FROM mahasiswa WHERE npm = '$npm' AND id != '$id'");
        if(mysqli_num_rows($cek_npm) > 0) {
            $error = "Gagal: NPM $npm sudah digunakan oleh mahasiswa lain!";
        } else {
            
            // TUGAS 2: Ambil data foto yang lama untuk persiapan jika diganti
            $foto_lama = $data['foto'];
            $foto_baru = $foto_lama; // Default menggunakan foto lama jika tidak ganti

            // Proses pemeriksaan berkas foto baru
            $nama_foto   = $_FILES['foto']['name'];
            $tmp_foto    = $_FILES['foto']['tmp_name'];
            $ukuran_foto = $_FILES['foto']['size'];
            $error_foto  = $_FILES['foto']['error'];

            if ($error_foto === 0) {
                // TUGAS 1: Validasi Ukuran Berkas Foto Baru (Maks 2MB)
                if ($ukuran_foto > 2097152) {
                    $error = "Gagal: Ukuran berkas foto terlalu besar! Maksimal 2MB.";
                } else {
                    // TUGAS 1: Validasi Format Ekstensi Foto Baru
                    $ekstensi_boleh = ['jpg', 'jpeg', 'png'];
                    $ekstensi_file  = strtolower(pathinfo($nama_foto, PATHINFO_EXTENSION));
                    
                    if (!in_array($ekstensi_file, $ekstensi_boleh)) {
                        $error = "Gagal: Format berkas salah! Harus berupa JPG, JPEG, atau PNG.";
                    } else {
                        // Jika valid berkasnya, buat nama unik baru
                        $foto_baru = time() . "_" . $npm . "." . $ekstensi_file;
                        
                        if(move_uploaded_file($tmp_foto, "uploads/" . $foto_baru)) {
                            // Hapus berkas fisik file foto lama dari folder uploads (jika bukan file bawaan)
                            if (!empty($foto_lama) && $foto_lama != "default.png" && file_exists("uploads/" . $foto_lama)) {
                                unlink("uploads/" . $foto_lama);
                            }
                        }
                    }
                }
            }

            // Jika dari rangkaian proses validasi di atas tidak ditemukan error, lakukan query UPDATE
            if(empty($error)) {
                mysqli_query($koneksi,"UPDATE mahasiswa SET npm='$npm', nama='$nama', kelas='$kelas', semester='$semester', kd_prodi='$kd_prodi', jenis_kelamin='$jk', foto='$foto_baru' WHERE id='$id'");
                
                header("location:mahasiswa.php");
                exit();
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
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navigasi.php"; ?>

    <div id="main">
        <div class="container">
            <h2>Edit Mahasiswa</h2>
            <hr>
            
            <?php if(!empty($error)) { ?>
                <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tr>
                        <td width="150">NPM</td>
                        <td><input type="text" name="npm" value="<?php echo htmlspecialchars($data['npm']); ?>" required></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td><input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required></td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td><input type="text" name="kelas" value="<?php echo htmlspecialchars($data['kelas']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Semester</td>
                        <td>
                            <input type="radio" name="semester" value="I" <?php if($data['semester'] == "I") { echo "checked"; } ?>> I
                            <input type="radio" name="semester" value="II" <?php if($data['semester'] == "II") { echo "checked"; } ?>> II
                            <input type="radio" name="semester" value="III" <?php if($data['semester'] == "III") { echo "checked"; } ?>> III
                            <input type="radio" name="semester" value="IV" <?php if($data['semester'] == "IV") { echo "checked"; } ?>> IV
                            <input type="radio" name="semester" value="V" <?php if($data['semester'] == "V") { echo "checked"; } ?>> V
                            <input type="radio" name="semester" value="VI" <?php if($data['semester'] == "VI") { echo "checked"; } ?>> VI
                        </td>
                    </tr>
                    <tr>
                        <td>Prodi</td>
                        <td>
                            <select name="kd_prodi" required>
                                <?php while($p = mysqli_fetch_assoc($prodi)) { ?>
                                    <option value="<?php echo $p['kd_prodi']; ?>" <?php if($p['kd_prodi'] == $data['kd_prodi']) { echo "selected"; } ?>><?php echo $p['nama_prodi']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>
                            <input type="radio" name="jenis_kelamin" value="Laki-laki" <?php if($data['jenis_kelamin'] == "Laki-laki") { echo "checked"; } ?>> Laki-laki
                            <input type="radio" name="jenis_kelamin" value="Perempuan" <?php if($data['jenis_kelamin'] == "Perempuan") { echo "checked"; } ?>> Perempuan
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Foto Profil</td>
                        <td>
                            <div style="margin-bottom: 8px;">
                                <img src="uploads/<?php echo !empty($data['foto']) ? $data['foto'] : 'default.png'; ?>" width="80" height="80" style="border: 1px solid #ccc; object-fit: cover; border-radius: 4px;">
                            </div>
                            <input type="file" name="foto" accept="image/*"><br>
                            <small style="color: gray;">*Kosongkan jika tidak ingin mengubah foto profil (Maks 2MB, JPG/PNG)</small>
                        </td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" name="update" class="submit">UPDATE</button>
                            <a href="mahasiswa.php" style="background-color: #f44336; color: white; padding: 7px 15px; text-decoration: none; display: inline-block; border-radius: 4px; font-size: 14px; margin-left: 5px;">BATAL</a>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>