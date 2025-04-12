<?php
require 'connesione.php'; // Connessione al DB

// Se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    try {
        // Prepariamo la query per recuperare la password hashata dell'utente
        $stmt = $pdo->prepare("SELECT username, password FROM utente WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verifica se l'utente esiste e se la password è corretta
        if ($user && password_verify($password, $user['password'])) {
            // Successo: Login riuscito
            echo "Login riuscito! Benvenuto, " . htmlspecialchars($username);
            
            // Avvia la sessione e memorizza l'utente
            session_start();
            $_SESSION["username"] = $user["username"]; // Usa username come identificativo
            //echo " ID utente: " . htmlspecialchars($_SESSION["username"]);

            //REINDIRIZZARE ALLA HOME
        } else {
            echo "Credenziali errate!";
        }
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>

    <!-- Form di login -->
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Accedi</button>
    </form>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</body>
</html>
