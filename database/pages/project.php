<?php
// project.php

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

$sql = "SELECT id, name, genre, chapter_count, short_description, long_description, cover_image, banner_image, small_image, series_tag 
        FROM mangas 
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$mangas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr"> 
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>List de Projet - IsekaiScan</title>
  <link rel="stylesheet" href="/assets/css/pages/project.css">
  <link rel="icon" href="/assets/images/icones/favicon.ico" type="image/x-icon">
</head>
<body>
  <?php include('../../templates/header.php'); ?>
  <div class="manga-container">
    <div class="filters-bar">
      <div class="filter-dropdown">
        <select name="genre" id="genre-filter">
          <option value="">Genre</option>
          <option value="all">Tout</option>
          <option value="action">Action</option>
          <option value="adventure">Aventure</option>
          <option value="fantasy">Fantaisie</option>
          <option value="isekai">Isekai</option>
          <option value="romance">Romance</option>
        </select>
      </div>
      
      <div class="filter-dropdown">
        <select name="status" id="status-filter">
          <option value="">Status</option>
          <option value="all">Tout</option>
          <option value="ongoing">En cours</option>
          <option value="completed">Terminé</option>
          <option value="hiatus">En pause</option>
        </select>
      </div>
      
      <div class="filter-dropdown">
        <select name="type" id="type-filter">
          <option value="">Type</option>
          <option value="all">Tout</option>
          <option value="manhwa">Manhwa</option>
          <option value="manga">Manga</option>
          <option value="manhua">Manhua</option>
        </select>
      </div>
      
      <div class="filter-dropdown">
        <select name="sort" id="sort-filter">
          <option value="default">Trié par Default</option>
          <option value="all">Tout</option>
          <option value="newest">Plus récent</option>
          <option value="oldest">Plus ancien</option>
          <option value="rating">Note</option>
        </select>
      </div>
      
      <button class="search-button">
        <i class="fas fa-search"></i> Recherche
      </button>
      
      <button class="text-mode-button">
        Mode Texte
      </button>
    </div>
    
    <div class="manga-grid">
      <?php foreach ($mangas as $manga): ?>
      <a href="/database/manga/<?php echo strtolower(str_replace([" ", "'"], ['-', ''], $manga['name'])); ?>.php">
        <div class="manga-card">
          <!-- Affichage de l'image de couverture -->
          <div class="manga-image" style="background-image: url('<?php echo htmlspecialchars($manga['cover_image']); ?>');">
            <div class="manga-type"><?php echo htmlspecialchars(strtoupper($manga['genre'])); ?></div>
          </div>
          <div class="manga-info">
            <!-- Nom du manga -->
            <div class="manga-title"><?php echo htmlspecialchars($manga['name']); ?></div>
            <!-- Nombre de chapitres -->
            <div class="manga-chapter">
              <?php 
                echo htmlspecialchars($manga['chapter_count']) . " chapitre" . 
                     ($manga['chapter_count'] > 1 ? "s" : "");
              ?>
            </div>
            <!-- description courte -->
            <div class="manga-short-desc">
              <?php echo htmlspecialchars($manga['short_description']); ?>
            </div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
<!-- FOOTER -->   
    <?php include('../../templates/footer.php'); ?>
<!-- JS SCRIPT --> 
  <script src="/assets/js/slider.js"></script>
  <script src="/assets/js/menu_toggle.js"></script>
</body>
</html>
