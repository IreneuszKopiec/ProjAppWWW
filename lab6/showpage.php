<?php

function PokazPodstrone($id) {
    include 'cfg.php';

    $id_clear = htmlspecialchars($id);

    $querry = "SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1";
    $result = mysqli_query($link,$querry);
    $row = mysqli_fetch_array($result);

    if(empty($row['id'])) {
        $web = 'nie_znaleziono_strony';
    } else {
        $web = $row['page_content'];
    }

    echo $web;


    return $web;
}



?>