<?php
//Dołączenie pliku php odpowiedzialnego za połączenie z bazą
include("../cfg.php");

//Rozpoczęcie sesji
session_start();

//Funkcja pokazująca formularz logowania
//pokazująca informację o błędnych danych
function FormularzLogowania($error = '') {
    $wynik = '
    <div style="background-color:white;paddding: 5px;margin:20px: width:80%;text-align: center">
      <h1>Panel CMS:</h1>
      <div style="background-color:white;paddding: 5px;margin:20px: width:80%;text-align: center">
      '.($error ? '<p class="error">'.$error.'</p>' : '').'
      <center>
        <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
          <table style="background-color:white;paddding: 5px;margin:20px: width:80%">
            <tr style="border:1px solid black; text-align:center;"><td class="log4_t">[email]</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
            <tr style="border:1px solid black; text-align:center;"><td class="log4_t">[haslo]</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
            <tr style="border:1px solid black; text-align:center;"><td>&nbsp</td><td><input type="submit" name="x1_submit" class="logowanie" value="zaloguj" /></td></tr>
          </table>
        </form>
        </center>
      </div>
    </div>
    ';

    echo $wynik;
}

//Sprawdzenie czy login i hasło jest poprawne
if(isset($_SERVER['REQUEST_URI']) && isset($_POST['login_email']) && isset($_POST['login_pass'])) {
    if($_POST['login_email'] === $login && $_POST['login_pass'] === $pass) {
      //Użytkownik jest zalogowany
      $_SESSION['login'] = true;
    } else {
      FormularzLogowania('Błąd logowania. Sprawdź swoje dane.');
      $_SESSION['login'] = false;
      //Zakończenie skryptu
      exit();
    }
}


//Wyświetlanie listy podstron
function listaPodstron($link) {
  //Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if(!$_SESSION['login']) {
    FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
    return;
  }
  //Wykonanie zapytania sql
  $result = $link->query("SELECT id, page_title FROM page_list LIMIT 10");

  echo "<div style='background-color:lightgray'>";
  echo "<center>";
  echo "<a href='?add_new=true'>Dodaj nową podstronę</a>";
  echo "<table style='border-collapse:collapse; border:1px solid black;'>";
  echo "<tr style='background-color:gray'><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>";

  //Pętla pomagająca stworzyć wiersze i kolumny
  while ($row = $result->fetch_array()) {
    echo "<tr style='background-color:white'>
            <td>{$row['id']}</td>
            <td>{$row['page_title']}</td>
            <td>
                <a href='?edit_id={$row['id']}'>Edytuj</a> | 
                <a href='?delete_id={$row['id']}'>Usuń</a>
            </td>
          </tr>";
  }
  echo "</table>";
  echo "</center>";
  echo "<hr>";
  echo "</div>";
}


//Funkcja do edytowania podstrony
function EdytujPodstrone($id,$link) {
  //Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if(!$_SESSION['login']) {
    FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
    return;
  }
  //Zabezpieczenie zmiennej przekazanej w argumencie
  $id_clear = htmlspecialchars($id);
  //Wykonanie zapytania sql
  $result = $link->query("SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1");

  
  $row = $result->fetch_assoc();  //Zwrócenie wyniku
  $title = $row['page_title'];
  $content = $row['page_content'];
  $active = $row['status'];

  //Formularz do edycji strony
  echo "
  <div style='background-color:lightgray;text-align:center'>
    <h2>Edytuj podstronę</h2>
    <form method='post' action=''>
        <label for='page_title'>Tytuł:</label><br>
        <input type='text' name='page_title' id='page_title' value='".htmlspecialchars($title, ENT_QUOTES)."' required><br><br>

        <label for='page_content'>Treść:</label><br>
        <textarea name='page_content' id='page_content' rows='10' cols='50'>".htmlspecialchars($content, ENT_QUOTES)."</textarea><br><br>

        <label>
            <input type='checkbox' name='active' ".($active ? "checked" : "")."> Aktywna
        </label><br><br>

        <button type='submit' name='save_changes'>Zapisz zmiany</button>
    </form>
    <hr>
  </div>
  ";

    //Po zapisaniu zmian należy zaktualizować baze danych
    if (isset($_POST['save_changes'])) {
      $new_title = $_POST['page_title'];
      $new_content = $_POST['page_content'];
      $new_active = isset($_POST['active']) ? 1 : 0;
  
      //Przygotowanie zapytania
      $stmt = $link->prepare("UPDATE page_list SET page_title = ?, page_content = ?, status = ? WHERE id = ? LIMIT 1");
      //Przygotowanie parametrów zapytania
      $stmt->bind_param("ssii", $new_title, $new_content, $new_active, $id_clear);

      //Wykonanie zapytania
      if ($stmt->execute()) {
        //Odświeżenie strony
        header("Location: ?");
        //Zakończenie skryptu
        exit();
      } else {
        echo "<p>Błąd podczas aktualizacji podstrony: " . $stmt->error . "</p>";
      }
    }
}

