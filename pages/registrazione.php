<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>

    <?php
    session_start();
    include 'connesione.php'; // Connessione al DB

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        
        // Hash della password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO utente (username, password, amministratore) VALUES (:username, :password, FALSE)");
            $stmt->execute(['username' => $username, 'password' => $hashed_password]);

            $_SESSION["username"] = $username;

            $showModal = true;
            
        } catch (PDOException $e) {
            echo "Errore: " . $e->getMessage();
            echo "<br> utente già esistente";
        }
    }
    ?>

    <!-- Form di registrazione -->
    <form method="post">
        <input type="text"  name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit"  >Registrati</button>
    </form>

    <!-- Modal -->
    <?php if (isset($showModal) && $showModal): ?>
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Registrazione completata</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        La registrazione è avvenuta con successo!
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="home.php" class="btn btn-primary">Vai alla Home</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

    <script>
        // Mostra il modal quando la pagina è caricata
        <?php if (isset($showModal) && $showModal): ?>
            var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'), {
                keyboard: false
            });
            myModal.show();
        <?php endif; ?>
    </script>

</body>
</html>
