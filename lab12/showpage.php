<?php

//Funkcja pokazująca treść strony
function PokazPodstrone($id) {
  //Dołączenie pliku php odpowiedzialnego za połączenie z bazą
  include 'cfg.php';
  //Zabezpieczenie zmiennej przekazanej w argumencie
  $id_clear = htmlspecialchars($id);

  //Wykonanie zapytania sql
  $result = 
    $link->query("SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1");
  
  //Domyślnie strona nie jest znaleziona
  $web = 'nie znaleziono strony';
  while($record = mysqli_fetch_array($result)) {
    $web = $record; //jeśli jest wynik to strona jest znaleziona
  }
  //Zwrócenie treści strony
  return $web[2];
}



?>