//Funkcja do dodania nowej podstrony
function DodajNowaPodstrone($link) {
  //Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
  }

  //Po dodaniu postrony należy zaktualizować baze danych
  if (isset($_POST['save_new_page'])) {
      $new_title = $_POST['page_title'];
      $new_content = $_POST['page_content'];
      $new_active = isset($_POST['active']) ? 1 : 0;

      //Przygotowanie zapytania
      $stmt = $link->prepare("INSERT INTO page_list (page_title, page_content, status) VALUES (?, ?, ?)");
      //Przygotowanie parametrów zapytania
      $stmt->bind_param("ssi", $new_title, $new_content, $new_active);

      //Wykonanie zapytania
      if ($stmt->execute()) {
        //Odświeżenie strony
        header("Location: ?");
        //Zakończenie skryptu
        exit();
      } else {
        echo "<p>Błąd podczas dodawania podstrony: " . $stmt->error . "</p>";
      }
      //Zamyka przygotowane zapyanie
      $stmt->close();  
  }

  //Formularz do dodania podstrony
  echo "
  <div style='background-color:lightgray;text-align:center'>
      <h2>Dodaj nową podstronę</h2>
      <form method='post' action=''>
          <label for='page_title'>Tytuł:</label><br>
          <input type='text' name='page_title' id='page_title' required><br><br>

          <label for='page_content'>Treść:</label><br>
          <textarea name='page_content' id='page_content' rows='10' cols='50' required></textarea><br><br>

          <label>
              <input type='checkbox' name='active'> Aktywna
          </label><br><br>

          <button type='submit' name='save_new_page'>Zapisz nową podstronę</button>
      </form>
      <hr>
  </div>
  ";
}

//Funkcja do usuwania podstrony
function UsunPodstrone($id, $link) {
  //Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
  }

  //Przygotowanie zapytania
  $stmt = $link->prepare("DELETE FROM page_list WHERE id = ? LIMIT 1");
  //Przygotowanie parametrów zapytania
  $stmt->bind_param("i", $id); 

  //Wykonanie zapytania
  if ($stmt->execute()) {
    //Odświeżenie strony
    header("Location: ?");
    //Zakończenie skryptu
    exit();
  } else {
    echo "<p>Błąd podczas usuwania podstrony: " . $stmt->error . "</p>";
  }
  //Zamyka przygotowane zapyanie
  $stmt->close();
}


// Funkcja pokazania listy kategorii
function PokazKategorie($mother=0, $link, $level=2) {
  // Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
  }
  
  // Wykonanie zapytania sql
  $result = $link->query("SELECT id, matka, nazwa FROM kategoria WHERE matka ='$mother' LIMIT 10");

  if ($result->num_rows > 0) {
      echo "<div>";
      echo "<ul>"; // Rozpocznij listę unordered

      // Pętla pomagająca stworzyć wiersze i kolumny
      while ($row = $result->fetch_array()) {
          echo "<li style='padding-left:".($row['id'] == 0 ? $level * 50 : 0)."px'>{$row['id']}. {$row['nazwa']}</li>
                <ul>";

          PokazKategorie($row['id'], $link, $level = $level + 1); // Rekursywnie wyświetl podkategorie

          echo "</ul>"; // Zamknij zagnieżdżoną listę
      }

      echo "</ul>"; // Zamknij główną listę
      
      echo "</div>";
  }
}





