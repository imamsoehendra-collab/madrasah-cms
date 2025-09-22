<h1>Selamat Datang di Madrasah Al-Ihsan</h1>
<p>Madrasah Al-Ihsan adalah lembaga pendidikan Islam yang berkomitmen untuk mencetak generasi yang berakhlak mulia, berprestasi, dan bermanfaat bagi masyarakat.</p>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 30px 0;">
    <div style="background: #f0f8ff; padding: 20px; border-radius: 8px; text-align: center;">
        <h3>Visi</h3>
        <p>Menjadi lembaga pendidikan Islam unggulan yang menghasilkan generasi Qur'ani</p>
    </div>
    
    <div style="background: #fff0f5; padding: 20px; border-radius: 8px; text-align: center;">
        <h3>Misi</h3>
        <p>Menyelenggarakan pendidikan berkualitas berbasis nilai-nilai Islam</p>
    </div>
    
    <div style="background: #f0fff0; padding: 20px; border-radius: 8px; text-align: center;">
        <h3>Prestasi</h3>
        <p>Berbagai prestasi akademik dan non-akademik telah diraih</p>
    </div>
</div>

<h2>Artikel Terbaru</h2>
<?php
$sql = "SELECT * FROM articles ORDER BY created_at DESC LIMIT 3";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "<div class='article'>";
        echo "<h3>" . $row['title'] . "</h3>";
        echo "<div class='article-meta'>Ditulis oleh: " . $row['author'] . " pada " . $row['created_at'] . "</div>";
        if ($row['image']) {
            echo '<img src="' . $row['image'] . '" alt="' . $row['title'] . '" style="max-width: 100%; height: auto; margin: 10px 0;">';
        }
        echo "<div class='article-content'>" . substr(strip_tags($row['content']), 0, 200) . "...</div>";
        echo '<a href="index.php?page=artikel&p=1" class="btn">Baca Selengkapnya</a>';
        echo "</div>";
    }
} else {
    echo "<p>Belum ada artikel.</p>";
}
?>
