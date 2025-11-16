<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .navbar { background-color: #007bff; padding: 15px; text-align: center; }
        .navbar a { color: white; text-decoration: none; font-weight: bold; margin: 0 15px; }
        .navbar a:hover { text-decoration: underline; }
        .content { text-align: center; margin-top: 40px; }

        h2 { color: #333; margin-top: 10px; margin-bottom: 10px; }
        .logout-btn { background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .logout-btn:hover { background-color: #b02a37; }

        .card { background: white; width: 400px; margin: 40px auto; padding: 20px; border-radius: 10px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); }

        table { border-collapse: collapse; margin: 20px auto; width: 90%; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        table th, table td { border: none; padding: 10px 15px; text-align: center; }
        table th { background-color: #007bff; color: white; }
        table tr:nth-child(even) { background-color: #f9f9f9; }

        a.action-link { font-weight: bold; text-decoration: none; margin: 0 5px; }
        a.edit { color: #007bff; }
        a.delete { color: #dc3545; }
        a.edit:hover, a.delete:hover { text-decoration: underline; }

        .btn { background-color: #007BFF; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-warning { background:#ffc107; color:black; }
        .btn-danger { background:#dc3545; }
        .btn-success { background:#28a745; }
        .btn-secondary { background:#6c757d; }
        .btn:hover { opacity:0.9; }

        .form-container { background: white; max-width: 500px; margin: 20px auto; padding: 30px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); text-align: left; }
        .form-container label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-container input, .form-container select, .form-container textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .form-container button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .form-container button:hover { background: #0056b3; }
        
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>

   <div class="navbar">
    <?php if (isset($_SESSION['user'])): ?>
        <a href="index.php?page=home">Home</a>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="index.php?page=users">Master User</a>
            <a href="index.php?page=product">Product</a>
            <a href="index.php?page=supplier">Supplier</a>
            <a href="index.php?page=transaction">Transaction</a>
            <a href="index.php?page=detail">Detail</a>
        <?php else: ?>
            <a href="index.php?page=product">Product</a>
            <a href="index.php?page=transaction">Transaction</a>
            <a href="index.php?page=detail">Detail</a>
        <?php endif; ?>

        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="index.php?page=login">Login</a>
        <a href="index.php?page=register">Register</a>
    <?php endif; ?>
</div>

    <div class="content">
        <?php
        // Proteksi halaman (wajib login)
        if (!isset($_SESSION['user']) && isset($_GET['page']) && !in_array($_GET['page'], ['login', 'register'])) {
            echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
            exit;
        }

        // Routing halaman
        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            switch ($page) {
                case 'dashboard':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak! Hanya admin yang bisa mengakses dashboard.'); window.location='index.php?page=home';</script>";
                        exit;
                    }

                    echo "<h2> Admin Dashboard</h2>";
                    echo "<p>Selamat datang kembali, <b>" . htmlspecialchars($_SESSION['user']['username']) . "</b> 👑</p>";
                    echo "<hr style='width:60%; margin:auto; margin-top:15px; margin-bottom:25px;'>";
                    echo "<h3>Data Pengguna</h3>";
                    
                    $queryUser = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
                    if (mysqli_num_rows($queryUser) > 0) {
                        echo "<table><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
                        while ($u = mysqli_fetch_assoc($queryUser)) {
                            echo "<tr><td>{$u['id']}</td><td>{$u['username']}</td><td>{$u['email']}</td><td>{$u['role']}</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>Tidak ada data user.</p>";
                    }
                    break;

                case 'product':
                    if (!isset($_SESSION['user'])) {
                        echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
                        exit;
                    }
                    
                    $isAdmin = ($_SESSION['user']['role'] === 'admin');
                    
                    echo "<h2> " . ($isAdmin ? "Master Product" : "Daftar Product") . "</h2>";
                    
                    if (!$isAdmin) {
                        echo "<p style='color:#666; font-size:14px;'>Anda login sebagai <b>User</b> - Mode tampilan saja</p>";
                    }

                    $queryProduk = mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC");

                    if (!$queryProduk) {
                        echo "<p style='color:red;'> Tabel <b>products</b> belum tersedia di database.</p>";
                        echo "<pre>" . mysqli_error($conn) . "</pre>";
                        break;
                    }

                    if (mysqli_num_rows($queryProduk) > 0) {
                        echo "<table><tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Expired Date</th>";
                        
                        if ($isAdmin) {
                            echo "<th>Aksi</th>";
                        }
                        echo "</tr>";

                        while ($p = mysqli_fetch_assoc($queryProduk)) {
                            $stokBadge = '';
                            if ($p['stok'] == 0) {
                                $stokBadge = " <span class='badge badge-danger'>Habis</span>";
                            } elseif ($p['stok'] < 10) {
                                $stokBadge = " <span class='badge badge-warning'>Menipis</span>";
                            } else {
                                $stokBadge = " <span class='badge badge-success'>Tersedia</span>";
                            }
                            
                            echo "<tr>
                                    <td>{$p['id']}</td>
                                    <td>{$p['nama']}</td>
                                    <td>{$p['kategori']}</td>
                                    <td>Rp " . number_format($p['harga'], 0, '.', ',') . "</td>
                                    <td>{$p['stok']}{$stokBadge}</td>
                                    <td>{$p['expired_date']}</td>";
                            
                            if ($isAdmin) {
                                echo "<td>
                                        <a href='index.php?page=edit_product&id={$p['id']}' class='btn btn-warning'>Edit</a>
                                        <a href='index.php?page=hapus_product&id={$p['id']}' class='btn btn-danger'
                                           onclick='return confirm(\"Yakin ingin menghapus produk ini?\");'>Hapus</a>
                                      </td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>Belum ada data produk.</p>";
                    }

                    if ($isAdmin) {
                        echo "<div style='text-align:center; margin-top:20px;'>
                                <a href='index.php?page=tambah_product' class='btn btn-success'>+ Tambah Produk</a>
                              </div>";
                    }
                    break;

                case 'tambah_product':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=product';</script>";
                        exit;
                    }

                    if (isset($_POST['submit'])) {
                        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
                        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
                        $harga = mysqli_real_escape_string($conn, $_POST['harga']);
                        $stok = mysqli_real_escape_string($conn, $_POST['stok']);
                        $expired_date = mysqli_real_escape_string($conn, $_POST['expired_date']);
                        
                        $query = mysqli_query($conn, "INSERT INTO products (nama, kategori, harga, stok, expired_date)
                                                      VALUES ('$nama', '$kategori', '$harga', '$stok', '$expired_date')");
                        
                        if ($query) {
                            echo "<script>alert('Produk berhasil ditambahkan!'); window.location='index.php?page=product';</script>";
                        } else {
                            echo "<script>alert('Gagal menambahkan produk!');</script>";
                        }
                    }
                    ?>
                    <div class="form-container">
                        <h2>➕ Tambah Produk Baru</h2>
                        <form method="POST">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" required>
                            
                            <label>Kategori</label>
                            <input type="text" name="kategori" required>
                            
                            <label>Harga</label>
                            <input type="number" name="harga" min="0" required>
                            
                            <label>Stok</label>
                            <input type="number" name="stok" min="0" required>
                            
                            <label>Expired Date</label>
                            <input type="date" name="expired_date" required>
                            
                            <button type="submit" name="submit">Tambah Produk</button>
                        </form>
                        <div style="text-align:center; margin-top:15px;">
                            <a href="index.php?page=product" class="btn btn-secondary">← Kembali</a>
                        </div>
                    </div>
                    <?php
                    break;

                case 'edit_product':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=product';</script>";
                        exit;
                    }

                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    $queryEdit = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
                    
                    if (!$queryEdit || mysqli_num_rows($queryEdit) == 0) {
                        echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php?page=product';</script>";
                        exit;
                    }
                    
                    $prod = mysqli_fetch_assoc($queryEdit);

                    if (isset($_POST['update'])) {
                        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
                        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
                        $harga = mysqli_real_escape_string($conn, $_POST['harga']);
                        $stok = mysqli_real_escape_string($conn, $_POST['stok']);
                        $expired_date = mysqli_real_escape_string($conn, $_POST['expired_date']);
                        
                        
                        $queryUpdate = mysqli_query($conn, "UPDATE products SET 
                                                            nama = '$nama',
                                                            kategori = '$kategori',
                                                            harga = '$harga',
                                                            stok = '$stok',
                                                            expired_date = '$expired_date'
                                                            WHERE id = $id");
                        
                        if ($queryUpdate) {
                            echo "<script>alert('Produk berhasil diupdate!'); window.location='index.php?page=product';</script>";
                        } else {
                            echo "<script>alert('Gagal mengupdate produk!');</script>";
                        }
                    }
                    ?>
                    <div class="form-container">
                        <h2> Edit Produk</h2>
                        <form method="POST">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" value="<?php echo htmlspecialchars($prod['nama']); ?>" required>
                            
                            <label>Kategori</label>
                            <input type="text" name="kategori" value="<?php echo htmlspecialchars($prod['kategori']); ?>" required>
                            
                            <label>Harga</label>
                            <input type="number" name="harga" value="<?php echo $prod['harga']; ?>" min="0" required>
                            
                            <label>Stok</label>
                            <input type="number" name="stok" value="<?php echo $prod['stok']; ?>" min="0" required>
                            
                            <label>Expired Date</label>
                            <input type="date" name="expired_date" value="<?php echo $prod['expired_date']; ?>" required>
                            
                            <button type="submit" name="update">Update Produk</button>
                        </form>
                        <div style="text-align:center; margin-top:15px;">
                            <a href="index.php?page=product" class="btn btn-secondary">← Kembali</a>
                        </div>
                    </div>
                    <?php
                    break;

                case 'hapus_product':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=product';</script>";
                        exit;
                    }

                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    $queryHapus = mysqli_query($conn, "DELETE FROM products WHERE id = $id");
                    
                    if ($queryHapus) {
                        echo "<script>alert('Produk berhasil dihapus!'); window.location='index.php?page=product';</script>";
                    } else {
                        echo "<script>alert('Gagal menghapus produk!');</script>";
                    }
                    break;

                case 'supplier':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=home';</script>";
                        exit;
                    }
                    
                    echo "<h2> Kelola Supplier</h2>";
                    
                    $querySupp = mysqli_query($conn, "SELECT * FROM supplier ORDER BY id_supplier ASC");

                    if (!$querySupp) {
                        echo "<p style='color:red;'>Tabel supplier belum dibuat. Silakan buat tabel terlebih dahulu.</p>";
                        echo "<div style='text-align:center; margin-top:20px;'>
                                <a href='index.php?page=home' class='btn btn-secondary'>← Kembali ke Home</a>
                              </div>";
                        break;
                    }

                    if (mysqli_num_rows($querySupp) > 0) {
                        echo "<table>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>Telepon</th>
                                    <th>Aksi</th>
                                </tr>";

                        while ($supp = mysqli_fetch_assoc($querySupp)) {
                            echo "<tr>
                                    <td>{$supp['id_supplier']}</td>
                                    <td>" . htmlspecialchars($supp['nama_supplier']) . "</td>
                                    <td>" . htmlspecialchars($supp['alamat']) . "</td>
                                    <td>" . htmlspecialchars($supp['telepon']) . "</td>
                                    <td>
                                        <a href='index.php?page=edit_supplier&id={$supp['id_supplier']}' class='btn btn-warning'>Edit</a>
                                        <a href='index.php?page=hapus_supplier&id={$supp['id_supplier']}' class='btn btn-danger'
                                           onclick='return confirm(\"Yakin ingin menghapus supplier ini?\");'>Hapus</a>
                                    </td>
                                  </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p style='color:#666; font-size:16px; margin:30px 0;'> Belum ada data supplier</p>";
                    }

                    echo "<div style='text-align:center; margin-top:20px;'>
                            <a href='index.php?page=tambah_supplier' class='btn btn-success'>+ Tambah Supplier</a>
                          </div>";
                    break;

                case 'tambah_supplier':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=home';</script>";
                        exit;
                    }

                    if (isset($_POST['submit'])) {
                        $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
                        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
                        $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
                        
                        $query = mysqli_query($conn, "INSERT INTO supplier (nama_supplier, alamat, telepon)
                                                      VALUES ('$nama_supplier', '$alamat', '$telepon')");
                        
                        if ($query) {
                            echo "<script>alert('Supplier berhasil ditambahkan!'); window.location='index.php?page=supplier';</script>";
                        } else {
                            echo "<script>alert('Gagal menambahkan supplier!');</script>";
                        }
                    }
                    ?>
                    <div class="form-container">
                        <h2>➕ Tambah Supplier Baru</h2>
                        <form method="POST">
                            <label>Nama Supplier</label>
                            <input type="text" name="nama_supplier" required>
                            
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" required></textarea>
                            
                            <label>Telepon</label>
                            <input type="text" name="telepon" required>
                            
                            <button type="submit" name="submit">Tambah Supplier</button>
                        </form>
                        <div style="text-align:center; margin-top:15px;">
                            <a href="index.php?page=supplier" class="btn btn-secondary">← Kembali</a>
                        </div>
                    </div>
                    <?php
                    break;

                case 'edit_supplier':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=home';</script>";
                        exit;
                    }

                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    $queryEdit = mysqli_query($conn, "SELECT * FROM supplier WHERE id_supplier = $id");
                    
                    if (!$queryEdit || mysqli_num_rows($queryEdit) == 0) {
                        echo "<script>alert('Supplier tidak ditemukan!'); window.location='index.php?page=supplier';</script>";
                        exit;
                    }
                    
                    $supp = mysqli_fetch_assoc($queryEdit);

                    if (isset($_POST['update'])) {
                        $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
                        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
                        $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
                        
                        $queryUpdate = mysqli_query($conn, "UPDATE supplier SET 
                                                            nama_supplier = '$nama_supplier',
                                                            alamat = '$alamat',
                                                            telepon = '$telepon'
                                                            WHERE id_supplier = $id");
                        
                        if ($queryUpdate) {
                            echo "<script>alert('Supplier berhasil diupdate!'); window.location='index.php?page=supplier';</script>";
                        } else {
                            echo "<script>alert('Gagal mengupdate supplier!');</script>";
                        }
                    }
                    ?>
                    <div class="form-container">
                        <h2>✏️ Edit Supplier</h2>
                        <form method="POST">
                            <label>Nama Supplier</label>
                            <input type="text" name="nama_supplier" value="<?php echo htmlspecialchars($supp['nama_supplier']); ?>" required>
                            
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" required><?php echo htmlspecialchars($supp['alamat']); ?></textarea>
                            
                            <label>Telepon</label>
                            <input type="text" name="telepon" value="<?php echo htmlspecialchars($supp['telepon']); ?>" required>
                            
                            <button type="submit" name="update">Update Supplier</button>
                        </form>
                        <div style="text-align:center; margin-top:15px;">
                            <a href="index.php?page=supplier" class="btn btn-secondary">← Kembali</a>
                        </div>
                    </div>
                    <?php
                    break;

                case 'hapus_supplier':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=home';</script>";
                        exit;
                    }

                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    $queryHapus = mysqli_query($conn, "DELETE FROM supplier WHERE id_supplier = $id");
                    
                    if ($queryHapus) {
                        echo "<script>alert('Supplier berhasil dihapus!'); window.location='index.php?page=supplier';</script>";
                    } else {
                        echo "<script>alert('Gagal menghapus supplier!');</script>";
                    }
                    break;

                case 'pelanggan':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=home';</script>";
                        exit;
                    }
                    echo "<h2> Master Pelanggan</h2>";
                    echo "<p>Halaman untuk mengelola data pelanggan (Coming Soon)</p>";
                    break;

                // GANTI case 'transaksi' menjadi case 'transaction'
case 'transaction':
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
        exit;
    }
    
    $isAdmin = ($_SESSION['user']['role'] === 'admin');
    $userId = $_SESSION['user']['id'];
    
    echo "<h2> " . ($isAdmin ? "All Transactions" : "My Transaction History") . "</h2>";
    
    if (!$isAdmin) {
        echo "<p style='color:#666; font-size:14px;'>Showing your transactions</p>";
    }
    
    // Cek jika ada detail transaksi
    if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
        $transaction_id = (int)$_GET['id'];
        
        // Query berbeda untuk admin dan user
        if ($isAdmin) {
            $header_query = "SELECT t.*, u.username 
                             FROM transactions t
                             JOIN users u ON t.user_id = u.id
                             WHERE t.transaction_id = $transaction_id";
        } else {
            // User hanya bisa lihat transaksi sendiri
            $header_query = "SELECT t.*, u.username 
                             FROM transactions t
                             JOIN users u ON t.user_id = u.id
                             WHERE t.transaction_id = $transaction_id 
                             AND t.user_id = $userId";
        }
        
        $header_result = mysqli_query($conn, $header_query);
        
        if ($header_result && mysqli_num_rows($header_result) > 0) {
            $header = mysqli_fetch_assoc($header_result);
            
            echo "<div class='card' style='width: auto; max-width: 700px;'>";
            echo "<h3> Transaction Detail #" . htmlspecialchars($header['transaction_id']) . "</h3>";
            
            if ($isAdmin) {
                echo "<p><strong>User:</strong> " . htmlspecialchars($header['username']) . "</p>";
            }
            
            echo "<p><strong>Date:</strong> " . htmlspecialchars(date('d M Y H:i', strtotime($header['transaction_date']))) . "</p>";
            echo "<p><strong>Status:</strong> <span class='badge badge-";
            
            // Badge warna sesuai status
            if ($header['status'] == 'completed') {
                echo "success'>Completed";
            } elseif ($header['status'] == 'pending') {
                echo "warning'>Pending";
            } elseif ($header['status'] == 'cancelled') {
                echo "danger'>Cancelled";
            } else {
                echo "secondary'>" . htmlspecialchars($header['status']);
            }
            echo "</span></p>";
            
            echo "<p><strong>Total:</strong> <span style='font-size:18px; color:#28a745; font-weight:bold;'>Rp " . number_format($header['total_amount'], 0, ',', '.') . "</span></p>";
            
            // Tampilkan detail item transaksi jika ada tabel transaction_details
            $detail_query = "SELECT td.*, p.nama as product_name 
                            FROM transaction_details td
                            JOIN products p ON td.product_id = p.id
                            WHERE td.transaction_id = $transaction_id";
            $detail_result = mysqli_query($conn, $detail_query);
            
            if ($detail_result && mysqli_num_rows($detail_result) > 0) {
                echo "<hr>";
                echo "<h4>Transaction Items:</h4>";
                echo "<table style='width:100%;'>";
                echo "<tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>";
                
                while ($item = mysqli_fetch_assoc($detail_result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['product_name']) . "</td>";
                    echo "<td>" . $item['quantity'] . "</td>";
                    echo "<td>Rp " . number_format($item['price'], 0, ',', '.') . "</td>";
                    echo "<td>Rp " . number_format($item['subtotal'], 0, ',', '.') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            echo "<p style='margin-top:20px;'><a href='index.php?page=transaction' class='btn btn-secondary'>← Back</a></p>";
            echo "</div>";
        } else {
            echo "<p style='color:red;'> Transaction not found or you don't have access.</p>";
            echo "<p><a href='index.php?page=transaction' class='btn btn-secondary'>← Back</a></p>";
        }
    } else {
        // Tampilkan daftar transaksi
        if ($isAdmin) {
            // Admin melihat semua transaksi
            $query = "SELECT t.transaction_id, t.transaction_date, t.total_amount, t.status, u.username 
                      FROM transactions t
                      JOIN users u ON t.user_id = u.id
                      ORDER BY t.transaction_date DESC";
        } else {
            // User hanya melihat transaksi sendiri
            $query = "SELECT t.transaction_id, t.transaction_date, t.total_amount, t.status, u.username 
                      FROM transactions t
                      JOIN users u ON t.user_id = u.id
                      WHERE t.user_id = $userId
                      ORDER BY t.transaction_date DESC";
        }
        
        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "<p style='color:red;'> Transaction table not created yet.</p>";
            echo "<pre>Error: " . mysqli_error($conn) . "</pre>";
        } elseif (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Date</th>";
            
            if ($isAdmin) {
                echo "<th>User</th>";
            }
            
            echo "<th>Total</th><th>Status</th><th>Action</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['transaction_id']) . "</td>";
                echo "<td>" . htmlspecialchars(date('d M Y H:i', strtotime($row['transaction_date']))) . "</td>";
                
                if ($isAdmin) {
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                }
                
                echo "<td>Rp " . number_format($row['total_amount'], 0, ',', '.') . "</td>";
                echo "<td>";
                
                // Badge status
                if ($row['status'] == 'completed') {
                    echo "<span class='badge badge-success'>Completed</span>";
                } elseif ($row['status'] == 'pending') {
                    echo "<span class='badge badge-warning'>Pending</span>";
                } elseif ($row['status'] == 'cancelled') {
                    echo "<span class='badge badge-danger'>Cancelled</span>";
                } else {
                    echo "<span class='badge badge-secondary'>" . htmlspecialchars($row['status']) . "</span>";
                }
                
                echo "</td>";
                echo "<td><a href='index.php?page=transaction&action=view&id=" . $row['transaction_id'] . "' class='btn btn-warning'>View</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            if ($isAdmin) {
                echo "<p style='color:#666; font-size:16px; margin:30px 0;'> No transactions yet</p>";
            } else {
                echo "<p style='color:#666; font-size:16px; margin:30px 0;'> You don't have any transactions yet</p>";
                echo "<p><a href='index.php?page=product' class='btn btn-success'> Start Shopping</a></p>";
            }
        }
    }
    break;

// TAMBAHKAN case 'detail' - untuk halaman detail user/info
case 'detail':
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php?page=login';</script>";
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $isAdmin = ($_SESSION['user']['role'] === 'admin');
    
    // Jika admin dan ada parameter user_id, tampilkan detail user tersebut
    if ($isAdmin && isset($_GET['user_id'])) {
        $viewUserId = (int)$_GET['user_id'];
        $queryUser = mysqli_query($conn, "SELECT * FROM users WHERE id = $viewUserId");
    } else {
        // Tampilkan detail user sendiri
        $queryUser = mysqli_query($conn, "SELECT * FROM users WHERE id = $userId");
    }
    
    $user = mysqli_fetch_assoc($queryUser);
    
    if (!$user) {
        echo "<p style='color:red;'>User not found!</p>";
        break;
    }
    
    echo "<h2> User Detail</h2>";
    
    // Card Info User
    echo "<div class='card' style='max-width:600px; text-align:left;'>";
    echo "<h3> Account Information</h3>";
    echo "<table style='width:100%; border:none; box-shadow:none;'>";
    echo "<tr style='background:transparent;'><td style='border:none; padding:8px; font-weight:bold; width:150px;'>Username:</td><td style='border:none; padding:8px;'>" . htmlspecialchars($user['username']) . "</td></tr>";
    echo "<tr style='background:transparent;'><td style='border:none; padding:8px; font-weight:bold;'>Email:</td><td style='border:none; padding:8px;'>" . htmlspecialchars($user['email']) . "</td></tr>";
    echo "<tr style='background:transparent;'><td style='border:none; padding:8px; font-weight:bold;'>Role:</td><td style='border:none; padding:8px;'><span class='badge badge-" . ($user['role'] == 'admin' ? 'danger' : 'success') . "'>" . htmlspecialchars($user['role']) . "</span></td></tr>";
    echo "<tr style='background:transparent;'><td style='border:none; padding:8px; font-weight:bold;'>Joined:</td><td style='border:none; padding:8px;'>" . (isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-') . "</td></tr>";
    echo "</table>";
    echo "</div>";
    
    // Statistik Transaksi
    $viewUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $userId;
    $queryStats = mysqli_query($conn, "SELECT 
                                        COUNT(*) as total_transactions,
                                        SUM(total_amount) as total_spending
                                        FROM transactions 
                                        WHERE user_id = $viewUserId AND status = 'completed'");
    
    if ($queryStats) {
        $stats = mysqli_fetch_assoc($queryStats);
        
        echo "<div class='card' style='max-width:600px; margin-top:20px;'>";
        echo "<h3>  Shopping Statistics</h3>";
        echo "<div style='display:flex; justify-content:space-around; text-align:center;'>";
        echo "<div><h2 style='color:#007bff; margin:5px;'>" . ($stats['total_transactions'] ?: 0) . "</h2><p style='color:#666; margin:0;'>Transactions</p></div>";
        echo "<div><h2 style='color:#28a745; margin:5px;'>Rp " . number_format($stats['total_spending'] ?: 0, 0, ',', '.') . "</h2><p style='color:#666; margin:0;'>Total Spending</p></div>";
        echo "</div>";
        echo "</div>";
    }
    
    // Tombol kembali
    echo "<div style='text-align:center; margin-top:20px;'>";
    if ($isAdmin && isset($_GET['user_id'])) {
        echo "<a href='index.php?page=users' class='btn btn-secondary'>← Back to Users</a>";
    }
    echo "</div>";
    break;

                case 'users':
                    if ($_SESSION['user']['role'] !== 'admin') {
                        echo "<script>alert('Akses ditolak!'); window.location='index.php?page=home';</script>";
                        exit;
                    }
                    echo "<h2>Master User</h2>";
                    include 'users.php';
                    break;

                case 'home':
                    echo "<div class='card'>";
                    echo "<h2>Selamat datang di Home</h2>";
                    echo "<p>Halo, <b>" . htmlspecialchars($_SESSION['user']['username']) . "</b>!</p>";
                    echo "<p>Anda login sebagai <b>" . htmlspecialchars($_SESSION['user']['role']) . "</b>.</p>";
                    echo "</div>";
                    break;

                case 'login':
                    include 'login.php';
                    break;

                case 'register':
                    include 'register.php';
                    break;

                default:
                    echo "<h2>Halaman masih Proses ya!</h2>";
            }
        } else {
            // Halaman default jika tidak ada parameter page
            if (isset($_SESSION['user'])) {
                echo "<div class='card'>";
                echo "<h2>Selamat datang, <b>" . htmlspecialchars($_SESSION['user']['username']) . "</b>!</h2>";
                echo "<p>Anda login sebagai <b>" . htmlspecialchars($_SESSION['user']['role']) . "</b>.</p>";
                echo "</div>";
            } else {
                echo "<div class='card'>";
                echo "<h2>Selamat datang di Sistem Login Multiuser</h2>";
                echo "<p>Silakan login untuk melanjutkan.</p>";
                echo "</div>";
            }
        }
        ?>
    </div>

</body>
</html>