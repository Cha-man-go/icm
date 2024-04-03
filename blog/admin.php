<?php
session_start();

// Ovdje dodajte logiku za provjeru sesije i autentifikaciju
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$message = isset($_GET['message']) ? $_GET['message'] : '';
$posts_dir = 'posts/';
$posts = [];

// Logika za učitavanje postova
if (is_dir($posts_dir)) {
    foreach (glob($posts_dir . '*.json') as $file) {
        $post_content = file_get_contents($file);
        $post = json_decode($post_content, true);
        if (is_array($post)) {
            $posts[] = $post;
        }
    }
}

// Provjera da li je 'id' poslan kroz query string u URL-u za brisanje posta
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $filename = $posts_dir . $post_id . '.json';

    if (file_exists($filename)) {
        unlink($filename); // Brisanje datoteke
        $message = 'Post deleted successfully';
    } else {
        $message = 'Post not found';
    }
    // Osvježi stranicu da ukloni post iz prikaza
    header("Location: admin.php?message=" . urlencode($message));
    exit;
}

?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Administracija Blogova</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Stilovi i skripte kako je potrebno -->
</head>
<body>
<div class="container mt-5">
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h2>Administracija Blogova</h2>
    <a href="admin_add.php" class="btn btn-success mb-3">Dodaj Novi Post</a>
    
    <form method="post" action="admin_update_highlight.php">
        <div class="list-group">
            <?php foreach ($posts as $post): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($post['title']); ?>
                    <div>
                        <input type="checkbox" name="highlighted[<?php echo $post['id']; ?>]" <?php echo (isset($post['highlighted']) && $post['highlighted']) ? 'checked' : ''; ?> /> Istaknuti
                        <a href="admin_edit.php?id=<?php echo urlencode($post['id']); ?>" class="btn btn-primary btn-sm">Uredi</a>
                        <a href="admin_delete.php?id=<?php echo urlencode($post['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Jeste li sigurni da želite obrisati ovaj post?');">Obriši</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Ažuriraj istaknute postove</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
