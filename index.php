<?php

require_once __DIR__ . "/database.php";

// Nederlandse tijdzone instellen
date_default_timezone_set("Europe/Amsterdam");

// Verbinding maken met de database
$database = maakDatabaseVerbinding();

// Weersvoorspelling ophalen voor Leiderdorp
$url = "https://api.open-meteo.com/v1/forecast?latitude=52.16&longitude=4.53&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,wind_speed_10m_max&timezone=Europe%2FAmsterdam";


// Probeert de weersgegevens op te halen via de API
$response = @file_get_contents($url);

if ($response === false) {
    die("De weergegevens konden niet worden opgehaald. Probeer het later opnieuw.");
}

// Zet de JSON-gegevens om naar een PHP-array
$data = json_decode($response, true);

// Controleert of de API geldige gegevens heeft teruggestuurd
if (
    json_last_error() !== JSON_ERROR_NONE ||
    !isset(
        $data["daily"]["time"],
        $data["daily"]["temperature_2m_max"],
        $data["daily"]["temperature_2m_min"],
        $data["daily"]["precipitation_sum"],
        $data["daily"]["wind_speed_10m_max"]
    )
) {
    die("De API heeft ongeldige of onvolledige weergegevens teruggestuurd.");
}


// Gegevens uit de API halen
$dagen = $data["daily"]["time"];
$maximumTemperaturen = $data["daily"]["temperature_2m_max"];
$minimumTemperaturen = $data["daily"]["temperature_2m_min"];

// Voorspellingen tonen
for ($i = 0; $i < count($dagen); $i++) {
    echo "Datum: " . $dagen[$i] . "<br>";
    echo "Maximum temperatuur: " . $maximumTemperaturen[$i] . " °C<br>";
    echo "Minimum temperatuur: " . $minimumTemperaturen[$i] . " °C<br>";
    echo "<hr>";
}

// Controleren of een voorspelling vandaag al is opgeslagen
$controle = $database->prepare("
    SELECT COUNT(*)
    FROM voorspellingen
    WHERE datum = ?
    AND DATE(opgehaald_op) = ?
");

// Voorspelling opslaan
$statement = $database->prepare("
    INSERT INTO voorspellingen
    (datum, max_temp, min_temp, regen, wind, opgehaald_op)
    VALUES (?, ?, ?, ?, ?, ?)
");

$vandaag = date("Y-m-d");
$ophaalMoment = date("Y-m-d H:i:s");

$aantalOpgeslagen = 0;

// Alle voorspellingen verwerken
for ($i = 0; $i < count($dagen); $i++) {

    $controle->execute([
        $dagen[$i],
        $vandaag
    ]);

    // Alleen opslaan als deze voorspelling vandaag nog niet bestaat
    if ($controle->fetchColumn() == 0) {

        $statement->execute([
            $dagen[$i],
            $maximumTemperaturen[$i],
            $minimumTemperaturen[$i],
            $data["daily"]["precipitation_sum"][$i],
            $data["daily"]["wind_speed_10m_max"][$i],
            $ophaalMoment
        ]);

        $aantalOpgeslagen++;
    }
}

echo "<h3>" . $aantalOpgeslagen . " nieuwe voorspellingen opgeslagen.</h3>";

echo '<a href="overzicht.php">Bekijk het weeroverzicht</a>';
