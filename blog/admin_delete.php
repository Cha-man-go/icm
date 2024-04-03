<?php
session_start();

// Provjera da li je korisnik autentificiran
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Definiranje putanje direktorija gdje se nalaze postovi
$posts_dir = 'posts/';

// Provjera da li je ID posta poslan kroz query string
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $post_id = $_GET['id'];
    $post_file = $posts_dir . $post_id . '.json';

    // Provjera da li datoteka postoji
    if (file_exists($post_file)) {
        // Pokušaj brisanja datoteke posta
        if (unlink($post_file)) {
            $message = "Post uspješno obrisan.";
        } else {
            $message = "Došlo je do greške prilikom brisanja posta.";
        }
    } else {
        $message = "Nije pronađen post s ID-em '{$post_id}'.";
    }
} else {
    $message = "Nije specificiran ID posta za brisanje.";
}

// Preusmjeravanje natrag na admin stranicu s porukom
header("Location: admin.php?message=" . urlencode($message));
exit;
?>
