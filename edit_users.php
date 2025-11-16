<?php
include 'koneksi.php';
$id = $_GET['id'] ?? 0;
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    mysqli_query($conn, "UPDATE users SET username='$username', email='$email' WHERE id='$id'");
    header("Location: index.php?page=dashboard");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit User</title></head>
<body>
<h2>Edit User</h2>
<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>"><br><br>
    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br><br>
    <button type="submit">Simpan</button>
</form>
</body>
</html>