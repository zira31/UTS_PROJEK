<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Pastikan $stmt sudah di-prepare sebelumnya
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");

// Cek apakah prepare berhasil
if ($stmt === false) {
    die("Error: " . $conn->error);
}

// Sekarang baru bind_param
$stmt->bind_param("s", $email);

// Execute
$stmt->execute();

// Get result
$result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["user"] = [
                "id" => $user["id"],
                "username" => $user["username"],
                "email" => $user["email"],
                "role" => $user["role"]
            ];

            echo "<p style='color:green; text-align:center;'>Login berhasil! Mengarahkan...</p>";
            // Arahkan ke index
            header("refresh:1; url=index.php");
            exit;
        } else {
            echo "<p style='color:red; text-align:center;'>Password salah!</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>Email tidak ditemukan!</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!-- Tampilan Form Login -->
<div class="login-container">
    <h2>Login</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>

    <p>Belum punya akun? <a href="index.php?page=register">Daftar Sekarang</a></p>
</div>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f6fa;
}
.login-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 25px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.login-container h2 {
    text-align: center;
    color: #333;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    font-weight: bold;
    color: #555;
}
input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
}
button {
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background: #0056b3;
}
p {
    text-align: center;
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