//Funkcja do edytowania kategorii
function EdytujKategorie($id,$link) {
  //Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if(!$_SESSION['login']) {
    FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
    return;
  }
  //Zabezpieczenie zmiennej przekazanej w argumencie
  $id_clear = htmlspecialchars($id);
  //Wykonanie zapytania sql
  $result = $link->query("SELECT matka, nazwa FROM kategoria WHERE id='$id_clear' LIMIT 1");

  
  if ($result && $result->num_rows > 0) {
    // Zwrócenie wyniku
    $row = $result->fetch_assoc();
    $mother = $row['matka'];
    $name = $row['nazwa'];
  } else {
    // Jeśli brak wyników
    echo "<p>Nie znaleziono kategorii o podanym ID.</p>";
    return; // Zakończenie funkcji, jeśli brak wyników
  }

  //Formularz do edycji strony
  echo "
    <h2>Edytuj podstronę</h2>
    <form method='post' action=''>
        <label for='mother'>Matka:</label><br>
        <input type='number' name='mother' id='mother' value='".htmlspecialchars($mother, ENT_QUOTES)."' required><br><br>

        <label for='name'>Nazwa:</label><br>
        <textarea name='name' id='name' rows='10' cols='50'>".htmlspecialchars($name, ENT_QUOTES)."</textarea><br><br>

        <button type='submit' name='save_changes2'>Zapisz zmiany</button>
    </form>
    <hr>
    ";

    //Po zapisaniu zmian należy zaktualizować baze danych
    if (isset($_POST['save_changes2'])) {
      $new_mother = $_POST['mother'];
      $new_name = $_POST['name'];
  
      //Przygotowanie zapytania
      $stmt = $link->prepare("UPDATE kategoria SET matka = ?, nazwa = ? WHERE id = ? LIMIT 1");
      //Przygotowanie parametrów zapytania
      $stmt->bind_param("isi", $new_mother, $new_name, $id_clear);

      //Wykonanie zapytania
      if ($stmt->execute()) {
        //Odświeżenie strony
        header("Location: ?");
        //Zakończenie skryptu
        exit();
      } else {
        echo "<p>Błąd podczas aktualizacji podstrony: " . $stmt->error . "</p>";
      }
    }
}




//Funkcja do dodania kategorii
function DodajKategorie($link) {

    //Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
    if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
    }
  

    echo "
      <h2>Dodaj nową kategorie</h2>
      <form method='post' action=''>
          <label for='matka'>Matka:</label><br>
          <input type='number' name='matka' id='matka' required><br><br>

          <label for='nazwa'>Treść:</label><br>
          <textarea name='nazwa' id='nazwa' rows='10' cols='50' required></textarea><br><br>

          <button type='submit' name='save_category'>Zapisz nową kategorie</button>
      </form>
      <hr>
    ";

    //Po dodaniu kategori należy zaktualizować baze danych
    if (isset($_POST['save_category'])) {
      $new_mother = $_POST['matka'];
      $new_name = $_POST['nazwa'];
  
      //Przygotowanie zapytania
      $stmt = $link->prepare("INSERT INTO kategoria (matka, nazwa) VALUES (?, ?)");
      //Przygotowanie parametrów zapytania
      $stmt->bind_param("is", $new_mother, $new_name);
  
      //Wykonanie zapytania
      if ($stmt->execute()) {
        //Odświeżenie strony
        header("Location: ?");
        //Zakończenie skryptu
        exit();
      } else {
        echo "<p>Błąd podczas dodawania podstrony: " . $stmt->error . "</p>";
      }
  }

  
}


//Funkcja do usuwania kategorii
function UsunKategorie($id, $link) {
  //Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
  }

  //Przygotowanie zapytania
  $stmt = $link->prepare("DELETE FROM kategoria WHERE id = ? LIMIT 1");
  //Przygotowanie parametrów zapytania
  $stmt->bind_param("i", $id); 

  //Wykonanie zapytania
  if ($stmt->execute()) {
    //Odświeżenie strony
    header("Location: ?");
    //Zakończenie skryptu
    exit();
  } else {
    echo "<p>Błąd podczas usuwania podstrony: " . $stmt->error . "</p>";
  }
  //Zamyka przygotowane zapyanie
  $stmt->close();
}





