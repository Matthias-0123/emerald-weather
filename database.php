
<?php

function maakDatabaseVerbinding(): PDO
{
    $database = new PDO("sqlite:" . __DIR__ . "/weather.db");

    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $database->exec("
        CREATE TABLE IF NOT EXISTS voorspellingen (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            datum TEXT NOT NULL,
            max_temp REAL,
            min_temp REAL,
            regen REAL,
            wind REAL,
            opgehaald_op TEXT NOT NULL
        )
    ");

    return $database;
}
