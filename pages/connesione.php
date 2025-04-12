<?php
$host = 'localhost';
$dbname = 'calendario';
$username = 'root';  // Cambia se hai un altro utente
$password = '';      // Cambia se hai una password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}
?>
