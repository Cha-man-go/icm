<?php
session_start();

// Provjeri je li korisnik prijavljen
if(isset($_SESSION['username'])) {
    // Ako je prijavljen, uništi sesiju
    session_unset();
    session_destroy();
    // Preusmjeri korisnika na stranicu za prijavu
    header("Location: login.php");
    exit();
} else {
    // Ako korisnik nije prijavljen, preusmjeri ga na stranicu za prijavu
    header("Location: login.php");
    exit();
}
?>
