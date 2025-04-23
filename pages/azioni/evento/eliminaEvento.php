<?php
session_start();
include '../../connesione.php';

// Verifica che l'utente sia loggato
if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit();
}

// Verifica che l'ID evento sia passato e valido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID evento non valido.";
    exit();
}

$idEvento = (int)$_GET['id'];
$username = $_SESSION['username'];

// Verifica che l'utente partecipi all'evento
$sqlCheck = "SELECT 1 FROM utente_evento WHERE evento_id = :idEvento AND username = :username";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute([':idEvento' => $idEvento, ':username' => $username]);

if ($stmtCheck->rowCount() === 0) {
    echo "Evento non trovato o non hai accesso a questo evento.";
    exit();
}

// Elimina l'evento dalla tabella 'evento' (aggiungendo un controllo sulla relazione)
$sqlDelete = "DELETE FROM evento WHERE id = :idEvento";
$stmtDelete = $pdo->prepare($sqlDelete);
$stmtDelete->execute([':idEvento' => $idEvento]);

// Rimuove anche gli utenti dalla tabella 'utente_evento' per quel determinato evento
$sqlDeletePartecipanti = "DELETE FROM utente_evento WHERE evento_id = :idEvento";
$stmtDeletePartecipanti = $pdo->prepare($sqlDeletePartecipanti);
$stmtDeletePartecipanti->execute([':idEvento' => $idEvento]);

// Dopo l'eliminazione, reindirizza l'utente alla home page
header('Location: ../../home.php');
exit();
?>
