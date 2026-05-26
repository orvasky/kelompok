<?php
session_start();

$_SESSION['login'] = false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARA MEMBUAT LOGIN DENGAN SESSION DI PHP</title>
    <style rel="stylesheet" >
        body {
            font-family: Verdana;
            font-size: 14px;
            background-color: #f7f7f7;
            input, button {
                padding: 7px;
            }
        }
        input, button{
            padding: 7px;
}
button {
    cursor: pointer;
}
        .container {
            max-width: 400px;
            width: 400px;
            margin: 0 auto;
            background-color: #FFFFFF;
            padding: 10px;
            border: 1px solid #000000;
        }
        .container .form-control {
            margin-bottom: 10px;
            width: 100%;
        }
        .container .form-control:last-child {
            margin-bottom: 0;
        }
        .container .form-control input{
            width: 380px;
        }
        .container .form-control {
            width: 397px;
        }
        .container .pesan {
            color: #FFFFFF;
            text-align: center;
            padding: 7px;
            background-color: #FFFFFF;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><center>PANEL LOGIN</center></h1>
        <hr/>
        <form action="cek_login.php" method="post">
            <div class="form-control">
                <input type="text" name="user" placeholder="Masukkan Username" >
            </div>
            <div class="form-control">
                <input type="password" name="pass" placeholder="Masukkan Password" >
            </div>
            <div class="form-control">
                <button type="submit">Login</button>
            </div>
            <?php  
            if(isset($_GET['p'])) {

            ?>
            <div class="pesan"><?php echo $_GET['p'] ?></div>
            <?php } ?>
        </form>

    </div>
</body>
</html>