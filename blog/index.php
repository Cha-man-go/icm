<?php
session_start();

function getPosts() {
    $posts_dir = 'posts/';
    $posts = [];
    $featured_posts = [];

    if (is_dir($posts_dir)) {
        foreach (glob($posts_dir . '*.json') as $file) {
            $post_content = file_get_contents($file);
            $post = json_decode($post_content, true);
            if (is_array($post)) {
                if (!empty($post['highlighted'])) {
                    $featured_posts[] = $post; // Dodajemo post u istaknute ako je označen kao takav
                }
                $posts[] = $post;
            }
        }
    
        usort($posts, function($a, $b) {
            $timestampA = isset($a['timestamp']) ? strtotime($a['timestamp']) : strtotime($a['date']);
            $timestampB = isset($b['timestamp']) ? strtotime($b['timestamp']) : strtotime($b['date']);
            return $timestampB - $timestampA;
        });
        
    }

    // Nakon sortiranja, izdvajamo najnoviji post
    $latest_post = array_shift($posts);

    return ['posts' => $posts, 'featured_posts' => $featured_posts, 'latest_post' => $latest_post];
}

$result = getPosts();
$posts = $result['posts'];
$featured_posts = $result['featured_posts'];
$latest_post = $result['latest_post'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Implant centre Martinko: la tua clinica dentale in Croazia" name="description">
    <title>Blog</title>
    <link href="css/normalize.css" rel="stylesheet" type="text/css">
    <link href="css/components.css" rel="stylesheet" type="text/css">
    <link href="css/icmartinko.css" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 60px; /* Space for navbar */
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'zaglavlje.html'; ?>
    <?php include 'blog-lista-naslov.html'; ?>
    <div class="container">

        <div class="row">
            <!-- Prostor za najnoviji blog post -->
            <div class="col-lg-8 col-md-12">
                <?php if ($latest_post): ?>
                <div class="card mb-4">
                    <img src="images/<?php echo htmlspecialchars($latest_post['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($latest_post['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($latest_post['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($latest_post['subtitle']); ?></p>
                        <a href="post.php?id=<?php echo urlencode($latest_post['id']); ?>" class="btn btn-primary">Pročitaj više</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Prostor za istaknute blogove -->
            <div class="col-lg-4 col-md-12">
                <div class="list-group">
                    <?php foreach ($featured_posts as $post): ?>
                    <a href="post.php?id=<?php echo urlencode($post['id']); ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo htmlspecialchars($post['title']); ?></h5>
                        </div>
                        <p class="mb-1"><?php echo htmlspecialchars($post['subtitle']); ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Prostor za sve ostale blogove -->
        <div class="row">
            <?php foreach ($posts as $post): ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <img src="images/<?php echo htmlspecialchars($post['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($post['subtitle']); ?></p>
                        <a href="post.php?id=<?php echo urlencode($post['id']); ?>" class="btn btn-primary">Pročitaj više</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'podnozje.html'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
