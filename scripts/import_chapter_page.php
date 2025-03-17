<?php
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

$basePath = '/var/www/isekaiscan/assets/scans';

$mangaDirs = array_filter(glob($basePath . '/*'), 'is_dir');

foreach ($mangaDirs as $mangaDir) {
    $mangaName = basename($mangaDir);
    
    $sqlCheckManga = "SELECT id, name FROM mangas WHERE name = :name LIMIT 1";
    $stmtManga = $pdo->prepare($sqlCheckManga);
    $stmtManga->bindParam(':name', $mangaName);
    $stmtManga->execute();
    $manga = $stmtManga->fetch(PDO::FETCH_ASSOC);
    
    if (!$manga) {
        $sqlInsertManga = "INSERT INTO mangas (name) VALUES (:name)";
        $stmtInsertManga = $pdo->prepare($sqlInsertManga);
        $stmtInsertManga->bindParam(':name', $mangaName);
        $stmtInsertManga->execute();
        $mangaId = $pdo->lastInsertId();
    } else {
        $mangaId = $manga['id'];
    }
    
    $chapterDirs = array_filter(glob($mangaDir . '/*'), 'is_dir');
    
    foreach ($chapterDirs as $chapterDir) {
        $chapterNumber = basename($chapterDir);
        
        $sqlCheckChapter = "SELECT id FROM chapters WHERE manga_id = :manga_id AND chapter_number = :chapter_number LIMIT 1";
        $stmtChapter = $pdo->prepare($sqlCheckChapter);
        $stmtChapter->bindParam(':manga_id', $mangaId);
        $stmtChapter->bindParam(':chapter_number', $chapterNumber);
        $stmtChapter->execute();
        $chapter = $stmtChapter->fetch(PDO::FETCH_ASSOC);
        
        if (!$chapter) {
            $sqlInsertChapter = "INSERT INTO chapters (manga_id, chapter_number) VALUES (:manga_id, :chapter_number)";
            $stmtInsertChapter = $pdo->prepare($sqlInsertChapter);
            $stmtInsertChapter->bindParam(':manga_id', $mangaId);
            $stmtInsertChapter->bindParam(':chapter_number', $chapterNumber);
            $stmtInsertChapter->execute();
            $chapterId = $pdo->lastInsertId();
        } else {
            $chapterId = $chapter['id'];
        }
        
        $imageFiles = glob($chapterDir . '/page_*.{jpg,png}', GLOB_BRACE);

        foreach ($imageFiles as $imageFile) {
            $pageNumber = (int) filter_var(basename($imageFile), FILTER_SANITIZE_NUMBER_INT);

            $imagePath = '/assets/scans/' . urlencode($mangaName) . '/' . ltrim($chapterNumber, '/') . '/' . basename($imageFile);

            $sqlInsertPage = "INSERT INTO chapter_pages (chapter_id, page_number, image_path) 
                            VALUES (:chapter_id, :page_number, :image_path)";
            $stmtInsertPage = $pdo->prepare($sqlInsertPage);
            $stmtInsertPage->bindParam(':chapter_id', $chapterId);
            $stmtInsertPage->bindParam(':page_number', $pageNumber);
            $stmtInsertPage->bindParam(':image_path', $imagePath);
            $stmtInsertPage->execute();
        }
    }
}

echo "Base de données mise à jour avec succès!";
?>
