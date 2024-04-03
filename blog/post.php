<?php
// Pretpostavimo da je 'id' poslan kroz query string u URL-u.
$post_id = $_GET['id'] ?? '';
$posts_dir = 'posts/';
$post = [];

if ($post_id) {
    // Formiranje imena JSON datoteke na temelju ID-a.
    // Ovo pretpostavlja da ID odgovara imenu JSON datoteke.
    $filename = $posts_dir . $post_id . '.json';

    // Ako datoteka postoji, dohvatiti sadržaj.
    if (file_exists($filename)) {
        $post_content = file_get_contents($filename);
        $post = json_decode($post_content, true);
    }
}

// Provjeri da li post postoji prije prikazivanja
if (!$post) {
    // Ako post ne postoji, preusmjeri na glavnu stranicu ili prikaži poruku
    header('Location: zaglavlje.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        body {
            padding-top: 60px; /* Mjesto za navbar */
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'zaglavlje.html'; ?>


    <div class="container">
        <div class="card">
            <div class="card-body">
    <h2><?php echo html_entity_decode($post['title']); ?></h2>
    <h3><?php echo html_entity_decode($post['subtitle']); ?></h3>
    <p><?php echo date('d.m.Y.', strtotime($post['date'])); ?></p>
    <?php if (!empty($post['image'])): ?>
        <img src="images/<?php echo htmlspecialchars($post['image']); ?>" class="img-fluid mb-3" alt="<?php echo html_entity_decode($post['title']); ?>">
    <?php endif; ?>
    <p><?php echo nl2br(html_entity_decode($post['content'])); ?></p> <!-- Ispravno dekodiranje sadržaja -->
</div>
        </div>
    </div>

    <?php include 'podnozje.html'; ?>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
