<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "madrasah_db");

// Tambah artikel
if (isset($_POST['tambah_artikel'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    
    $sql = "INSERT INTO articles (title, content, author) VALUES ('$title', '$content', '$author')";
    mysqli_query($conn, $sql);
    $message = "Artikel berhasil ditambahkan!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 8px; }
        button { background: #1a5fb4; color: white; padding: 10px 20px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel - Madrasah Al-Ihsan</h1>
        
        <?php if(isset($message)): ?>
            <p style="color: green;"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <h2>Tambah Artikel Baru</h2>
        <form method="post">
            <div class="form-group">
                <label>Judul:</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Konten:</label>
                <textarea name="content" rows="10" required></textarea>
            </div>
            <div class="form-group">
                <label>Penulis:</label>
                <input type="text" name="author" required>
            </div>
            <button type="submit" name="tambah_artikel">Tambah Artikel</button>
        </form>
        
        <p><a href="index.php">Kembali ke Website</a> | <a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
