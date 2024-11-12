<?php
 error_reporting(E_ALL^E_NOTICE^E_WARNING);
 /* po tym komentarzu będzie kod do dynamicznego ładowania stron */
 
 include 'cfg.php';
 include 'showpage.php';
 include 'showtitle.php';

 $idp = $_GET['idp'];
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="pl" />
    <meta name="Author" content="Ireneusz Kopiec" />
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Największe mosty świata v1.4</title>
  </head>
  <body onload="startclock()">
    <header>
      <nav>

          <ul class="nav-list">
                   
          <li><a href="index.php?idp=1" class="list-item">Home</a></li>
          <li><a href="index.php?idp=2" class="list-item">Najwyższe</a></li>
          <li><a href="index.php?idp=3" class="list-item">Najdłuższe</a></li>
          <li><a href="index.php?idp=4" class="list-item">Galeria</a></li>
          <li><a href="index.php?idp=5" class="list-item">Ciekawostki</a></li>
          <li><a href="index.php?idp=6" class="list-item">Kontakt</a></li>
          <li><a href="index.php?idp=7" class="list-item">testJS</a></li>
          <li><a href="index.php?idp=8" class="list-item">Filmy</a></li>

        </ul>
      </nav>

      <h1 class="header-title" id="header-title" onclick="changeBackground()">
        <?php
        Title($idp);
        ?>
      </h1>
    </header>

    <main>
      <?php
        PokazPodstrone($idp);
      ?>
    </main>

    <footer>
      <div class="section-text">
        <div id="zegarek"></div>
        <div id="data"></div>
      </div>

      <p class="section-text">Copyright: &copy; Ireneusz Kopiec</p>
    </footer>

    <script>
      const scale1 = 1.05;
      const scale2 = 1;

      $(".picture").on("mouseover", function () {
        $(this).css({
          transform: `scale(${scale1})`,
          transition: "transform 0.1s ease",
        });
      });

      $(".picture").on("mouseout", function () {
        $(this).css({
          transform: `scale(${scale2})`,
          transition: "transform 0.1s ease",
        });
      });

      $(".gallery-img").on("mouseover", function () {
        $(this).css({
          transform: `scale(${scale1})`,
          transition: "transform 0.1s ease",
        });
      });

      $(".gallery-img").on("mouseout", function () {
        $(this).css({
          transform: `scale(${scale2})`,
          transition: "transform 0.1s ease",
        });
      });

    </script>



      


    <?php
 $nr_indeksu = 169256;
 $nrGrupy = 2;
 echo 'Ireneusz Kopiec '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
?>

  </body>
</html>
