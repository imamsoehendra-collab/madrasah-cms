<?php
session_start();

// Koneksi database
$host = "localhost";
$username = "root";
$password = "";
$database = "madrasah_db";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Proses login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $login_success = true;
            header('Location: index.php');
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madrasah Al-Ihsan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f5f5; color: #333; line-height: 1.6; }
        .container { width: 90%; max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: linear-gradient(135deg, #1a5fb4 0%, #2d7bc4 100%); color: white; padding: 20px 0; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        nav ul { display: flex; list-style: none; }
        nav ul li { margin-left: 20px; }
        nav ul li a { color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; }
        .main-content { display: flex; margin: 20px 0; gap: 20px; }
        .content { flex: 3; background: white; padding: 20px; border-radius: 8px; }
        .sidebar { flex: 1; background: white; padding: 20px; border-radius: 8px; }
        .btn { background: #1a5fb4; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        footer { background: #333; color: white; padding: 30px 0; margin-top: 40px; }
        @media (max-width: 768px) { .main-content { flex-direction: column; } }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Madrasah Al-Ihsan</h1>
                    <p>Mencerdaskan Generasi Islami</p>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="index.php?page=profil">Profil</a></li>
                        <li><a href="index.php?page=artikel">Artikel</a></li>
                        <li><a href="index.php?page=galeri">Galeri</a></li>
                        <li><a href="index.php?page=kontak">Kontak</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="admin.php">Admin</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="index.php?page=login">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if(isset($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <div class="content">
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'beranda';
                
                switch($page) {
                    case 'login':
                        echo '<h2>Login Admin</h2>
                        <form method="post" style="max-width: 400px;">
                            <div style="margin-bottom: 15px;">
                                <label>Username:</label>
                                <input type="text" name="username" required style="width: 100%; padding: 8px;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label>Password:</label>
                                <input type="password" name="password" required style="width: 100%; padding: 8px;">
                            </div>
                            <button type="submit" name="login" class="btn">Login</button>
                        </form>';
                        break;
                        
                    case 'artikel':
                        echo "<h1>Artikel</h1>";
                        $sql = "SELECT * FROM articles ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<div style='margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;'>
                                    <h3>{$row['title']}</h3>
                                    <p><em>Oleh: {$row['author']} - {$row['created_at']}</em></p>
                                    <p>{$row['content']}</p>
                                </div>";
                            }
                        } else {
                            echo "<p>Belum ada artikel.</p>";
                        }
                        break;
                        
                    default:
                        echo "<h1>Selamat Datang</h1>
                        <p>Website Madrasah Al-Ihsan siap digunakan!</p>
                        <p>Silakan login untuk mengelola konten.</p>";
                        break;
                }
                ?>
            </div>
            
            <div class="sidebar">
                <h3>Informasi</h3>
                <p>Website Madrasah dengan CMS sederhana.</p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Madrasah Al-Ihsan. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
