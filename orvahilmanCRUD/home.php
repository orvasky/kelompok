<?php
session_start();

header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("location:index.php?p=Anda harus login terlebih dahulu");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Halaman Home</title>
</head>
<body>
    <?php include 'navigasi.php'; ?>

    <div id="main">
        <div class="container">
            <h2>APLIKASI</h2>
            <hr>

            <?php
            date_default_timezone_set("Asia/Jakarta");

            $hari_array = array(
                'Minggu',
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
                'Sabtu'
            );

            $bulan_array = array(
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );

            $hari_ini = $hari_array[date('w')];
            $tanggal = date('j');
            $bulan = $bulan_array[date('n')];
            $tahun = date('Y');

            $tanggal_lengkap = "$hari_ini , $tanggal $bulan $tahun";
            
            $jam = date("H");
            if ($jam >= 5 && $jam < 11) {
                $ucapan = "Selamat Pagi";
            } else if ($jam >= 11 && $jam < 15) {
                $ucapan = "Selamat Siang";
            } else if ($jam >= 15 && $jam < 18) {
                $ucapan = "Selamat Sore";
            } else {
                $ucapan = "Selamat Malam";
            }
            ?>
            <p><strong><?php echo $ucapan; ?>, <?php echo $_SESSION['user']; ?></strong></p>
            <p>Hari ini tanggal: <?php echo $tanggal_lengkap; ?></p>

            <p>Selamat datang di aplikasi</p>

        </div>
    </div>
</body>
</html>