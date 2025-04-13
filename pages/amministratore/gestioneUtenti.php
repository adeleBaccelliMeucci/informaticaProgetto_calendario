<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spazio aministratore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  </head>
  <body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../home.php">Home</a>
        </li>
        
      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"> <!--cerca per username DA FARE-->
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

<br>


  <?php 
    session_start();
    include '../connesione.php';
    $utenteAmministratore = false;

    if(isset($_SESSION['username']) && $_SESSION['username'] !== null){
        $nomeUtente = htmlspecialchars($_SESSION['username']);
        $sql = "SELECT amministratore from utente where username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $nomeUtente);
        $stmt->execute();
        $amministratore = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($amministratore as $utente) {
            if ($utente['amministratore']) $utenteAmministratore = true;
        }

    }
    if ($utenteAmministratore){ ?>

    <?php 
        
    $stmt = $pdo->query("SELECT * FROM utente");
    ?>

    <table class="table table-striped"> 
        <tr>
            <th>Username</th>
            <th>Amministratore</th>
            <th></th>
            <th></th>
        </tr>
        
        <?php
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . ($row['amministratore'] ? 'Sì' : 'No') . "</td>";

            // Bottone per invertire
            echo "<td>
                <form method='POST' action='cambiaAmministratore.php'>
                    <input type='hidden' name='user' value='" . $row['username'] . "'>
                    <button type='submit' class='btn btn-sm btn-warning'>Inverti</button>
                </form>
            </td>";

            // Bottone per eliminare 
            echo "<td>
                <form method='POST' action='eliminaUtente.php' onsubmit=\"return confirm('Sei sicuro di voler eliminare questo utente?');\">
                    <input type='hidden' name='user' value='" . $row['username'] . "'>
                    <button type='submit' class='btn btn-danger btn-sm'>Elimina</button>
                </form>
            </td>";

            echo "</tr>";
        }
        ?>

        
    </table>

        
    



    <?php } else {
        echo "<div class='alert alert-danger m-4'>Accesso negato. Solo gli amministratori possono visualizzare questa pagina.</div>";
    } ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  </body>
</html>