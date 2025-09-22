<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madrasah Al-Ihsan - Sistem CMS</title>
    <style>
        /* CSS sebelumnya tetap sama, tambahkan style baru di bawah ini */
        
        .login-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 50px auto;
        }
        
        .login-section h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1a5fb4;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .gallery-item {
            background: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .gallery-item p {
            padding: 10px;
            text-align: center;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
        }
        
        .pagination a {
            padding: 8px 15px;
            background: #1a5fb4;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .pagination a.active {
            background: #155294;
        }
        
        .search-form {
            margin-bottom: 20px;
        }
        
        .search-form input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            background: white;
            min-width: 150px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 4px;
            z-index: 1;
            right: 0;
        }
        
        .user-dropdown a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
        }
        
        .user-dropdown a:hover {
            background: #f5f5f5;
        }
        
        .user-menu:hover .user-dropdown {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSIjZmZmIiBkPSJNMjU2IDUxMmMtMTM4LjEgMC0yNTAtMTExLjktMjUwLTI1MFMxMTcuOSAxMiAyNTYgMTJzMjUwIDExMS45IDI1MCAyNTAtMTExLjkgMjUwLTI1MCAyNTB6bS0yNS0xMjVoNTBjNjkuMyAwIDEyNS01NS43IDEyNS0xMjVzLTU1LjctMTI1LTEyNS0xMjVoLTUwYy02OS4zIDAtMTI1IDU1LjctMTI1IDEyNXM1NS43IDEyNSAxMjUgMTI1eiIvPjwvc3ZnPg==" alt="Logo Madrasah">
                    <div>
                        <h1>Madrasah Al-Ihsan</h1>
                        <p>Mencerdaskan Generasi Islami</p>
                    </div>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="index.php?page=profil">Profil</a></li>
                        <li><a href="index.php?page=guru">Guru & Staf</a></li>
                        <li><a href="index.php?page=artikel">Artikel</a></li>
                        <li><a href="index.php?page=galeri">Galeri</a></li>
                        <li><a href="index.php?page=kontak">Kontak</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="user-menu">
                                <a href="#"><?php echo $_SESSION['username']; ?> ▼</a>
                                <div class="user-dropdown">
                                    <a href="admin.php">Dashboard Admin</a>
                                    <a href="logout.php">Logout</a>
                                </div>
                            </li>
                        <?php else: ?>
                            <li><a href="index.php?page=login">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php
        session_start();
        
        // Koneksi database
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "madrasah_db";
        
        $conn = mysqli_connect($host, $username, $password, $database);
        
        if (!$conn) {
            // Buat database dan tabel jika belum ada
            $conn = mysqli_connect($host, $username, $password);
            if ($conn) {
                $sql = "CREATE DATABASE IF NOT EXISTS $database";
                mysqli_query($conn, $sql);
                mysqli_select_db($conn, $database);
                
                // Tabel articles
                $sql = "CREATE TABLE IF NOT EXISTS articles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    content TEXT,
                    author VARCHAR(100),
                    image VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                mysqli_query($conn, $sql);
                
                // Tabel users
                $sql = "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(100),
                    role ENUM('admin','editor') DEFAULT 'editor',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                mysqli_query($conn, $sql);
                
                // Tabel gallery
                $sql = "CREATE TABLE IF NOT EXISTS gallery (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    image VARCHAR(255) NOT NULL,
                    description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                mysqli_query($conn, $sql);
                
                // Insert admin user (password: admin123)
                $sql = "INSERT IGNORE INTO users (username, password, email, role) VALUES 
                ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@madrasah.sch.id', 'admin')";
                mysqli_query($conn, $sql);
                
                // Contoh data
                $sql = "INSERT IGNORE INTO articles (title, content, author) VALUES 
                ('Selamat Datang di Website Madrasah Al-Ihsan', 'Madrasah Al-Ihsan adalah lembaga pendidikan Islam yang berkomitmen untuk mencetak generasi yang berakhlak mulia dan berprestasi.', 'Admin'),
                ('Kegiatan Pesantren Kilat Ramadan 2023', 'Dalam rangka menyambut bulan suci Ramadan, Madrasah Al-Ihsan mengadakan pesantren kilat untuk siswa-siswi.', 'Kepala Madrasah')";
                mysqli_query($conn, $sql);
            }
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
                } else {
                    $error = "Password salah!";
                }
            } else {
                $error = "Username tidak ditemukan!";
            }
        }
        
        // Ambil halaman
        $page = isset($_GET['page']) ? $_GET['page'] : 'beranda';
        ?>
        
        <?php if(isset($login_success)): ?>
            <div class="alert alert-success">
                Login berhasil! Selamat datang, <?php echo $_SESSION['username']; ?>.
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="main-content">
            <div class="content">
                <?php
                switch($page) {
                    case 'login':
                        echo '<div class="login-section">';
                        echo '<h2>Login Admin</h2>';
                        echo '<form method="post">';
                        echo '<div class="form-group"><label>Username:</label><input type="text" name="username" required></div>';
                        echo '<div class="form-group"><label>Password:</label><input type="password" name="password" required></div>';
                        echo '<button type="submit" name="login" class="btn">Login</button>';
                        echo '</form>';
                        echo '</div>';
                        break;
                        
                    case 'galeri':
                        echo "<h1>Galeri Kegiatan</h1>";
                        echo "<p>Berikut adalah dokumentasi kegiatan di Madrasah Al-Ihsan:</p>";
                        
                        $sql = "SELECT * FROM gallery ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="gallery-grid">';
                            while($row = mysqli_fetch_assoc($result)) {
                                echo '<div class="gallery-item">';
                                echo '<img src="' . ($row['image'] ?: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZTwvdGV4dD48L3N2Zz4=') . '" alt="' . $row['title'] . '">';
                                echo '<p><strong>' . $row['title'] . '</strong></p>';
                                if ($row['description']) {
                                    echo '<p style="font-size: 0.9em; color: #666;">' . $row['description'] . '</p>';
                                }
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<p>Belum ada gambar dalam galeri.</p>';
                        }
                        break;
                        
                    case 'artikel':
                        echo "<h1>Artikel Madrasah</h1>";
                        
                        // Pagination
                        $limit = 5;
                        $page_num = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
                        $offset = ($page_num - 1) * $limit;
                        
                        // Search
                        $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
                        $where = $search ? "WHERE title LIKE '%$search%' OR content LIKE '%$search%'" : "";
                        
                        echo '<div class="search-form">';
                        echo '<form method="get">';
                        echo '<input type="hidden" name="page" value="artikel">';
                        echo '<input type="text" name="search" placeholder="Cari artikel..." value="' . htmlspecialchars($search) . '">';
                        echo '<button type="submit" class="btn">Cari</button>';
                        echo '</form>';
                        echo '</div>';
                        
                        $sql = "SELECT * FROM articles $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<div class='article'>";
                                echo "<h2>" . $row['title'] . "</h2>";
                                echo "<div class='article-meta'>Ditulis oleh: " . $row['author'] . " pada " . $row['created_at'] . "</div>";
                                if ($row['image']) {
                                    echo '<img src="' . $row['image'] . '" alt="' . $row['title'] . '" style="max-width: 100%; height: auto; margin: 10px 0;">';
                                }
                                echo "<div class='article-content'>" . nl2br($row['content']) . "</div>";
                                echo "</div>";
                            }
                            
                            // Pagination links
                            $count_sql = "SELECT COUNT(*) as total FROM articles $where";
                            $count_result = mysqli_query($conn, $count_sql);
                            $total_rows = mysqli_fetch_assoc($count_result)['total'];
                            $total_pages = ceil($total_rows / $limit);
                            
                            if ($total_pages > 1) {
                                echo '<div class="pagination">';
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    $active = $i == $page_num ? 'active' : '';
                                    echo '<a href="index.php?page=artikel&p=' . $i . ($search ? '&search=' . urlencode($search) : '') . '" class="' . $active . '">' . $i . '</a>';
                                }
                                echo '</div>';
                            }
                        } else {
                            echo "<p>Belum ada artikel.</p>";
                        }
                        break;
                        
                    // Halaman lainnya tetap sama
                    default:
                        include 'pages/' . $page . '.php';
                        break;
                }
                ?>
            </div>
            
            <div class="sidebar">
                <!-- Sidebar content tetap sama -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <!-- Footer content tetap sama -->
    </footer>

    <script>
        // JavaScript tambahan
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview untuk form upload
            const imageInputs = document.querySelectorAll('input[type="file"]');
            imageInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('image-preview');
                            if (preview) {
                                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 200px; margin: 10px 0;">';
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            });
            
            // Confirm sebelum hapus
            const deleteButtons = document.querySelectorAll('a[onclick]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Yakin ingin menghapus?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>
