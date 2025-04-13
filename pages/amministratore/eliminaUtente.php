<?php
session_start();
include '../connesione.php';

$user = $_POST['user'];

$stmt = $pdo->prepare("DELETE FROM utente WHERE username = ?");
$stmt->execute([$user]);

header("Location: gestioneUtenti.php");
exit;
?>
