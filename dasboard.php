<?php
session_start();
include 'koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['user'])) {
    echo "<script>
            alert('Silakan login terlebih dahulu!');
            window.location.href = 'index.php?page=login';
          </script>";
    exit();
}

$user = $_SESSION['user'];
$username = htmlspecialchars($user['username']);
$email = htmlspecialchars($user['email']);
$nim = htmlspecialchars($user['nim'] ?? 'Tidak ada');
$role = htmlspecialchars($user['role']);

// Jika admin, tampilkan dashboard admin
if ($role === 'admin') {
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="style.css">
        <style>
            body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:0; }
            .site-title { background: #007BFF; text-align:center; padding:15px 0; font-size:24px; font-weight:bold; color:white; margin-bottom:20px; }
            nav { background:#f8f9fa; padding:10px; text-align:center; border-bottom:1px solid #ddd; }
            nav a { color:#007BFF; text-decoration:none; margin:0 10px; font-weight:bold; }
            nav a:hover { text-decoration:underline; }
            .container { width:90%; max-width:1000px; background:#fff; margin:30px auto; padding:25px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
            table { width:100%; border-collapse:collapse; margin-top:10px; margin-bottom:30px; }
            th, td { padding:10px; border:1px solid #ddd; text-align:center; }
            th { background-color:#007BFF; color:white; }
            .btn { background-color:#007BFF; color:white; border:none; padding:8px 16px; border-radius:5px; cursor:pointer; text-decoration:none; display:inline-block; }
            .btn-warning { background:#ffc107; color:black; }
            .btn-danger { background:#dc3545; }
            .btn:hover { opacity:0.9; }
        </style>
    </head>
    <body>

    <div class="site-title">Admin Control Panel</div>

    <nav>
        <a href="?page=dashboard">Dashboard</a> |
        <a href="?page=users">Master User</a> |
        <a href="?page=product">Product</a> |
        <a href="supplier.php">Supplier</a> |
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
    <?php
    // ================= DASHBOARD =================
    if ($page == 'dashboard') {
        echo "<h2>Dashboard</h2>";
        echo "<p>Selamat datang kembali, <b>$username</b> 👑</p>";
    }

    // ================= MASTER USER =================
    elseif ($page == 'users') {
        echo "<h2>Master User</h2>";
        $queryUser = mysqli_query($conn, "SELECT * FROM users");
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>";
        while ($userData = mysqli_fetch_assoc($queryUser)) {
            echo "<tr>
                    <td>{$userData['id']}</td>
                    <td>{$userData['username']}</td>
                    <td>{$userData['email']}</td>
                    <td>{$userData['role']}</td>
                  </tr>";
        }
        echo "</table>";
    }

    // ================= PRODUCT =================
    elseif ($page == 'product') {
        echo "<h2>Kelola Produk</h2>";
        $queryProduk = mysqli_query($conn, "SELECT * FROM products");
        echo "<table>
                <tr>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Expired Date</th>
                    <th>Aksi</th>
                </tr>";
        while ($prod = mysqli_fetch_assoc($queryProduk)) {
            echo "<tr>
                    <td>{$prod['nama']}</td>
                    <td>{$prod['kategori']}</td>
                    <td>Rp " . number_format($prod['harga'], 0, ',', '.') . "</td>
                    <td>{$prod['stok']}</td>
                    <td>{$prod['expired_date']}</td>
                    <td>
                        <a href='edit_produk.php?id={$prod['id']}' class='btn btn-warning'>Edit</a>
                        <a href='hapus_produk.php?id={$prod['id']}' class='btn btn-danger' onclick='return confirm(\"Hapus produk ini?\");'>Hapus</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
        echo "<div style='text-align:center; margin-top:20px;'>
                <a href='tambah_produk.php' class='btn'>+ Tambah Produk</a>
              </div>";
    }

    // ================= HALAMAN TIDAK DITEMUKAN =================
    else {
        echo "<p>Halaman tidak ditemukan 🚫</p>";
    }
    ?>
    </div>

    </body>
    </html>
    <?php
}
// Jika bukan admin → tampilkan dashboard user biasa
else {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>User Dashboard</title>
        <link rel="stylesheet" href="style.css">
        <style>
            .dashboard-container {
                max-width: 800px;
                margin: 20px auto;
                padding: 30px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            
            .user-info {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 5px;
                margin-top: 20px;
            }
            
            .user-info p {
                margin: 10px 0;
                font-size: 16px;
            }
            
            .user-info strong {
                color: #659bf8ff;
            }
        </style>
    </head>
    <body>

    <div class="dashboard-container">
        <h2>Dashboard</h2>
        <p>Selamat datang di dashboard, <strong><?php echo $username; ?></strong>! 🎉</p>
        
        <div class="user-info">
            <h3>Informasi Pengguna</h3>
            <p><strong>Username:</strong> <?php echo $username; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>NIM:</strong> <?php echo $nim; ?></p>
            <p><strong>Role:</strong> <?php echo $role; ?></p>
        </div>

        <p style="text-align:center; margin-top:25px;">
            <a href="logout.php" style="color:red; font-weight:bold;">Logout</a>
        </p>

        <?php if ($conn): ?>
            <p style="color: green; margin-top: 20px;">✓ Koneksi database berhasil!</p>
        <?php endif; ?>
    </div>

    </body>
    </html>
    <?php
}
?>
