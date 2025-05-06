<?php
session_start();
if (isset($_POST['cancella'])) {
    unset($_SESSION['username']);
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    
    <style>
        div{
            /*border-style: solid;*/
            margin-top: 30px;
            margin-bottom: 30px;
            padding-top: 30px;
            padding-bottom: 30px;
        }
    </style>
    
</head>
<body class="container mt-5">

    <div class="bg-light text-center">
        
        <div>
            <a href="pages/login.php">
                <button type="button" class="btn btn-primary btn-lg">login</button>
            </a>
            <br><br>
            <a href="pages/registrazione.php">
                <button type="button" class="btn btn-outline-primary btn-lg">registrati</button>
            </a>
        </div>

        <?php
            if(isset($_SESSION['username']) && $_SESSION['username'] !== null){
                ?>
                <div>
                    <a href="pages/home.php"> <button type="button" class="btn btn-success btn-lg">home</button> </a>
                    <br><br>
                    <form method="POST">
                        <button type="submit" name="cancella" class="btn btn-danger" >esci dall' account <?php htmlspecialchars($_SESSION["username"]) ?> </button>
                    </form>
                </div>
                
                

                <?php
            }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>