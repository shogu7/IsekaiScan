<?php
// chapter.php

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

if (!isset($_GET['manga']) || !isset($_GET['chapter'])) {
    die("Manga ou chapitre non spécifié.");
}

$mangaName = $_GET['manga'];
$chapterNumber = intval($_GET['chapter']);  

if (empty($mangaName) || $chapterNumber < 0) {
    die("Paramètres invalides.");
}

$sqlManga = "SELECT * FROM mangas WHERE name = :name LIMIT 1";
$stmtManga = $pdo->prepare($sqlManga);
$stmtManga->bindParam(':name', $mangaName);
$stmtManga->execute();
$manga = $stmtManga->fetch(PDO::FETCH_ASSOC);

if (!$manga) {
    die("Manga non trouvé.");
}

$sqlChapter = "SELECT * FROM chapters WHERE manga_id = :manga_id AND chapter_number = :chapter_number LIMIT 1";
$stmtChapter = $pdo->prepare($sqlChapter);
$stmtChapter->bindParam(':manga_id', $manga['id'], PDO::PARAM_INT);
$stmtChapter->bindParam(':chapter_number', $chapterNumber, PDO::PARAM_INT);
$stmtChapter->execute();
$chapter = $stmtChapter->fetch(PDO::FETCH_ASSOC);

if (!$chapter) {
    die("Chapitre non trouvé.");
}

$sqlImages = "SELECT * FROM chapter_pages WHERE chapter_id = :chapter_id ORDER BY page_number ASC";
$stmtImages = $pdo->prepare($sqlImages);
$stmtImages->bindParam(':chapter_id', $chapter['id'], PDO::PARAM_INT);
$stmtImages->execute();
$pages = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
 
$prevChapter = $chapterNumber > $manga['first_chapter'] ? $chapterNumber - 1 : null;
$nextChapter = $chapterNumber < $manga['last_chapter'] ? $chapterNumber + 1 : null;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($manga['name']) . " - Chapitre " . $chapterNumber; ?> - IsekaiScan</title>
    <link rel="stylesheet" href="/assets/css/pages/chapter.css">
    <link rel="icon" href="/assets/images/icones/favicon.ico" type="image/x-icon">
</head>
  <?php include('../../templates/header.php'); ?>
  <!-- URL DU SCAN -->
<body>
<div class="chapter-navigation">
  <div class="chapter-align">
    <div class="chapter-header">
      <a href="http://146.59.229.153/database/pages/index.php">Accueil/</a>
      <a href="http://146.59.229.153/database/pages/project.php">Projet/</a>
      <a href="http://146.59.229.153/database/manga/<?php echo urlencode(strtolower(str_replace([' ', "'", '+'], ['-', '', '-'], $manga['name']))); ?>.php"> <?php echo htmlspecialchars($manga['name']); ?>/</a>
      <span>Chapitre <?php echo $chapterNumber; ?></span>
    </div>
    <!-- Premier chapter-selector DEBUT DE PAGE -->
    <div class="chapter-selector">
      <!-- Bouton précédent -->
      <?php if (!is_null($prevChapter)): ?>
        <a href="chapter.php?manga=<?php echo urlencode($manga['name']); ?>&chapter=<?php echo $prevChapter; ?>" class="nav-arrow">
          <span class="arrow-icon">&lt;</span>
        </a>
      <?php else: ?>
        <span class="nav-arrow disabled"><span class="arrow-icon">&lt;</span></span>
      <?php endif; ?>
      <!-- Bouton dropdown central -->
      <div class="chapter-dropdown">
        <button class="chapter-dropdown-btn">
         <span class="chapter-number">Chapitre <?php echo $chapterNumber; ?></span>
          <span class="dropdown-icon">◄</span>
        </button>
        <div class="chapter-dropdown-content">
          <div class="search-container">
            <input type="text" id="chapterSearch" placeholder="Rechercher">
          </div>
          <div class="chapter-list">
            <?php 
              for ($i = $manga['first_chapter']; $i <= $manga['last_chapter']; $i++) {
                $active = ($i == $chapterNumber) ? 'active' : '';
                echo '<a href="chapter.php?manga=' . urlencode($manga['name']) . '&chapter=' . $i . '" class="' . $active . '">
                  <span class="chapter-item">' . $i . '</span>
                </a>';
              }
            ?>
          </div>
        </div>
      </div>

      <!-- Bouton suivant -->
      <?php if ($nextChapter): ?>
        <a href="chapter.php?manga=<?php echo urlencode($manga['name']); ?>&chapter=<?php echo $nextChapter; ?>" class="nav-arrow">
          <span class="arrow-icon">&gt;</span>
        </a>
      <?php else: ?>
        <span class="nav-arrow disabled"><span class="arrow-icon">&gt;</span></span>
      <?php endif; ?>
    </div>
  </div>
