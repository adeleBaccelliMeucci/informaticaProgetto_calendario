<?php
session_start();
include '../../connesione.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = $_POST['titolo'] ?? '';
    $urgenza = $_POST['urgenza'] ?? 5;
    $giorno = $_POST['giorno'] ?? date('Y-m-d');

    $attivitaTitoli = $_POST['attivita_titolo'] ?? [];
    $attivitaDescrizioni = $_POST['attivita_descrizione'] ?? [];

    // Inizia la transazione per inserire la to-do list e le attività
    $pdo->beginTransaction();
    try {
        // 1. Inserisci la to-do list
        $sql = "INSERT INTO toDoList (titolo, urgenza, giorno, username)
                VALUES (:titolo, :urgenza, :giorno, :username)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titolo' => $titolo,
            ':urgenza' => $urgenza,
            ':giorno' => $giorno,
            ':username' => $_SESSION['username']
        ]);

        $toDoListId = $pdo->lastInsertId();

        // 2. Inserisci le attività se presenti
        if (!empty($attivitaTitoli)) {
            for ($i = 0; $i < count($attivitaTitoli); $i++) {
                $titoloAtt = trim($attivitaTitoli[$i]);
                $descrizioneAtt = trim($attivitaDescrizioni[$i]);

                if ($titoloAtt !== '') {
                    $stmt = $pdo->prepare("INSERT INTO attivita (titolo, descrizione, idToDoList)
                                           VALUES (:titolo, :descrizione, :idToDoList)");
                    $stmt->execute([
                        ':titolo' => $titoloAtt,
                        ':descrizione' => $descrizioneAtt,
                        ':idToDoList' => $toDoListId
                    ]);
                }
            }
        }

        $pdo->commit();
        header("Location: ../../home.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $messaggio = "Errore: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuova To-Do List con Attività</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
    <h1>Crea nuova To-Do List</h1>

    <?php if (!empty($messaggio)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($messaggio) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Titolo</label>
            <input type="text" name="titolo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Urgenza (1 = bassa, 10 = altissima)</label>
            <input type="number" name="urgenza" class="form-control" min="1" max="10" value="5" required>
        </div>
        <div class="mb-3">
            <label>Giorno</label>
            <input type="date" name="giorno" class="form-control" required value="<?= date('Y-m-d') ?>">
        </div>

        <hr>
        <h3>Attività</h3>

        <div id="attivita-container">
            <!-- Le attività aggiunte dinamicamente verranno qui -->
        </div>

        <button type="button" class="btn btn-secondary mb-3" onclick="aggiungiAttivita()">+ Aggiungi Attività</button>
        <br>
        <button type="submit" class="btn btn-primary">Crea To-Do List</button>
    </form>
</div>

<script>
    // Funzione per aggiungere dinamicamente un campo di attività
    function aggiungiAttivita() {
        const container = document.getElementById('attivita-container');
        const nuovaAttivita = document.createElement('div');
        nuovaAttivita.className = 'attivita mb-3';
        nuovaAttivita.innerHTML = `
            <label>Titolo attività</label>
            <input type="text" name="attivita_titolo[]" class="form-control mb-2" required>
            <label>Descrizione</label>
            <textarea name="attivita_descrizione[]" class="form-control"></textarea>
            <hr>
        `;
        container.appendChild(nuovaAttivita);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
