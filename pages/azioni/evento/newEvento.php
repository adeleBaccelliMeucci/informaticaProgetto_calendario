<?php
session_start();
include '../../connesione.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

$utenti = [];
$creato = false;

// Prendi tutti gli utenti tranne se stesso
$sqlUtenti = "SELECT username FROM utente WHERE username != :me";
$stmtUtenti = $pdo->prepare($sqlUtenti);
$stmtUtenti->bindParam(':me', $_SESSION['username']);
$stmtUtenti->execute();
$utenti = $stmtUtenti->fetchAll(PDO::FETCH_ASSOC);

// Gestione del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = $_POST['titolo'];
    $descrizione = $_POST['descrizione'];
    $colore = $_POST['colore'];
    $dataInizio = $_POST['dataInizio'];
    $dataFine = $_POST['dataFine'];
    $orarioInizio = $_POST['orarioInizio'];
    $orarioFine = $_POST['orarioFine'];
    $utentiSelezionati = $_POST['utenti'] ?? [];

    $pdo->beginTransaction();

    try {
        // 1. Inserisci l'evento nella tabella evento
        $sql = "INSERT INTO evento (titolo, descrizione, colore, dataInizio, dataFine, orarioInizio, orarioFine)
                VALUES (:titolo, :descrizione, :colore, :dataInizio, :dataFine, :orarioInizio, :orarioFine)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titolo' => $titolo,
            ':descrizione' => $descrizione,
            ':colore' => $colore,
            ':dataInizio' => $dataInizio,
            ':dataFine' => $dataFine,
            ':orarioInizio' => $orarioInizio,
            ':orarioFine' => $orarioFine
        ]);

        // Ottieni l'ID dell'evento appena inserito
        $eventoId = $pdo->lastInsertId();

        // 2. Aggiungi l'utente proprietario (attualmente loggato)
        $stmt = $pdo->prepare("INSERT INTO utente_evento (username, evento_id) VALUES (:username, :evento_id)");
        $stmt->execute([
            ':username' => $_SESSION['username'],
            ':evento_id' => $eventoId
        ]);

        // 3. Aggiungi gli altri utenti selezionati
        foreach ($utentiSelezionati as $utente) {
            $stmt = $pdo->prepare("INSERT INTO utente_evento (username, evento_id) VALUES (:username, :evento_id)");
            $stmt->execute([':username' => $utente, ':evento_id' => $eventoId]);
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
    <title>Nuovo Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <div class="container">
        <h1>Crea nuovo evento</h1>

        <?php
        $oggi = date('Y-m-d');
        $oraInizio = date('H:i');

        $oraFineDateTime = new DateTime($oraInizio);
        $oraFineDateTime->modify('+1 hour');
        $oraFine = $oraFineDateTime->format('H:i');
        ?>

        <form method="POST">
            <div class="mb-3">
                <label>Titolo</label>
                <input type="text" name="titolo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Descrizione</label>
                <textarea name="descrizione" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label>Colore (RGB o nome CSS)</label>
                <input type="color" name="colore" class="form-control" value="#000000">
            </div>
            <div class="mb-3">
                <label>Data Inizio</label>
                <input type="date" name="dataInizio" class="form-control" required value="<?= $oggi ?>">
            </div>
            <div class="mb-3">
                <label>Data Fine</label>
                <input type="date" name="dataFine" class="form-control" required value="<?= $oggi ?>">
            </div>
            <div class="mb-3">
                <label>Orario Inizio</label>
                <input type="time" name="orarioInizio" class="form-control" required value="<?= $oraInizio ?>">
            </div>
            <div class="mb-3">
                <label>Orario Fine</label>
                <input type="time" name="orarioFine" class="form-control" required value="<?= $oraFine ?>">
            </div>
            <?php if (!empty($utenti)) { ?>
                <div class="mb-3">
                    <label>Coinvolgi altri utenti:</label>
                    <?php foreach ($utenti as $utente): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="utenti[]" value="<?= $utente['username'] ?>" id="check<?= $utente['username'] ?>">
                            <label class="form-check-label" for="check<?= $utente['username'] ?>">
                                <?= $utente['username'] ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
            <?php } ?>
            <button type="submit" class="btn btn-primary">Crea Evento</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
