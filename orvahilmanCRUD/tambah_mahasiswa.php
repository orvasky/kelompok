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

//ambil data prodi
$prodi = mysqli_query($koneksi,"SELECT * FROM prodi");

$error = "";

//simpan
if(isset($_POST['simpan'])) {
    $npm = mysqli_real_escape_string($koneksi, trim($_POST['npm']));
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $semester = isset($_POST['semester']) ? $_POST['semester'] : '';
    $kd_prodi = mysqli_real_escape_string($koneksi, $_POST['kd_prodi']);
    $jk = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';

    // TUGAS 1: Validasi form teks kosong
    if(empty($npm) || empty($nama) || empty($kd_prodi) || empty($semester) || empty($jk)) {
        $error = "Semua field wajib diisi!";
    } else {
        // TUGAS 1: Validasi mengecek NPM Duplikat
        $cek_npm = mysqli_query($koneksi, "SELECT npm FROM mahasiswa WHERE npm = '$npm'");
        if(mysqli_num_rows($cek_npm) > 0) {
            $error = "Gagal: NPM $npm sudah terdaftar di sistem!";
        } else {
            
            // TUGAS 2: Proses Validasi & Upload Berkas Gambar Foto Profil
            $nama_foto   = $_FILES['foto']['name'];
            $tmp_foto    = $_FILES['foto']['tmp_name'];
            $ukuran_foto = $_FILES['foto']['size'];
            $error_foto  = $_FILES['foto']['error'];

            if ($error_foto === 0) {
                // TUGAS 1: Validasi Ukuran Foto (Maksimal 2MB)
                if ($ukuran_foto > 2097152) {
                    $error = "Gagal: Ukuran file foto terlalu besar! Maksimal 2MB.";
                } else {
                    // TUGAS 1: Validasi Ekstensi File Foto
                    $ekstensi_boleh = ['jpg', 'jpeg', 'png'];
                    $ekstensi_file  = strtolower(pathinfo($nama_foto, PATHINFO_EXTENSION));
                    
                    if (!in_array($ekstensi_file, $ekstensi_boleh)) {
                        $error = "Gagal: Format berkas salah! Harus berupa JPG, JPEG, atau PNG.";
                    } else {
                        // Jika lolos validasi gambar, buat nama unik baru dan pindahkan file
                        $foto_baru = time() . "_" . $npm . "." . $ekstensi_file;
                        move_uploaded_file($tmp_foto, "uploads/" . $foto_baru);
                    }
                }
            } else {
                // Jika tidak ada file yang diupload, gunakan foto default
                $foto_baru = "default.png";
            }

            // Jika tidak ada error sama sekali dari rangkaian validasi di atas, lakukan insert ke database
            if(empty($error)) {
                mysqli_query($koneksi,"INSERT INTO mahasiswa (npm, nama, kelas, semester, kd_prodi, jenis_kelamin, foto) 
                VALUES ('$npm', '$nama', '$kelas', '$semester', '$kd_prodi', '$jk', '$foto_baru')");
                
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
    <title>Tambah Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navigasi.php"; ?>

    <div id="main">
        <div class="container">
            <h2>Tambah Mahasiswa</h2>
            <hr>

            <?php if(!empty($error)) { ?>
                <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tr>
                        <td width="150">NPM</td>
                        <td><input type="text" name="npm" value="<?php echo isset($_POST['npm']) ? htmlspecialchars($_POST['npm']) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td><input type="text" name="nama" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required></td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td><input type="text" name="kelas" value="<?php echo isset($_POST['kelas']) ? htmlspecialchars($_POST['kelas']) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <td>Semester</td>
                        <td>
                            <input type="radio" name="semester" value="I" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'I') ? 'checked' : ''; ?>> I
                            <input type="radio" name="semester" value="II" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'II') ? 'checked' : ''; ?>> II
                            <input type="radio" name="semester" value="III" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'III') ? 'checked' : ''; ?>> III
                            <input type="radio" name="semester" value="IV" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'IV') ? 'checked' : ''; ?>> IV
                            <input type="radio" name="semester" value="V" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'V') ? 'checked' : ''; ?>> V
                            <input type="radio" name="semester" value="VI" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'VI') ? 'checked' : ''; ?>> VI
                        </td>
                    </tr>
                    <tr>
                        <td>Prodi</td>
                        <td>
                            <select name="kd_prodi" required>
                                <option value="">Pilih Prodi</option>
                                <?php 
                                mysqli_data_seek($prodi, 0); // Reset pointer loop prodi
                                while($p = mysqli_fetch_assoc($prodi)) { 
                                    $selected = (isset($_POST['kd_prodi']) && $_POST['kd_prodi'] == $p['kd_prodi']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $p['kd_prodi']; ?>" <?php echo $selected; ?>>
                                        <?php echo $p['nama_prodi']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>
                            <input type="radio" name="jenis_kelamin" value="Laki-laki" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki-laki') ? 'checked' : ''; ?>> Laki-laki
                            <input type="radio" name="jenis_kelamin" value="Perempuan" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') ? 'checked' : ''; ?>> Perempuan
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Foto Profil</td>
                        <td>
                            <input type="file" name="foto" accept="image/*"><br>
                            <small style="color: gray;">*Format: JPG/JPEG/PNG (Maksimal 2MB)</small>
                        </td>
                    </tr>
                    
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" name="simpan" class="submit">SUBMIT</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>