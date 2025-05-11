<?php
session_start();
include '../../connesione.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attivita_id'])) {
    $id = (int) $_POST['attivita_id'];
    $username = $_SESSION['username'];

    // Verifica che l'attività appartenga all'utente
    $stmt = $pdo->prepare("
        SELECT a.completata
        FROM attivita a
        INNER JOIN toDoList t ON a.idToDoList = t.id
        WHERE a.id = ? AND t.username = ?
    ");
    $stmt->execute([$id, $username]);
    $attivita = $stmt->fetch();

    if ($attivita) {
        $nuovoStato = $attivita['completata'] ? 0 : 1;
        $update = $pdo->prepare("UPDATE attivita SET completata = ? WHERE id = ?");
        $update->execute([$nuovoStato, $id]);
    }
}

header("Location: ../../home.php");
exit;