// Funkcja pokazania listy produktów
function PokazProdukt($link) {
  // Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
  }

  // Wykonanie zapytania sql
  $result = $link->query("SELECT * FROM produkt LIMIT 10");

  echo "<div style='background-color:lightgray'>";
  echo "<table style='border-collapse:collapse; border:1px solid black'>";
  echo "<tr style='background-color:gray'><th>ID</th><th>Tytuł</th><th>Opis</th><th>Data utworzenia</th><th>Data modyfikacji</th><th>Data wygasniecia</th><th>Cena netto</th><th>Podatek VAT</th><th>Ilość</th><th>Status</th><th>Kategoria</th><th>Gabaryty</th><th>Zdjęcie</th><th>Akcje</th></tr>";

  // Pętla pomagająca stworzyć wiersze i kolumny
  while ($row = $result->fetch_array()) {
      echo "<tr style='background-color:white; border:1px solid black'>
              <td style='border:1px solid black'>{$row['id']}</td>
              <td style='border:1px solid black'>{$row['tytul']}</td>
              <td style='border:1px solid black'>{$row['opis']}</td>
              <td style='border:1px solid black'> {$row['data_utworzenia']}</td>
              <td style='border:1px solid black'>{$row['data_modyfikacji']}</td>
              <td style='border:1px solid black'>{$row['data_wygasniecia']}</td>
              <td style='border:1px solid black'>{$row['cena_netto']}</td>
              <td style='border:1px solid black'>{$row['podatek_vat']}</td>
              <td style='border:1px solid black'>{$row['ilosc_w_magazynie']}</td>
              <td style='border:1px solid black'>{$row['status']}</td>
              <td style='border:1px solid black'>{$row['kategoria']}</td>
              <td style='border:1px solid black'>{$row['gabaryt_produktu']}</td>
              <td style='border:1px solid black'>";

      if (!empty($row['zdjecie'])) {
          // Używamy ścieżki do pliku bezpośrednio w `src` tagu img
          echo "<img src='{$row['zdjecie']}' alt='Zdjęcie' width='100' height='100'>";
      } else {
          echo "Brak zdjęcia";
      }

      echo "</td>
            <td style='border:1px solid black'>
                <a href='?edit_id3={$row['id']}'>Edytuj</a> | 
                <a href='?delete_id3={$row['id']}'>Usuń</a>
            </td>
          </tr>";
  }

  echo "</table>";
  echo "</div>";
}







