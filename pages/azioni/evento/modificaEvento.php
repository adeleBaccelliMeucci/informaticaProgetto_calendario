<?php
session_start();
include '../../connesione.php';

$messaggioErrore = "";
$messaggioSuccesso = "";

// Verifica che l'utente sia loggato
if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit();
}

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("ID evento non valido.");
    }

    $idEvento = (int)$_GET['id'];
    $username = $_SESSION['username'];

    // Verifica che l'evento esista
    $sql = "SELECT * FROM evento WHERE id = :idEvento";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idEvento' => $idEvento]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        throw new Exception("Evento non trovato.");
    }

    // Recupera i partecipanti (escluso il creatore)
    $sqlPartecipanti = "SELECT username FROM utente_evento WHERE evento_id = :idEvento AND username != :username";
    $stmtPartecipanti = $pdo->prepare($sqlPartecipanti);
    $stmtPartecipanti->execute([':idEvento' => $idEvento, ':username' => $username]);
    $partecipanti = $stmtPartecipanti->fetchAll(PDO::FETCH_COLUMN);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titolo = $_POST['titolo'];
        $descrizione = $_POST['descrizione'];
        $colore = $_POST['colore'];
        $dataInizio = $_POST['dataInizio'];
        $dataFine = $_POST['dataFine'];
        $orarioInizio = $_POST['orarioInizio'];
        $orarioFine = $_POST['orarioFine'];
        $utentiSelezionati = $_POST['utenti'] ?? [];

        if ($dataFine < $dataInizio || $orarioFine <= $orarioInizio) {
            throw new Exception("Controlla che le date e gli orari siano coerenti.");
        }

        $sqlUpdate = "UPDATE evento 
                      SET titolo = :titolo, descrizione = :descrizione, colore = :colore,
                          dataInizio = :dataInizio, dataFine = :dataFine,
                          orarioInizio = :orarioInizio, orarioFine = :orarioFine
                      WHERE id = :idEvento";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':titolo' => $titolo,
            ':descrizione' => $descrizione,
            ':colore' => $colore,
            ':dataInizio' => $dataInizio,
            ':dataFine' => $dataFine,
            ':orarioInizio' => $orarioInizio,
            ':orarioFine' => $orarioFine,
            ':idEvento' => $idEvento
        ]);

        $pdo->beginTransaction();
        try {
            $stmtDelete = $pdo->prepare("DELETE FROM utente_evento WHERE evento_id = :evento_id");
            $stmtDelete->execute([':evento_id' => $idEvento]);

            $stmtInsertOwner = $pdo->prepare("INSERT INTO utente_evento (username, evento_id) VALUES (:username, :evento_id)");
            $stmtInsertOwner->execute([':username' => $username, ':evento_id' => $idEvento]);

            foreach ($utentiSelezionati as $utente) {
                $stmtInsertUser = $pdo->prepare("INSERT INTO utente_evento (username, evento_id) VALUES (:username, :evento_id)");
                $stmtInsertUser->execute([':username' => $utente, ':evento_id' => $idEvento]);
            }

            $pdo->commit();
            $messaggioSuccesso = "Evento aggiornato con successo.";
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Errore durante l'aggiornamento dei partecipanti: " . $e->getMessage());
        }
    }
} catch (Exception $e) {
    $messaggioErrore = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Modifica Evento</h2>

    <?php if (!empty($messaggioErrore)) : ?>
        <div class="alert alert-danger"> <?= htmlspecialchars($messaggioErrore) ?> </div>
    <?php endif; ?>

    <?php if (!empty($messaggioSuccesso)) : ?>
        <div class="alert alert-success"> <?= htmlspecialchars($messaggioSuccesso) ?> </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Titolo</label>
            <input type="text" name="titolo" class="form-control" value="<?= htmlspecialchars($evento['titolo']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrizione</label>
            <textarea name="descrizione" class="form-control" rows="3"><?= htmlspecialchars($evento['descrizione']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Colore</label>
            <input type="color" name="colore" class="form-control form-control-color" value="<?= htmlspecialchars($evento['colore']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Data Inizio</label>
            <input type="date" name="dataInizio" class="form-control" value="<?= $evento['dataInizio'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Fine</label>
            <input type="date" name="dataFine" class="form-control" value="<?= $evento['dataFine'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Orario Inizio</label>
            <input type="time" name="orarioInizio" class="form-control" value="<?= $evento['orarioInizio'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Orario Fine</label>
            <input type="time" name="orarioFine" class="form-control" value="<?= $evento['orarioFine'] ?>" required>
        </div>

        <div class="mb-3">
            <?php
            $stmtUtenti = $pdo->prepare("SELECT username FROM utente WHERE username != :username");
            $stmtUtenti->execute([':username' => $username]);
            $altriUtenti = $stmtUtenti->fetchAll(PDO::FETCH_COLUMN);
            ?>
            <?php if (!empty($altriUtenti)) { ?>
                <label class="form-label">Coinvolgi altri utenti:</label>
                <?php
                foreach ($altriUtenti as $altro) {
                    $checked = in_array($altro, $partecipanti) ? 'checked' : '';
                    echo "<div class='form-check'>
                            <input class='form-check-input' type='checkbox' name='utenti[]' value='{$altro}' id='utente_{$altro}' {$checked}>
                            <label class='form-check-label' for='utente_{$altro}'>{$altro}</label>
                          </div>";
                }
                ?>
            <?php } ?>
        </div>

        <button type="submit" class="btn btn-primary">Salva modifiche</button>
        <a href="../../home.php" class="btn btn-secondary">Annulla</a>
    </form>
</div>
</body>
</html>
