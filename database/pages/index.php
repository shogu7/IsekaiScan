<?php
// index.php

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
  <title>IsekaiScan</title>
  <link rel="stylesheet" href="/assets/css/pages/index.css">
  <link rel="icon" href="/assets/images/icones/favicon.ico" type="image/x-icon">
</head>
<body>
<?php include('../../templates/header.php'); ?>
<div class="hero-slider">
  <div class="slides">
    <?php 
    $limitedMangas = array_slice($mangas, 0, 4); 
    foreach ($limitedMangas as $manga): 
    ?>
      <div class="slide" style="background-image: url('<?php echo htmlspecialchars($manga['banner_image']); ?>')">
        <div class="slide-content">
          <h2><?php echo htmlspecialchars($manga['name']); ?></h2>
          <p><?php echo htmlspecialchars($manga['short_description']); ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


  <div class="recruitment-banner">
    <i class="fas fa-bullhorn"></i>
    <p><strong>En développement:</strong> Version de test!</p>
  </div>

  <div class="section-title">
    <h2>Les plus <span>Populaire</span></h2>
  </div>
<div class="manga-cards">
<?php 
  $limitedMangas = array_slice($mangas, 0, 8);
  foreach ($limitedMangas as $manga): 
  ?>
    <div class="manga-card">
      <img src="<?php echo htmlspecialchars($manga['small_image']); ?>" alt="<?php echo htmlspecialchars($manga['name']); ?>">
      <div class="manga-info">
        <h3><?php echo htmlspecialchars($manga['name']); ?></h3>
        <p><?php echo htmlspecialchars($manga['short_description']); ?></p>
        <div class="meta">
          <span><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($manga['chapter_count']); ?> Chapitres</span>
        </div>
      </div>
    </div>
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