<?php
session_start();
include '../connesione.php';

$user = $_POST['user'];

$stmt = $pdo->prepare("UPDATE utente SET amministratore = NOT amministratore WHERE username = ?");
$stmt->execute([$user]);

header("Location: gestioneUtenti.php");
exit;
?>
