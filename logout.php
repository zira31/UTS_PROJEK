<?php
// Mulai session
session_start();

// Simpan pesan logout
$_SESSION['logout_message'] = 'Anda berhasil logout dari sistem!';

// Hapus data session user
unset($_SESSION['user']);

// Redirect ke halaman login
header("Location: index.php?page=login");
exit();
?>