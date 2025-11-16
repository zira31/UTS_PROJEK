<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nim      = trim($_POST['nim']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']); // user atau admin

    // Validasi input kosong
    if (empty($username) || empty($nim) || empty($email) || empty($password) || empty($role)) {
        echo "<p style='color:red; text-align:center;'>⚠️ Semua kolom wajib diisi!</p>";
    } else {
        // Cek apakah email atau NIM sudah digunakan
        $check = $conn->prepare("SELECT email, nim FROM users WHERE email = ? OR nim = ?");
        $check->bind_param("ss", $email, $nim);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $existing = $result->fetch_assoc();
            if ($existing['email'] === $email) {
                echo "<p style='color:red; text-align:center;'>❌ Email sudah terdaftar! Gunakan email lain.</p>";
            } elseif ($existing['nim'] === $nim) {
                echo "<p style='color:red; text-align:center;'>❌ NIM sudah terdaftar! Gunakan NIM lain.</p>";
            }
        } else {
            // Enkripsi password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan ke database
            $sql = "INSERT INTO users (username, nim, email, password, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // Cek apakah prepare berhasil
            if ($stmt === false) {
                die("Error: " . $conn->error);
            }

            // Bind 5 parameter
            $stmt->bind_param("sssss", $username, $nim, $email, $hashed_password, $role);

            // Execute
            if ($stmt->execute()) {
                echo "<p style='color:green; text-align:center;'>✅ Registrasi berhasil! Anda terdaftar sebagai <strong>$role</strong>.</p>";
                echo "<meta http-equiv='refresh' content='2;url=index.php?page=login'>";
            } else {
                echo "<p style='color:red; text-align:center;'>❌ Terjadi kesalahan: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
        $check->close();
    }
}
$conn->close();
?>

<!-- Tampilan Form -->
<div class="register-wrapper">
    <h2>Form Registrasi</h2>

    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" placeholder="Masukkan username" required>

        <label for="nim">NIM:</label>
        <input type="text" name="nim" id="nim" placeholder="Masukkan NIM" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Masukkan email" required> 

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Masukkan password" required>

        <label for="role">Daftar sebagai:</label>
        <select name="role" id="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="index.php?page=login">Login di sini</a></p>
</div>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f3f4f6;
    margin: 0;
    padding: 0;
}

.register-wrapper {
    width: 100%;
    max-width: 500px;
    margin: 40px auto;
    background: #fff;
    padding: 40px 50px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

form label {
    font-weight: bold;
    color: #444;
    display: block;
    margin-bottom: 5px;
}

input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-sizing: border-box;
    font-size: 14px;
}

input:focus, select:focus {
    border-color: #659bf8ff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(101, 155, 248, 0.1);
}

button {
    width: 100%;
    background-color: #659bf8ff;
    color: white;
    padding: 14px 0;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background-color: #5589e0;
}

p {
    text-align: center;
    font-size: 14px;
}

a {
    color: #659bf8ff;
    font-weight: bold;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
