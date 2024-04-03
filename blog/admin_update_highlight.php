<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$posts_dir = 'posts/';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $highlighted_info = $_POST['highlighted'] ?? [];
    foreach ($highlighted_info as $post_id => $highlighted) {
        $filename = $posts_dir . $post_id . '.json';
        if (file_exists($filename)) {
            $post_data = json_decode(file_get_contents($filename), true);
            $post_data['highlighted'] = (bool)$highlighted;
            file_put_contents($filename, json_encode($post_data, JSON_PRETTY_PRINT));
            $message .= 'Post ' . $post_id . ' updated. ';
        } else {
            $message .= 'Post ' . $post_id . ' not found. ';
        }
    }
}

header("Location: admin.php?message=" . urlencode($message));
exit;
?>

