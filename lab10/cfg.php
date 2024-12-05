<?php
//Dane do połączenia z bazą 
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';

//Dane logowania
$login = 'irek';
$pass = '123';

//nawiązywanie połączenia
$link = new mysqli($dbhost, $dbuser, $dbpass, $baza);

//Obsługa wyjątków
if (!$link) echo '<b>przerwane połączenie</b>';
if (!mysqli_select_db($link, $baza)) echo 'nie wybrano bazy';
?>