function DodajProdukt($link) {
  // Jeżeli user nie jest zalogowany -> wyświetl formularz logowania
  if (!$_SESSION['login']) {
    FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
    return;
  }

  echo "
    <div style='background-color:lightgray;'>
    <center>
    <h2>Dodaj nowy produkt</h2>
    <form method='post' action=''>
        <label for='tytul'>Tytuł:</label><br>
        <input type='text' name='tytul' id='tytul' required><br><br>

        <label for='opis'>Opis:</label><br>
        <textarea name='opis' id='opis' rows='10' cols='50' required></textarea><br><br>

        <label for='expiration'>data wygaśnięcia:</label><br>
        <input type='date' name='expiration' id='expiration' required></input><br><br>

        <label for='netto'>Cena netto:</label><br>
        <input type='text' name='netto' id='netto' required><br><br>

        <label for='vat'>Podatek VAT:</label><br>
        <input type='number' name='vat' id='vat' required><br><br>

        <label for='ilosc'>ilość w magazynie:</label><br>
        <input type='number' name='ilosc' id='ilosc' required><br><br>

        <label for='status'>Status dostępności:</label><br>
        <textarea name='status' id='status' rows='10' cols='50' required></textarea><br><br>

        <label for='kategoria'>Kategoria:</label><br>
        <input type='number' name='kategoria' id='kategoria' required><br><br>

        <label for='gabaryt'>Gabaryty produktu:</label><br>
        <textarea name='gabaryt' id='gabaryt' rows='10' cols='50' required></textarea><br><br>

        <label for='zdjecie'>Wklej ścieżkę do zdjęcia:</label>
        <input type='text' id='zdjecie' name='zdjecie' required><br><br>

        <button type='submit' name='save_product'>Zapisz nowy produkt</button>
    </form>
    </center>
    </div>
  ";

  // Po dodaniu produkt należy zaktualizować bazę danych
  if (isset($_POST['save_product'])) {
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $data_utworzenia = date('Y-m-d H:i:s'); // Używaj obecnej daty i czasu
    $data_wygasniecia = $_POST['expiration'];
    $netto = $_POST['netto'];
    $vat = $_POST['vat'];
    $ilosc = $_POST['ilosc'];
    $status = $_POST['status'];
    $kategoria = $_POST['kategoria'];
    $gabaryt = $_POST['gabaryt'];
    $zdjecie = $_POST['zdjecie']; // Przechowujemy pełną ścieżkę dostępu do zdjęcia

    // Przygotowanie zapytania
    $stmt = $link->prepare("INSERT INTO produkt (tytul, opis, 
    data_utworzenia, data_modyfikacji, data_wygasniecia,
    cena_netto, podatek_vat, ilosc_w_magazynie, status, kategoria,
    gabaryt_produktu, zdjecie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Przygotowanie parametrów zapytania
    $stmt->bind_param("sssssdiissss", $tytul, $opis, $data_utworzenia, 
    $data_utworzenia, $data_wygasniecia, $netto, $vat, $ilosc, 
    $status, $kategoria, $gabaryt, $zdjecie);

    // Wykonanie zapytania
    if ($stmt->execute()) {
        // Odświeżenie strony
        PokazProdukt($link);
        exit();
    } else {
        echo "<p>Błąd podczas dodawania produktu: " . $stmt->error . "</p>";
    }
  }
}











function EdytujProdukt($link) {
  // Sprawdzamy, czy użytkownik jest zalogowany
  if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
  }

  // Pobieramy ID produktu do edycji
  if (!isset($_GET['edit_id3'])) {
      echo "Brak ID produktu do edycji.";
      return;
  }

  $id = $_GET['edit_id3'];

  // Pobranie danych produktu do edycji
  $query = $link->prepare("SELECT * FROM produkt WHERE id = ?");
  $query->bind_param("i", $id);
  $query->execute();
  $result = $query->get_result();

  if ($result->num_rows === 0) {
      echo "Produkt o podanym ID nie istnieje.";
      return;
  }

  $produkt = $result->fetch_assoc();

  // Formularz edycji
  echo "
  <div style='background-color:lightgray'>
  <center>
  <h2>Edytuj produkt</h2>
  <form method='post' action=''>
      <label for='tytul'>Tytuł:</label><br>
      <input type='text' name='tytul' id='tytul' value='{$produkt['tytul']}' required><br><br>

      <label for='opis'>Opis:</label><br>
      <textarea name='opis' id='opis' rows='5' cols='50' required>{$produkt['opis']}</textarea><br><br>

      <label for='expiration'>Data wygaśnięcia:</label><br>
      <input type='date' name='expiration' id='expiration' value='{$produkt['data_wygasniecia']}' required><br><br>

      <label for='netto'>Cena netto:</label><br>
      <input type='text' name='netto' id='netto' value='{$produkt['cena_netto']}' required><br><br>

      <label for='vat'>Podatek VAT:</label><br>
      <input type='number' name='vat' id='vat' value='{$produkt['podatek_vat']}' required><br><br>

      <label for='ilosc'>Ilość w magazynie:</label><br>
      <input type='number' name='ilosc' id='ilosc' value='{$produkt['ilosc_w_magazynie']}' required><br><br>

      <label for='status'>Status dostępności:</label><br>
      <input type='text' name='status' id='status' value='{$produkt['status']}' required><br><br>

      <label for='kategoria'>Kategoria:</label><br>
      <input type='number' name='kategoria' id='kategoria' value='{$produkt['kategoria']}' required><br><br>

      <label for='gabaryt'>Gabaryty produktu:</label><br>
      <input type='text' name='gabaryt' id='gabaryt' value='{$produkt['gabaryt_produktu']}' required><br><br>

      <label for='zdjecie'>Ścieżka do zdjęcia:</label><br>
      <input type='text' name='zdjecie' id='zdjecie' value='{$produkt['zdjecie']}'><br><br>

      <button type='submit' name='update_product'>Zapisz zmiany</button>
  </form>
  
  </center>
  </div>
  ";

  // Aktualizacja danych w bazie
  if (isset($_POST['update_product'])) {
      $tytul = $_POST['tytul'];
      $opis = $_POST['opis'];
      $data_wygasniecia = $_POST['expiration'];
      $cena_netto = $_POST['netto'];
      $podatek_vat = $_POST['vat'];
      $ilosc = $_POST['ilosc'];
      $status = $_POST['status'];
      $kategoria = $_POST['kategoria'];
      $gabaryt = $_POST['gabaryt'];
      $zdjecie = $_POST['zdjecie']; 

      $data_modyfikacji = date('Y-m-d'); // Data aktualizacji

      // Przygotowanie zapytania do aktualizacji
      $stmt = $link->prepare("UPDATE produkt SET 
          tytul = ?, 
          opis = ?, 
          data_modyfikacji = ?, 
          data_wygasniecia = ?, 
          cena_netto = ?, 
          podatek_vat = ?, 
          ilosc_w_magazynie = ?, 
          status = ?, 
          kategoria = ?, 
          gabaryt_produktu = ?, 
          zdjecie = ? 
          WHERE id = ?");

      $stmt->bind_param(
          "ssssdiissssi", 
          $tytul, 
          $opis, 
          $data_modyfikacji, 
          $data_wygasniecia, 
          $cena_netto, 
          $podatek_vat, 
          $ilosc, 
          $status, 
          $kategoria, 
          $gabaryt, 
          $zdjecie, 
          $id
      );

      // Wykonanie zapytania
      if ($stmt->execute()) {
          echo "
            <div style='background-color:lightgray'>
  <center>
          Produkt został zaktualizowany pomyślnie.   </center>
  </div>";
          PokazProdukt($link);
          exit();
      } else {
          echo "<p>Błąd podczas aktualizacji produktu: " . $stmt->error . "</p>";
      }
  }
}

function UsunProdukt($link) {
  // Sprawdzamy, czy użytkownik jest zalogowany
  if (!$_SESSION['login']) {
      FormularzLogowania('Musisz być zalogowany, aby uzyskać dostęp.');
      return;
  }

  // Pobieramy ID produktu do usunięcia
  if (!isset($_GET['delete_id3'])) {
      echo "Brak ID produktu do usunięcia.";
      return;
  }

  $id = $_GET['delete_id3'];

  // Przygotowanie zapytania do usunięcia
  $stmt = $link->prepare("DELETE FROM produkt WHERE id = ?");
  $stmt->bind_param("i", $id);

  // Wykonanie zapytania
  if ($stmt->execute()) {
      echo "Produkt o ID $id został pomyślnie usunięty.";
      // Opcjonalnie: przekierowanie na stronę główną
      header("Location: ?");
      exit();
  } else {
      echo "<p>Błąd podczas usuwania produktu: " . $stmt->error . "</p>";
  }
}







//Warunkek obsługujący czy user jest zalogowany
if ($_SESSION['login']) {
  //Warunki sprawdzające czy któraś z funkcji została aktywowana -> wykonanie danej funkcji
  if (isset($_GET['edit_id'])) {
      EdytujPodstrone($_GET['edit_id'], $link);
  } elseif (isset($_GET['add_new'])) {
      DodajNowaPodstrone($link);
  } elseif (isset($_GET['delete_id'])) {
      UsunPodstrone($_GET['delete_id'], $link);
  } else {
      listaPodstron($link);
  }
} else {
  FormularzLogowania();
}

// Warunek sprawdzający, czy użytkownik jest zalogowany
if ($_SESSION['login']) {
  echo "<div style='background-color:lightgray;'>";

  // Formularz do dodania nowej kategorii
  echo "
  <form method='get'>
      <button type='submit' name='add_new_category' value='true'>Dodaj nową kategorię</button>
  </form>
  ";

  // Formularz do edytowania lub usuwania kategorii
  echo "
  <form method='get'>
      <label for='category_id'>Podaj ID kategorii:</label><br>
      <input type='number' name='category_id' id='category_id' required><br><br>
      
      <button type='submit' name='edit_category'>Edytuj kategorię</button>
      <button type='submit' name='delete_category'>Usuń kategorię</button>
  </form>
  ";

  // Sprawdzamy, czy użytkownik chce edytować kategorię
  if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Jeśli użytkownik chce edytować kategorię
    if (isset($_GET['edit_category'])) {
        EdytujKategorie($category_id, $link);  // Przekazujemy ID do funkcji edytującej
    }
    
    // Jeśli użytkownik chce usunąć kategorię
    if (isset($_GET['delete_category'])) {
        UsunKategorie($category_id, $link);  // Przekazujemy ID do funkcji usuwającej
    }
  }

  // Sprawdzamy, czy użytkownik chce dodać nową kategorię
  if (isset($_GET['add_new_category']) && $_GET['add_new_category'] === 'true') {
    // Wywołanie funkcji do dodania nowej kategorii
    DodajKategorie($link);  // Tutaj wywołujemy funkcję DodajKategorie
  }

  // Wyświetlenie kategorii
  PokazKategorie(0, $link, 2);
  echo "<hr>";

  echo "</div>";
} else {
  FormularzLogowania();  // Formularz logowania, jeśli użytkownik nie jest zalogowany
}



if ($_SESSION['login']) {
  //Warunki sprawdzające czy któraś z funkcji została aktywowana -> wykonanie danej funkcji
  if (isset($_GET['edit_id3'])) {
      EdytujProdukt($link, $_GET['edit_id3']);
  } elseif (isset($_GET['add_new3'])) {
      DodajProdukt($link);
  } elseif (isset($_GET['delete_id3'])) {
      UsunProdukt($link);
  } else {
      echo "<div style='background-color:lightgray'>";
      echo "<a href='?add_new3=true'>Dodaj nowy produkt</a>";
      echo "</div>";
      PokazProdukt($link);
  }
} else {
  FormularzLogowania();
}


?>