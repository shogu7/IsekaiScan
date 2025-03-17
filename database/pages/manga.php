<?php
// manga.php

$dotenv = parse_ini_file('/var/www/isekaiscan/.env');

$host = $dotenv['DB_HOST'];
$dbname = $dotenv['DB_NAME'];
$username = $dotenv['DB_USER'];
$password = $dotenv['DB_PASSWORD'];

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if (isset($_GET['name'])) {
    $mangaName = $_GET['name'];
} else {
    $uriParts = explode('/', $_SERVER['REQUEST_URI']);
    $fileName = end($uriParts);
    $mangaName = basename($fileName, '.php'); //
}

$mangaName = htmlspecialchars($mangaName);

$sql = "SELECT id, name, genre, chapter_count, short_description, long_description, cover_image, banner_image, first_chapter, last_chapter, alternate_title, author, small_image, series_tag 
        FROM mangas 
        WHERE LOWER(REPLACE(REPLACE(name, \"'\", ''), ' ', '-')) = :name 
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':name', $mangaName);
$stmt->execute();
$manga = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$manga) {
    die("Manga non trouvé.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($manga['name']); ?> - IsekaiScan</title>
    <link rel="stylesheet" href="/assets/css/pages/manga.css">
    <link rel="icon" href="/assets/images/icones/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include('../../templates/header.php'); ?>
    <main>
        <!-- Banner -->
        <section class="manga-banner" style="background-image: url('<?php echo htmlspecialchars($manga['banner_image']); ?>');">
            <div class="manga-title">
                <h1><?php echo htmlspecialchars($manga['name']); ?></h1>
            </div>
        </section>


        <!-- Manga Info -->
        <section class="manga-info-container">
            <div class="manga-cover">
                <img src="<?php echo htmlspecialchars($manga['cover_image']); ?>" alt="<?php echo htmlspecialchars($manga['name']); ?>">
                <button class="favorite-btn"><i class="fas fa-bookmark"></i> Favoris</button>
                <div class="manga-stats">
                    <span><?php echo htmlspecialchars($manga['chapter_count']); ?> chapitres</span>
                </div>
            </div>
            
            <div class="manga-details">
                <h3>Titre alternatif 🇰🇷 : </h3>
                <h2><?php echo htmlspecialchars($manga['alternate_title']); ?></h2> 
                
                <div class="synopsis">
                    <h3>Synopsis</h3>
                    <p><?php echo nl2br(htmlspecialchars($manga['long_description'])); ?></p>
                </div>
                
                <div class="info-grid">
                    <h3>Auteur</h3>
                    <p><?php echo nl2br(htmlspecialchars($manga['author'])); ?></p>
                    <div class="info-item">
                        <span class="info-value"><?php echo htmlspecialchars($manga['series_tag']); ?></span>
                    </div>
                </div>

                <div class="genres">
                    <h3>Genres</h3>
                    <div class="genre-tags">
                        <?php
                        $genres = explode(',', $manga['genre']);
                        foreach($genres as $genre): ?>
                            <span class="genre-tag"><?php echo htmlspecialchars(strtoupper(trim($genre))); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- Chapters  -->
        <section class="chapters-section">
            <h2 class="title">Chapitres de <?php echo htmlspecialchars($manga['name']); ?></h2>
            
            <div class="first-last-chapters">
                <a href="http://146.59.229.153/database/pages/chapter.php?manga=<?php echo urlencode($manga['name']); ?>&chapter=<?php echo $manga['first_chapter']; ?>" class="chapter-btn first-chapter">
                    <div class="chapter-btn-label">Premier Chapitre</div>
                    <div class="chapter-btn-title"><?php echo htmlspecialchars($manga['first_chapter']); ?></div>
                </a>

                <a href="http://146.59.229.153/database/pages/chapter.php?manga=<?php echo urlencode($manga['name']); ?>&chapter=<?php echo $manga['last_chapter']; ?>" class="chapter-btn first-chapter">
                    <div class="chapter-btn-label">Dernier Chapitre</div>
                    <div class="chapter-btn-title"><?php echo htmlspecialchars($manga['last_chapter']); ?></div>
                </a>
            </div>
        <div class="chapter-grid">
        <?php
            if ($manga['chapter_count'] > 0) {
                for ($chapter = $manga['first_chapter']; $chapter <= $manga['last_chapter']; $chapter++) {
                    ?>
                    <a href="http://146.59.229.153/database/pages/chapter.php?manga=<?php echo urlencode($manga['name']); ?>&chapter=<?php echo $chapter; ?>" class="chapter-btn">
                        <div class="chapter-title">Chapitre</div>
                        <div class="chapter-date"><?php echo htmlspecialchars($chapter); ?></div>
                    </a>
                    <?php
                }
            } else {
                echo '<p>No chapters available.</p>';
            }
            ?>
        </div>
<!-- FOOTER -->   
    <?php include('../../templates/footer.php'); ?>
<!-- JS SCRIPT --> 
    <script src="scripts.js"></script>
</body>
</html>