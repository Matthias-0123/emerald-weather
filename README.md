# Weather Forecast – Leiderdorp

Een eenvoudige weerapplicatie gemaakt met PHP voor de programmeeropdracht van Emerald IT.

## Functionaliteiten

- Weersvoorspellingen voor Leiderdorp ophalen via Open-Meteo.
- Voorspellingen opslaan in een SQLite-database.
- Eerder opgeslagen voorspellingen bekijken via een datumfilter.
- Temperatuurtrends tonen met afbeeldingen.
- Afbeeldingen tonen bij extreme temperaturen, sterke wind en regen.
- Dubbele voorspellingen op dezelfde ophaaldag voorkomen.

## Gebruikte technieken

- PHP
- HTML en CSS
- SQLite
- Open-Meteo API

## Applicatie starten

1. Zorg dat PHP met de SQLite-extensie geïnstalleerd is.
2. Open een terminal in de projectmap.
3. Start de PHP-server met:

   `php -S localhost:8000`

4. Open `http://localhost:8000/index.php` om weersvoorspellingen op te halen en op te slaan.
5. Open `http://localhost:8000/overzicht.php` om de opgeslagen voorspellingen te bekijken.

## Technische keuzes

Ik heb Open-Meteo gekozen omdat de API zonder persoonlijke API-key gebruikt kan worden. SQLite is gekozen omdat het eenvoudig is en geen aparte databaseserver nodig heeft.

De applicatie bewaart maximaal één voorspelling per voorspelde datum per ophaaldag. Zo kunnen voorspellingen van verschillende dagen met elkaar worden vergeleken.

Voor sterke wind gebruik ik een grens van 50 km/u. Bij temperaturen boven 40°C of onder 10°C verschijnt een thermometer.

De voorspellingen worden bijgewerkt wanneer `index.php` wordt uitgevoerd.

