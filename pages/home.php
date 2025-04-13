<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php
    session_start();
    echo "<h4> Benvenuto " . htmlspecialchars($_SESSION['username']) . "</h4>";

    
?>





<?php
$month = 4;
$year = 2025;
$firstDay = new DateTime("$year-$month-01");
$daysInMonth = $firstDay->format('t');
$startDayOfWeek = (int)$firstDay->format('N'); // 1 (Lun) - 7 (Dom)
?>

<table border="3" cellpadding="10">
    <caption><?= $firstDay->format('F Y') ?></caption>
    <tr>
        <th>Lun</th><th>Mar</th><th>Mer</th><th>Gio</th><th>Ven</th><th>Sab</th><th>Dom</th>
    </tr>
    <tr>
    <?php
    $currentDay = 1;
    $dayOfWeek = 1;

    // Celle vuote prima del primo giorno del mese
    while ($dayOfWeek < $startDayOfWeek) {
        echo "<td></td>";
        $dayOfWeek++;
    }

    // Stampa i giorni del mese
    while ($currentDay <= $daysInMonth) {
        echo "<td>$currentDay</td>";
        if ($dayOfWeek % 7 == 0) echo "</tr><tr>"; // nuova riga ogni domenica
        $currentDay++;
        $dayOfWeek++;
    }

    // Celle vuote dopo l'ultimo giorno del mese
    while ($dayOfWeek <= 7) {
        echo "<td></td>";
        $dayOfWeek++;
    }
    ?>
    </tr>
</table>


</body>
</html>