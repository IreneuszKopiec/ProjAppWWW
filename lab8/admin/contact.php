<?php
include("../cfg.php");




function PokazKontakt() {
    $show = '<form method="POST" action="">
    [temat]: <input type="text" name="temat" required><br><br>
    [treść]:<textarea name="tresc" rows="4" cols="50" required>Tu wpisz swoja wiadomosc.</textarea><br><br>
    [email]:<input type="email" name="email" required><br><br>
    <input type="submit" name="formularzKontaktowy" value="Wyślij wiadomość">
    </form>
    ';

    return $show;
}

function WyslijMailaKontakt($odbiorca) {
    if(empty($_POST['temat']) || 
       empty($_POST['tresc']) || 
       empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt();
    } else {
        $mail['subject'] = $_POST['temat'];
        $mail['body'] = $_POST['tresc'];
        $mail['sender'] = $_POST['email'];
        $mail['reciptient'] = $odbiorca;

        $header = "From: Formularz kontaktowy <".$mail['sender'].">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";
        $header .= "X-Sender: <".$mail['sender'].">\n";
        $header .= "X-Mailer: PRapWWW mail 1.2\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <".$mail['sender'].">\n";

        mail($mail['reciptient'], $mail['subject'], $mail['body'], $header);

        
        header("Location: ?");
        echo '[wiadomosc_wyslana]';
    }
}


function PrzypomnijHaslo($odbiorca, $pass) {
    $mail['subject'] = "Przypomnienie hasla";
    $mail['body'] = "Twoje haslo to: ".$pass;
    $mail['reciptient'] = $odbiorca;

    $header = "From: Formularz kontaktowy <email@gamil.com>\n";
    $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";
    $header .= "X-Mailer: PHP/".phpversion()."\n";
    $header .= "X-Priority: 3\n";

    mail($mail['reciptient'], $mail['subject'], $mail['body'], $header);

    header("Location: ?");
    echo '[wysłano_przypomnienie]';
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['formularzKontaktowy'])) {
        WyslijMailaKontakt("kopiecira@gmail.com");
    }
    elseif(isset($_POST['przypomnijHaslo'])) {
        PrzypomnijHaslo("kopiecira@gmail.com", $pass);
    }

} else {
    echo '<h1>Wyślij maila</h1>';
    echo PokazKontakt();

    echo '
    <h1>Przypomnij haslo</h1>
    <form method="POST" action="">
    [email]:<input type="email" name="email" required><br><br>
    <input type="submit" name="przypomnijHaslo" value="Przypomnij hasło">
    </form>
    ';

}

?>