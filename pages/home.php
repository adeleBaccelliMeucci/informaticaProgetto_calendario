<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>

<?php 
session_start();
include 'connesione.php';

$utenteAmministratore = false;
if (isset($_SESSION['username']) && $_SESSION['username'] !== null) {
    $nomeUtente = htmlspecialchars($_SESSION['username']);
    $sql = "SELECT amministratore FROM utente WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $nomeUtente);
    $stmt->execute();
    $amministratore = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($amministratore as $utente) {
        if ($utente['amministratore']) $utenteAmministratore = true;
    }
}
?>

<?php
$eventi = [];

if (isset($_SESSION['username']) && $_SESSION['username'] !== null) {
    $sqlEventi = "
        SELECT e.id, e.titolo, e.descrizione, e.colore, e.orarioInizio, e.orarioFine, e.dataInizio, e.dataFine
        FROM evento e
        INNER JOIN utente_evento ue ON e.id = ue.evento_id
        WHERE ue.username = :username
        ORDER BY e.dataInizio, e.orarioInizio
    ";

    $stmtEventi = $pdo->prepare($sqlEventi);
    $stmtEventi->bindParam(':username', $_SESSION['username']);
    $stmtEventi->execute();
    $eventi = $stmtEventi->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php
$todoList = [];

if (isset($_SESSION['username']) && $_SESSION['username'] !== null) {
    $sqlTodo = "SELECT titolo, urgenza, giorno 
                FROM toDoList 
                WHERE username = :username 
                ORDER BY giorno ASC, urgenza DESC";
    
    $stmtTodo = $pdo->prepare($sqlTodo);
    $stmtTodo->bindParam(':username', $_SESSION['username']);
    $stmtTodo->execute();
    $todoList = $stmtTodo->fetchAll(PDO::FETCH_ASSOC);
}
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" 
           <?php if (isset($_SESSION['username']) && $utenteAmministratore) { ?> 
               href="amministratore/gestioneUtenti.php" 
           <?php } ?>>
           Ciao <?php echo isset($_SESSION["username"]) ? $_SESSION["username"] : "unknown"; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="../index.php">Exit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        New
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="azioni/evento/newEvento.php">Evento</a></li>
                        <li><a class="dropdown-item" href="#">To do list</a></li>
                        <li><a class="dropdown-item" href="#">Etichetta</a></li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid mt-3">
    <div class="row">

        <!-- Eventi -->
        <div class="col-md-9">
            <div class="border border-primary p-3 rounded" style="min-height: 70vh;">
                <?php if (empty($eventi)): ?>
                    <h2 class="text-primary">Nessun evento disponibile</h2>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($eventi as $evento): ?>
                            <li class="list-group-item mb-2" style="border-left: 5px solid <?= htmlspecialchars($evento['colore']) ?>;">
                                <h5><?= htmlspecialchars($evento['titolo']) ?></h5>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($evento['descrizione'])) ?></p>
                                <small>
                                    Dal <?= $evento['dataInizio'] ?> ore <?= substr($evento['orarioInizio'], 0, 5) ?> 
                                    al <?= $evento['dataFine'] ?> ore <?= substr($evento['orarioFine'], 0, 5) ?>
                                </small>
                                <div class="mt-2">
                                    <a href="azioni/evento/modificaEvento.php?id=<?= htmlspecialchars($evento['id']) ?>" class="btn btn-sm btn-warning">Modifica</a>
                                    <a href="azioni/evento/eliminaEvento.php?id=<?= htmlspecialchars($evento['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo evento?');">Elimina</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- To-Do List -->
        <div class="col-md-3">
            <div class="border border-success p-3 rounded" style="min-height: 70vh;">
                <?php if (empty($todoList)): ?>
                    <h2 class="text-success">Nessuna to-do list trovata</h2>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($todoList as $todo): ?>
                            <li class="list-group-item mb-2">
                                <h6><?= htmlspecialchars($todo['titolo']) ?></h6>
                                <p class="mb-1">
                                    Giorno: <?= $todo['giorno'] ?><br>
                                    Urgenza: <strong><?= $todo['urgenza'] ?>/10</strong>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
