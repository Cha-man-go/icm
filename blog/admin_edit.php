<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$postsDir = 'posts/';
$imagesDir = 'images/';

$message = '';
$postData = [];

// Dohvaćanje ID-a posta iz URL-a
$post_id = $_GET['id'] ?? '';

if ($post_id) {
    $filename = $postsDir . $post_id . '.json';
    if (file_exists($filename)) {
        $post_content = file_get_contents($filename);
        $postData = json_decode($post_content, true);
    } else {
        $message = 'Post nije pronađen.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title'] ?? 'Bez naslova');
    $subtitle = htmlspecialchars($_POST['subtitle'] ?? '');
    $content = htmlspecialchars($_POST['content'] ?? '');
    $date = htmlspecialchars($_POST['date'] ?? date('Y-m-d'));

    $imageAlt = htmlspecialchars($_POST['image_alt'] ?? $title); // Ako nije unesen alt tekst, koristi naslov
    $seoTitle = htmlspecialchars($_POST['seo_title'] ?? $title); // Ako SEO naslov nije unesen, koristi naslov
    $seoDescription = htmlspecialchars($_POST['seo_description'] ?? $subtitle); // Ako SEO opis nije unesen, koristi podnaslov
    $openGraphTitle = htmlspecialchars($_POST['open_graph_title'] ?? $title); // Ako Open Graph naslov nije unesen, koristi naslov
    $openGraphDescription = htmlspecialchars($_POST['open_graph_description'] ?? $subtitle); // Ako Open Graph opis nije unesen, koristi podnaslov

    $imageName = $postData['image'] ?? '';
    if (isset($_FILES['image']['error']) && $_FILES['image']['error'] == 0) {
        $imageType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($imageType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            if (!empty($imageName)) {
                // Ako postoji prethodna slika, opcionalno ju možete obrisati
                // unlink("$imagesDir$imageName");
            }
            $tempName = $_FILES['image']['tmp_name'];
            $imageName = uniqid() . '-' . $_FILES['image']['name'];
            move_uploaded_file($tempName, "$imagesDir$imageName");
        } else {
            $message = 'Nepodržani format slike.';
        }
    }
    
}


$postData = [
    'id' => $post_id, // Zadržite originalni ID
    'title' => $title,
    'subtitle' => $subtitle,
    'content' => $content,
    'date' => $date,
    'image' => $imageName, // Ažurirajte ime slike nakon obrade slike
    'seo_title' => $seoTitle,
    'seo_description' => $seoDescription,
     'open_graph_title' => $openGraphTitle,
     'open_graph_description' => $openGraphDescription,
        'open_graph_image' => htmlspecialchars($_POST['open_graph_image'] ?? ''),
        'image' => $imageName
    ];

    if (file_put_contents($filename, json_encode($postData, JSON_PRETTY_PRINT))) {
        $message = 'Post je uspješno ažuriran.';
        header('Location: admin.php'); // Preusmjerite natrag na glavnu admin stranicu
        exit;
    } else {
        $message = 'Došlo je do greške pri ažuriranju posta.';
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Uredi Post</title>
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
    <h2>Uredi Post</h2>
    <!-- PHP poruka ako postoji -->
    <form action="admin_edit.php?id=<?php echo urlencode($post_id); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Naslov:</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($postData['title'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Podnaslov:</label>
            <input type="text" name="subtitle" class="form-control" value="<?php echo htmlspecialchars($postData['subtitle'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Sadržaj:</label>
            <textarea id="content" name="content" class="form-control"><?php echo htmlspecialchars($postData['content'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Datum:</label>
            <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($postData['date'] ?? date('Y-m-d')); ?>">
        </div>
        <div class="form-group">
            <label>SEO Naslov:</label>
            <input type="text" name="seo_title" class="form-control" value="<?php echo htmlspecialchars($postData['seo_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>SEO Opis:</label>
            <textarea name="seo_description" class="form-control"><?php echo htmlspecialchars($postData['seo_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Open Graph Naslov:</label>
            <input type="text" name="open_graph_title" class="form-control" value="<?php echo htmlspecialchars($postData['open_graph_title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Open Graph Opis:</label>
            <textarea name="open_graph_description" class="form-control"><?php echo htmlspecialchars($postData['open_graph_description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Open Graph Slika (URL):</label>
            <input type="text" name="open_graph_image" class="form-control" value="<?php echo htmlspecialchars($postData['open_graph_image'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Trenutna Slika:</label>
            <?php if (!empty($postData['image'])): ?>
                <img src="images/<?php echo htmlspecialchars($postData['image']); ?>" width="100" height="auto">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($postData['image']); ?>">
            <?php else: ?>
                <p>Nema trenutne slike.</p>
            <?php endif; ?>
            <label>Odaberite novu sliku ako želite zamijeniti:</label>
            <input type="file" name="image" class="form-control-file">
        </div>
        <!-- Ažurirani gumb za slanje obrasca -->
        <button type="submit" class="btn btn-primary">Ažuriraj post</button>
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
