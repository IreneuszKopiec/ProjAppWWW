<?php
 $nr_indeksu = 169256;
 $nrGrupy = 2;
 echo 'Ireneusz Kopiec '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
 echo 'Zastosowanie metody include() <br />';

 include 'testA.php';
 echo "A $color $fruit <br />"; 

 echo 'Zastosowanie metody require_once() <br />';

 if(require_once('testA.php')) {
    echo "A $color $fruit <br />";
 }

 echo 'Zastosowanie if elseif oraz else <br />';

 $a = 50;
 $b = 300;
 $c = 10;
 if($a>$b && $a>$c) {
    echo $a;
 } elseif($b>$a && $b>$c) {
    echo $b;
 } else {
    echo $c;
 }

 echo '<br />Zastosowanie switch ';

 $opcja = 112;
 switch($opcja) {
    case 0:
        echo '<br />'.$a+$b+$c;
        break;
    case 1:
        echo '<br />Opcja 1';
        break;
    default:
        echo '<br />Zła opcja';
 }
 echo '<br />Zastosowanie pętli while <br />';

 $i = 1;
 while ($i<10) {
    echo 'działa '.$i.' raz<br />';
    $i++;
 }

 echo 'Zastosowanie pętli for <br />';

 for($j=0;$j<=5;$j++) {
    echo 'Wynik: '.$i*$j.'<br />';
 }

 echo 'Zastosowanie zmiennej $_GET <br />';
 echo 'Hello ' . htmlspecialchars($_GET["name"]) . '!<br />';
 // działa z tym linkiem
 //http://localhost/169256projekt/labor_169256_2.php?name=Irek

 

 echo 'Zastosowanie zmiennej $_POST <br />';
 echo '<form method="POST" action="labor_169256_2.php">
  Name: <input type="text" name="number">
  <input type="submit">
</form>';
$number = $_POST['number'];
 echo $number. '!<br />';

 echo 'Zastosowanie zmiennej $SESSION <br />';
 session_start();
 $_SESSION["nowa_sesja"]= 0;
 echo $_SESSION["nowa_sesja"].'<br />';
 $_SESSION["nowa_sesja"]= 1;
 echo $_SESSION["nowa_sesja"].'<br />';
 unset($_SESSION["nowa_sesja"]);




?>
