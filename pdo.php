<?php

// potrebni podaci
$host = 'localhost';
$database = 'videoteka';
$username = 'algebra';
$password = 'algebra';
// opcionalno
$port = 3306;
$charset = 'utf8mb4';

// dodatne opcije, kako će se dohvaćati podaci (fetch - associjativno polje) i kako će se prikazivati greške (error mode - exception)
$options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

// dsn (data source name) - osnovni podaci o bazi, tip (u ovom slučaju mysql), host, naziv i (opcionalno) port, charset
$dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

// konekcija sa dsn, username, password i dodatnim opcijama (dohvat podataka, greške i slično)
$pdo = new PDO($dsn, $username, $password, $options);

// zatvaranje konekcije (u biti nuliranje varijable koja sadrži konekciju na bazu)
$pdo = NULL;