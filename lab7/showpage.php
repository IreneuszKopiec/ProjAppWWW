<?php

function PokazPodstrone($id) {
    include 'cfg.php';
    $id_clear = htmlspecialchars($id);

    $result = 
      $link->query("SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1");
    
    $web = 'nie znaleziono strony';
    while($record = mysqli_fetch_array($result)) {
        $web = $record;
    }
    
    return $web[2];
}



?>