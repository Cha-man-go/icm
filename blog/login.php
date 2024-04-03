<?php
session_start();

// Provjeri je li korisnik već prijavljen, ako jest preusmjeri ga na administrativnu stranicu
if(isset($_SESSION['username'])) {
    header("Location: admin.php");
    exit();
}

// Postavite korisničko ime i lozinku
$valid_username = "chamango";
$valid_password = "slavisko";

// Provjerite je li forma poslana i ako jest, provjerite podatke za prijavu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Provjeri je li uneseno korisničko ime i lozinka
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        // Provjeri jesu li uneseno korisničko ime i lozinka ispravni
        if ($_POST['username'] === $valid_username && $_POST['password'] === $valid_password) {
            // Postavi sesiju i preusmjeri na administrativnu stranicu
            $_SESSION['username'] = $_POST['username'];
            header("Location: admin.php");
            exit();
        } else {
            $error_message = "Pogrešno korisničko ime ili lozinka";
        }
    } else {
        $error_message = "Unesite korisničko ime i lozinku";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Prijava</h2>
    <?php
    if(isset($error_message)) {
        echo '<p class="error-message">' . $error_message . '</p>';
    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="username">Korisničko ime:</label><br>
        <input type="text" id="username" name="username"><br>
        <label for="password">Lozinka:</label><br>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Prijavi se">
    </form>
</body>
</html>
