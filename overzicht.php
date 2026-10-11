
<?php
require_once __DIR__ . "/database.php";

$database = maakDatabaseVerbinding();

// Haalt alle datums op waarop we voorspellingen hebben opgeslagen
$datums = $database->query("
    SELECT DISTINCT DATE(opgehaald_op)
    FROM voorspellingen
    ORDER BY DATE(opgehaald_op) DESC
")->fetchAll(PDO::FETCH_COLUMN);

$gekozenDatum = $_GET["datum"] ?? ($datums[0] ?? "");

// Haalt de voorspellingen op van de gekozen datum
$statement = $database->prepare("
    SELECT *
    FROM voorspellingen
    WHERE DATE(opgehaald_op) = ?
    ORDER BY opgehaald_op ASC, datum ASC, id ASC
");

$statement->execute([$gekozenDatum]);

$voorspellingen = $statement->fetchAll(PDO::FETCH_ASSOC);

$vorigeTemperatuur = null;
$vorigOphaalmoment = null;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast - Leiderdorp</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Weather Forecast</h1>
<h2>Leiderdorp</h2>

<!-- Hiermee kun je kiezen van welke dag je de voorspellingen wilt zien -->
<form method="GET" class="datum-filter">
    <label for="datum">Voorspellingen opgehaald op:</label>

    <select name="datum" id="datum">
        <?php foreach ($datums as $datum): ?>
            <option
                    value="<?= htmlspecialchars($datum) ?>"
                <?= $datum === $gekozenDatum ? "selected" : "" ?>
            >
                <?= date("d-m-Y", strtotime($datum)) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Bekijken</button>
</form>

<table>
    <tr>
        <th>Datum</th>
        <th>Opgehaald op</th>
        <th>Maximum</th>
        <th>Minimum</th>
        <th>Regen</th>
        <th>Wind</th>
        <th>Trend</th>
        <th>Weersymbolen</th>
    </tr>

    <?php foreach ($voorspellingen as $voorspelling): ?>

        <?php
        $huidigeTemperatuur = $voorspelling["max_temp"];
        $ophaalMoment = $voorspelling["opgehaald_op"];

        $trend = "—";

        // Vergelijkt de temperatuur met de vorige dag uit dezelfde voorspelling
        if ($vorigeTemperatuur !== null && $vorigOphaalmoment === $ophaalMoment) {

            if ($huidigeTemperatuur > $vorigeTemperatuur) {
                $trend = '<img src="images/happy.svg" alt="Stijgende temperatuur" class="weather-icon">';
            } elseif ($huidigeTemperatuur < $vorigeTemperatuur) {
                $trend = '<img src="images/sad.svg" alt="Dalende temperatuur" class="weather-icon">';
            }
        }

        $vorigeTemperatuur = $huidigeTemperatuur;
        $vorigOphaalmoment = $ophaalMoment;

        $symbolen = [];

        // Laat afbeeldingen zien als bepaalde weersomstandigheden voorkomen
        if ($voorspelling["max_temp"] > 40 || $voorspelling["min_temp"] < 10) {
            $symbolen[] = '<img src="images/thermometer.svg" alt="Extreme temperatuur" class="weather-icon">';
        }

        if ($voorspelling["wind"] >= 50) {
            $symbolen[] = '<img src="images/hurricane.svg" alt="Sterke wind" class="weather-icon">';
        }

        if ($voorspelling["regen"] > 0) {
            $symbolen[] = '<img src="images/swimmer.svg" alt="Regen" class="weather-icon">';
        }
        ?>

        <tr>
            <td><?= date("d-m-Y", strtotime($voorspelling["datum"])) ?></td>
            <td><?= date("d-m-Y H:i", strtotime($voorspelling["opgehaald_op"])) ?></td>
            <td><?= htmlspecialchars((string)$voorspelling["max_temp"]) ?> °C</td>
            <td><?= htmlspecialchars((string)$voorspelling["min_temp"]) ?> °C</td>
            <td><?= htmlspecialchars((string)$voorspelling["regen"]) ?> mm</td>
            <td><?= htmlspecialchars((string)$voorspelling["wind"]) ?> km/u</td>
            <td><?= $trend ?></td>
            <td><?= implode(" ", $symbolen) ?></td>
        </tr>

    <?php endforeach; ?>

</table>

</body>
</html>
