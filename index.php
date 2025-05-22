<?php
session_start();

// Jika tombol logout ditekan
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

// Jika belum login, arahkan ke login.php
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Data user, misal nama/email diambil dari session
$username = $_SESSION['username'] ?? 'User';
$email = $_SESSION['email'] ?? '';

// Tampilkan halaman utama
?>
<!DOCTYPE html>
<html>
<head>
    <title>Halaman Utama</title>
</head>
<body>
    <h1>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <form method="get" action="">
        <button type="submit" name="logout" value="1">Logout</button>
    </form>
</body>
</html>