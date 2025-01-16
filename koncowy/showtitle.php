<?php
//Funkcja pokazująca tytuł strony
function Title($id) {
    //Dołączenie pliku php odpowiedzialnego za połączenie z bazą
    include 'cfg.php';
    //Zabezpieczenie zmiennej przekazanej w argumencie
    $id_clear = htmlspecialchars($id);

    //Utworzenie zapytania sql
    $querry = "SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1";
    //Wykonanie zapytania sql
    $result = mysqli_query($link,$querry);
    //Zwrócenie wyniku
    $row = mysqli_fetch_assoc($result);

    if(empty($row['id'])) {
        echo 'nie_znaleziono_strony';
    } else {
        echo $row['page_title'];
    }
}
?>