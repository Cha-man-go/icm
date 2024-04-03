<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$postsDir = 'posts/';
$imagesDir = 'images/';
if (!is_dir($postsDir)) {
    mkdir($postsDir, 0777, true);
}
if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0777, true);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'] ?? 'Bez naslova');
    $subtitle = htmlspecialchars($_POST['subtitle'] ?? '');
    $content = htmlspecialchars($_POST['content'] ?? '');
    $date = htmlspecialchars($_POST['date'] ?? date('Y-m-d'));
    $timestamp = htmlspecialchars($_POST['timestamp'] ?? date('Y-m-d H:i:s'));
    $imageAlt = htmlspecialchars($_POST['image_alt'] ?? $title); // Ako nije unesen alt tekst, koristi naslov
    $seoTitle = htmlspecialchars($_POST['seo_title'] ?? $title); // Ako SEO naslov nije unesen, koristi naslov
    $seoDescription = htmlspecialchars($_POST['seo_description'] ?? $subtitle); // Ako SEO opis nije unesen, koristi podnaslov
    $openGraphTitle = htmlspecialchars($_POST['open_graph_title'] ?? $title); // Ako Open Graph naslov nije unesen, koristi naslov
    $openGraphDescription = htmlspecialchars($_POST['open_graph_description'] ?? $subtitle); // Ako Open Graph opis nije unesen, koristi podnaslov
    $openGraphImage = htmlspecialchars($_POST['open_graph_image'] ?? ''); // Možete dodati logiku za zadavanje defaultne slike


    $imageName = '';
    if (isset($_FILES['image']['error']) && $_FILES['image']['error'] == 0) {
        $imageType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($imageType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            $tempName = $_FILES['image']['tmp_name'];
            $imageName = uniqid() . '-' . $_FILES['image']['name'];
            move_uploaded_file($tempName, "$imagesDir$imageName");
        } else {
            $message = 'Nepodržani format slike.';
        }
    }

    if ($imageName || !$_FILES['image']['name']) {
        $postData = [
            'id' => uniqid('post_'),
            'title' => $title,
            'subtitle' => $subtitle,
            'content' => $content,
            'date' => $date,
            "timestamp" => $timestamp, // Dodajemo točno vrijeme objave
            'seo_title' => htmlspecialchars($_POST['seo_title'] ?? ''),
            'seo_description' => htmlspecialchars($_POST['seo_description'] ?? ''),
            'open_graph_title' => htmlspecialchars($_POST['open_graph_title'] ?? ''),
            'open_graph_description' => htmlspecialchars($_POST['open_graph_description'] ?? ''),
            'open_graph_image' => htmlspecialchars($_POST['open_graph_image'] ?? ''),
            'image' => $imageName,
            'alt' => $imageAlt,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'open_graph_title' => $openGraphTitle,
            'open_graph_description' => $openGraphDescription,
            'open_graph_image' => $openGraphImage
        ];

        $postFile = $postsDir . $postData['id'] . '.json';
        if (file_put_contents($postFile, json_encode($postData, JSON_PRETTY_PRINT))) {
            $message = 'Post je uspješno spremljen.';
            header('Location: index.php'); // Preusmjeravanje na index.php
            exit; // Prekida izvršavanje skripte nakon preusmjeravanja
        } else {
            $message = 'Došlo je do greške pri spremanju posta.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dodaj Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.tiny.cloud/1/c90wwea062rcntk77chwngi9kpoy5dzxohn86eyrdck5hubr/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'link image media',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media'
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Dodaj novi post</h2>
    <!-- PHP poruka ako postoji -->
    <form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>Naslov:</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Podnaslov:</label>
        <input type="text" name="subtitle" class="form-control">
    </div>
    <div class="form-group">
        <label>Sadržaj:</label>
        <textarea id="content" name="content" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label>Datum:</label>
        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="form-group">
        <label>Vrijeme objave:</label>
        <input type="datetime-local" name="timestamp" class="form-control" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i:s')); ?>">
    </div>
    <div class="form-group">
        <label>Alt tekst slike:</label>
        <input type="text" name="image_alt" class="form-control">
    </div>
    <div class="form-group">
        <label>SEO Naslov:</label>
        <input type="text" name="seo_title" class="form-control">
    </div>
    <div class="form-group">
        <label>SEO Opis:</label>
        <textarea name="seo_description" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label>Open Graph Naslov:</label>
        <input type="text" name="open_graph_title" class="form-control">
    </div>
    <div class="form-group">
        <label>Open Graph Opis:</label>
        <textarea name="open_graph_description" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label>Open Graph Slika (URL):</label>
        <input type="text" name="open_graph_image" class="form-control">
    </div>
    <div class="form-group">
        <label>Slika:</label>
        <input type="file" name="image" class="form-control-file">
    </div>
    <button type="submit" class="btn btn-primary">Spremi post</button>
</form>

        
        <!-- Ažurirani gumb za slanje obrasca -->
        <button type="submit" class="btn btn-primary">Spremi post</button>
    </form>
</div>

<script>
    tinymce.init({
        selector: '#content',
        plugins: 'link image',
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link image'
    });
</script>

</body>
</html>
