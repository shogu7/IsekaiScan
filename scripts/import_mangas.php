<?php

$basePath = '/var/www/isekaiscan/assets/scans';

$host     = 'localhost';
$dbname   = "isekaiscan_db";
$username = 'isekaiuser';
$password = 'Natixis0607&*';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$directories = array_filter(glob($basePath . '/*'), 'is_dir');

foreach ($directories as $dir) {
    $mangaName = basename($dir);  
    
    $infoFilePath = $dir . '/info.txt';
    if (file_exists($infoFilePath)) {
        $infoContent = file_get_contents($infoFilePath);
        $info = json_decode($infoContent, true);

        $genre = $info['genre'] ?? 'Inconnu';
        $shortDescription = $info['short_description'] ?? 'Aucune description';
        $longDescription = $info['long_description'] ?? 'Aucune description longue';
        $alternateTitle = $info['alternate_title'] ?? 'Pas de titre alternatif';
        $author = $info['author'] ?? 'Inconnue';

        $chapterDirs = array_filter(glob($dir . '/*'), 'is_dir');
        $chapterNumbers = [];
        foreach ($chapterDirs as $chapterDir) {
            $chapterName = basename($chapterDir);
            if (is_numeric($chapterName)) {
                $chapterNumbers[] = intval($chapterName);
            }
        }
        sort($chapterNumbers);
        $chapterCount = count($chapterNumbers) - 1;
        $firstChapter = !empty($chapterNumbers) ? min($chapterNumbers) : null;
        $lastChapter  = !empty($chapterNumbers) ? max($chapterNumbers) : null;

        $banner_imagePath = '/assets/images/banner/' . strtolower(str_replace("'", "", $mangaName)) . '_banner.jpg';
        $small_imagePath  = '/assets/images/small_image/' . strtolower(str_replace("'", "", $mangaName)) . '_small_image.jpg';
        $coverImagePath   = '/assets/images/cover/' . strtolower(str_replace("'", "", $mangaName)) . '_cover.jpg';

        $sqlCheck = "SELECT COUNT(*) FROM mangas WHERE name = :name";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':name', $mangaName);
        $stmtCheck->execute();
        $exists = $stmtCheck->fetchColumn();
        
        if ($exists) {
            $sqlUpdate = "UPDATE mangas 
                    SET genre = :genre, chapter_count = :chapter_count, short_description = :short_description, 
                        long_description = :long_description, cover_image = :cover_image, 
                        small_image = :small_image, banner_image = :banner_image,
                        first_chapter = :first_chapter, last_chapter = :last_chapter, alternate_title = :alternate_title, author = :author
                    WHERE name = :name";

            $stmtUpdate = $pdo->prepare($sqlUpdate);
            
            $stmtUpdate->bindParam(':name', $mangaName);
            $stmtUpdate->bindParam(':genre', $genre);
            $stmtUpdate->bindParam(':chapter_count', $chapterCount, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':short_description', $shortDescription);
            $stmtUpdate->bindParam(':long_description', $longDescription);
            $stmtUpdate->bindParam(':cover_image', $coverImagePath);
            $stmtUpdate->bindParam(':small_image', $small_imagePath);
            $stmtUpdate->bindParam(':banner_image', $banner_imagePath);
            $stmtUpdate->bindParam(':first_chapter', $firstChapter, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':last_chapter', $lastChapter, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':alternate_title', $alternateTitle);
            $stmtUpdate->bindParam(':author', $author);
            
            try {
                $stmtUpdate->execute();
                echo "Manga $mangaName mis à jour avec succès.<br>";
            } catch (PDOException $e) {
                echo "Erreur lors de la mise à jour de $mangaName : " . $e->getMessage() . "<br>";
            }
        } else {
            $sqlInsert = "INSERT INTO mangas (name, genre, chapter_count, short_description, long_description, cover_image, small_image, banner_image, first_chapter, last_chapter, author, alternate_title) 
                          VALUES (:name, :genre, :chapter_count, :short_description, :long_description, :cover_image, :small_image, :banner_image, :first_chapter, :last_chapter, :author, :alternate_title)";
            
            $stmtInsert = $pdo->prepare($sqlInsert);
            
            $stmtInsert->bindParam(':name', $mangaName);
            $stmtInsert->bindParam(':genre', $genre);
            $stmtInsert->bindParam(':chapter_count', $chapterCount, PDO::PARAM_INT);
            $stmtInsert->bindParam(':short_description', $shortDescription);
            $stmtInsert->bindParam(':long_description', $longDescription);
            $stmtInsert->bindParam(':cover_image', $coverImagePath);
            $stmtInsert->bindParam(':small_image', $small_imagePath);
            $stmtInsert->bindParam(':banner_image', $banner_imagePath);
            $stmtInsert->bindParam(':first_chapter', $firstChapter, PDO::PARAM_INT);
            $stmtInsert->bindParam(':last_chapter', $lastChapter, PDO::PARAM_INT);
            $stmtInsert->bindParam(':alternate_title', $alternateTitle);
            $stmtInsert->bindParam(':author', $author);            
            
            try {
                $stmtInsert->execute();
                echo "Manga $mangaName inséré avec succès.<br>";
            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion de $mangaName : " . $e->getMessage() . "<br>";
            }
        }
    } else {
        echo "Fichier info.txt non trouvé pour $mangaName.<br>";
    }
}
?>
