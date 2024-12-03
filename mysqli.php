<?php

$hostname = "localhost";
$username = "algebra";
$password = "algebra";
$database = "videoteka";
// opcionalno
$port = 3306;

// stvaranje konekcije na bazu
$connection = mysqli_connect($hostname, $username, $password, $database, $port);

// provjera je li sve prošlo bez greške
if (mysqli_connect_errno()) {
    die("Pogreška kod spajanja na poslužitelj: " . mysqli_connect_error());
}
echo "Spojeni ste na bazu";

// zatvaranje konekcije
mysqli_close($connection);