</div>
<!-- AFFICHAGE PAGE BLOUCE PHP -->
 <main>
    <h1 class="title"><?php echo htmlspecialchars($manga['name']); ?> - Chapitre <?php echo $chapterNumber; ?></h1>
        <div class="chapter-content">
        <?php foreach ($pages as $page): ?>
            <img src="<?php echo htmlspecialchars($page['image_path']); ?>" 
                 class="chapter-page" 
                 alt="Page <?php echo htmlspecialchars($page['page_number']); ?>">
        <?php endforeach; ?>
        </div>
 </main>
  <!-- Deuxieme chapter-selector FIN DE PAGE -->
   <div class="chapter-navigation-bottom">
  <div class="chapter-selector">
      <!-- Bouton précédent -->
      <?php if (!is_null($prevChapter)): ?>
        <a href="chapter.php?manga=<?php echo urlencode($manga['name']); ?>&chapter=<?php echo $prevChapter; ?>" class="nav-arrow">
          <span class="arrow-icon">&lt;</span>
        </a>
      <?php else: ?>
        <span class="nav-arrow disabled"><span class="arrow-icon">&lt;</span></span>
      <?php endif; ?>
      <!-- Bouton dropdown central -->
      <div class="chapter-dropdown">
        <button class="chapter-dropdown-btn">
          Chapitre <span class="chapter-number"><?php echo $chapterNumber; ?></span>
          <span class="dropdown-icon">▼</span>
        </button>
        <div class="chapter-dropdown-content">
          <div class="search-container">
            <input type="text" id="chapterSearch" placeholder="Rechercher">
          </div>
          <div class="chapter-list">
            <?php 
              for ($i = $manga['first_chapter']; $i <= $manga['last_chapter']; $i++) {
                $active = ($i == $chapterNumber) ? 'active' : '';
                echo '<a href="chapter.php?manga=' . urlencode($manga['name']) . '&chapter=' . $i . '" class="' . $active . '">
                  <span class="chapter-item">' . $i . '</span>
                </a>';
              }
            ?>
          </div>
        </div>
      </div>
      <!-- Bouton suivant -->
      <?php if ($nextChapter): ?>
        <a href="chapter.php?manga=<?php echo urlencode($manga['name']); ?>&chapter=<?php echo $nextChapter; ?>" class="nav-arrow">
          <span class="arrow-icon">&gt;</span>
        </a>
      <?php else: ?>
        <span class="nav-arrow disabled"><span class="arrow-icon">&gt;</span></span>
      <?php endif; ?>
    <button id="scrollToTopBtn" class="scroll-to-top-btn">↑ Retour en haut</button>
    </div>
  </div>
</div>
</div>
<!-- FOOTER -->   
    <?php include('../../templates/footer.php'); ?>
<!-- JS SCRIPT --> 
    <script src="/assets/js/press_top_button.js"></script>
    <script src="/assets/js/header_chapter_bar.js"></script>
</body>
</html>