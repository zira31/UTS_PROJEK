<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit();
}

$username = htmlspecialchars($_SESSION['user']['username']);
?>

<style>
    .data-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 30px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>

<div class="data-container">
    <h2>Data Pengguna</h2>
    <p>Selamat datang <strong><?php echo $username; ?></strong>! 🎉</p>
    <p>Ini adalah halaman data kamu.</p>
    
    <p align='center' style="margin-top: 30px;">Aku Jerman Kamu Perancis, aku perhatikan kamu makin manis!</p>
    <p align='center'>
        <img src="menggodah.jpg" alt="Foto Kakan" width="400" style="border-radius: 8px;"><br>
        <strong>HAHAHAY PAPAPALE</strong>
    </p>
</div>