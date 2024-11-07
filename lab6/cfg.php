<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';

$link = new mysqli($dbhost, $dbuser, $dbpass, $baza);
if (!$link) echo '<b>przerwane połączenie</b>';
if (!mysqli_select_db($link, $baza)) echo 'nie wybrano bazy';
?>