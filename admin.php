<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// Koneksi database
$host = "localhost";
$username = "root";
$password = "";
$database = "madrasah_db";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi upload gambar
function uploadImage($file) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $filename;
    
    // Validasi gambar
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["error" => "File bukan gambar"];
    }
    
    // Ukuran maksimal 2MB
    if ($file["size"] > 2000000) {
        return ["error" => "Ukuran file terlalu besar"];
    }
    
    // Format yang diizinkan
    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        return ["error" => "Hanya format JPG, JPEG, PNG & GIF yang diizinkan"];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => $target_file];
    } else {
        return ["error" => "Gagal upload gambar"];
    }
}

// Proses CRUD Artikel
if (isset($_POST['tambah_artikel'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadImage($_FILES['image']);
        if (isset($upload['success'])) {
            $image_path = $upload['success'];
        } else {
            $message = $upload['error'];
        }
    }
    
    if (!isset($message)) {
        $sql = "INSERT INTO articles (title, content, author, image) VALUES ('$title', '$content', '$author', '$image_path')";
        if (mysqli_query($conn, $sql)) {
            $message = "Artikel berhasil ditambahkan!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

if (isset($_POST['edit_artikel'])) {
    $id = $_POST['id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    
    $image_path = $_POST['existing_image'];
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadImage($_FILES['image']);
        if (isset($upload['success'])) {
            $image_path = $upload['success'];
        } else {
            $message = $upload['error'];
        }
    }
    
    if (!isset($message)) {
        $sql = "UPDATE articles SET title='$title', content='$content', author='$author', image='$image_path' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $message = "Artikel berhasil diupdate!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['hapus_artikel'])) {
    $id = $_GET['hapus_artikel'];
    $sql = "DELETE FROM articles WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $message = "Artikel berhasil dihapus!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Proses CRUD Galeri
if (isset($_POST['tambah_gambar'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    if (!empty($_FILES['image']['name'])) {
        $upload = uploadImage($_FILES['image']);
        if (isset($upload['success'])) {
            $image_path = $upload['success'];
            $sql = "INSERT INTO gallery (title, image, description) VALUES ('$title', '$image_path', '$description')";
            if (mysqli_query($conn, $sql)) {
                $message = "Gambar berhasil ditambahkan ke galeri!";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        } else {
            $message = $upload['error'];
        }
    } else {
        $message = "Pilih gambar terlebih dahulu!";
    }
}

if (isset($_GET['hapus_gambar'])) {
    $id = $_GET['hapus_gambar'];
    $sql = "DELETE FROM gallery WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $message = "Gambar berhasil dihapus dari galeri!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Ambil data
$articles = mysqli_query($conn, "SELECT * FROM articles ORDER BY created_at DESC");
$gallery = mysqli_query($conn, "SELECT * FROM gallery ORDER BY created_at DESC");

// Edit artikel
$edit_article = null;
if (isset($_GET['edit_artikel'])) {
    $id = $_GET['edit_artikel'];
    $result = mysqli_query($conn, "SELECT * FROM articles WHERE id = $id");
    $edit_article = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - CMS Madrasah</title>
    <style>
        /* CSS sebelumnya tetap, tambahkan style baru */
        
        .tab-container {
            margin-top: 20px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 2px solid #1a5fb4;
        }
        
        .tab {
            padding: 10px 20px;
            background: #f0f0f0;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        
        .tab.active {
            background: #1a5fb4;
            color: white;
        }
        
        .tab-content {
            display: none;
            padding: 20px;
            background: white;
            border-radius: 0 0 5px 5px;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1a5fb4;
        }
        
        .image-preview {
            margin: 10px 0;
        }
        
        .image-preview img {
            max-width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>CMS Madrasah Al-Ihsan</h1>
            <p>Panel Admin - Selamat datang, <?php echo $_SESSION['username']; ?>!</p>
        </div>
        
        <div class="admin-nav">
            <a href="index.php">Kembali ke Website</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Statistik -->
        <div class="stats-container">
            <?php
            $total_articles = mysqli_query($conn, "SELECT COUNT(*) as total FROM articles");
            $total_articles = mysqli_fetch_assoc($total_articles)['total'];
            
            $total_gallery = mysqli_query($conn, "SELECT COUNT(*) as total FROM gallery");
            $total_gallery = mysqli_fetch_assoc($total_gallery)['total'];
            
            $latest_article = mysqli_query($conn, "SELECT title FROM articles ORDER BY created_at DESC LIMIT 1");
            $latest_article = mysqli_num_rows($latest_article) > 0 ? mysqli_fetch_assoc($latest_article)['title'] : 'Belum ada';
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_articles; ?></div>
                <div>Total Artikel</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_gallery; ?></div>
                <div>Total Gambar</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users")); ?></div>
                <div>Total User</div>
            </div>
            
            <div class="stat-card">
                <div style="font-size: 0.9rem;">Artikel Terbaru:</div>
                <div style="font-weight: bold; margin-top: 5px;"><?php echo substr($latest_article, 0, 30) . (strlen($latest_article) > 30 ? '...' : ''); ?></div>
            </div>
        </div>
        
        <!-- Tab Interface -->
        <div class="tab-container">
            <div class="tabs">
                <button class="tab active" onclick="openTab('artikel')">Kelola Artikel</button>
                <button class="tab" onclick="openTab('galeri')">Kelola Galeri</button>
                <button class="tab" onclick="openTab('pengaturan')">Pengaturan</button>
            </div>
            
            <!-- Tab Artikel -->
            <div id="artikel" class="tab-content active">
                <h2><?php echo $edit_article ? 'Edit Artikel' : 'Tambah Artikel Baru'; ?></h2>
                
                <form method="post" enctype="multipart/form-data">
                    <?php if ($edit_article): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_article['id']; ?>">
                        <input type="hidden" name="existing_image" value="<?php echo $edit_article['image']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Judul Artikel</label>
                        <input type="text" name="title" value="<?php echo $edit_article ? $edit_article['title'] : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Artikel</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if ($edit_article && $edit_article['image']): ?>
                            <div class="image-preview">
                                <img src="<?php echo $edit_article['image']; ?>" alt="Preview">
                                <p>Gambar saat ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Isi Artikel</label>
                        <textarea name="content" rows="10" required><?php echo $edit_article ? $edit_article['content'] : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="author" value="<?php echo $edit_article ? $edit_article['author'] : ''; ?>" required>
                    </div>
                    
                    <button type="submit" name="<?php echo $edit_article ? 'edit_artikel' : 'tambah_artikel'; ?>" class="btn">
                        <?php echo $edit_article ? 'Update Artikel' : 'Tambah Artikel'; ?>
                    </button>
                    
                    <?php if ($edit_article): ?>
                        <a href="admin.php" class="btn">Batal</a>
                    <?php endif; ?>
                </form>
                
                <h2 style="margin-top: 40px;">Daftar Artikel</h2>
                <div class="article-list">
                    <?php if (mysqli_num_rows($articles) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($articles)): ?>
                            <div class="article-item">
                                <h3><?php echo $row['title']; ?></h3>
                                <p><strong>Penulis:</strong> <?php echo $row['author']; ?> | 
                                   <strong>Tanggal:</strong> <?php echo $row['created_at']; ?></p>
                                <?php if ($row['image']): ?>
                                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" style="max-width: 200px; margin: 10px 0;">
                                <?php endif; ?>
                                <p><?php echo substr(strip_tags($row['content']), 0, 150); ?>...</p>
                                <div class="article-actions">
                                    <a href="admin.php?edit_artikel=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                    <a href="admin.php?hapus_artikel=<?php echo $row['id']; ?>" class="btn btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus artikel ini?')">Hapus</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Belum ada artikel.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Tab Galeri -->
            <div id="galeri" class="tab-content">
                <h2>Tambah Gambar ke Galeri</h2>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Judul Gambar</label>
                        <input type="text" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar</label>
                        <input type="file" name="image" accept="image/*" required>
                        <div id="image-preview"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi (opsional)</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="tambah_gambar" class="btn">Tambah ke Galeri</button>
                </form>
                
                <h2 style="margin-top: 40px;">Daftar Gambar</h2>
                <div class="gallery-grid">
                    <?php if (mysqli_num_rows($gallery) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($gallery)): ?>
                            <div class="gallery-item">
                                <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                                <p><strong><?php echo $row['title']; ?></strong></p>
                                <?php if ($row['description']): ?>
                                    <p><?php echo $row['description']; ?></p>
                                <?php endif; ?>
                                <p><small><?php echo $row['created_at']; ?></small></p>
                                <a href="admin.php?hapus_gambar=<?php echo $row['id']; ?>" class="btn btn-danger" 
                                   onclick="return confirm('Yakin ingin menghapus gambar ini?')">Hapus</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Belum ada gambar dalam galeri.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Tab Pengaturan -->
            <div id="pengaturan" class="tab-content">
                <h2>Pengaturan Website</h2>
                
                <form method="post">
                    <div class="form-group">
                        <label>Nama Madrasah</label>
                        <input type="text" name="school_name" value="Madrasah Al-Ihsan" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi Singkat</label>
                        <textarea name="description" rows="3">Mencerdaskan Generasi Islami</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="address" value="Jl. Pendidikan No. 123, Jakarta" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="phone" value="(021) 1234567" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="info@madrasah-alihsan.sch.id" required>
                    </div>
                    
                    <button type="submit" name="save_settings" class="btn">Simpan Pengaturan</button>
                </form>
                
                <h2 style="margin-top: 40px;">Manajemen User</h2>
                <p>Fitur manajemen user akan tersedia dalam versi premium.</p>
            </div>
        </div>
    </div>

    <script>
        function openTab(tabName) {
            // Sembunyikan semua tab content
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Hapus active class dari semua tab
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Tampilkan tab yang dipilih
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        // Image preview untuk galeri
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').innerHTML = 
                        '<img src="' + e.target.result + '" style="max-width: 200px; margin: 10px 0;">';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
