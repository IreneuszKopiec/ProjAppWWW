<?php
//Dołączenie pliku php odpowiedzialnego za połączenie z bazą
include("../cfg.php");

//Rozpoczęcie sesji
session_start();

//Funkcja pokazująca formularz logowania
//pokazująca informację o błędnych danych
function FormularzLogowania($error = '') {
    $wynik = '
    <div class="logowanie">
      <h1 class="heading">Panel CMS:</h1>
      <div class="logowanie">
      '.($error ? '<p class="error">'.$error.'</p>' : '').'
        <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
          <table class="logowanie">
            <tr><td class="log4_t">[email]</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
            <tr><td class="log4_t">[haslo]</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
            <tr><td>&nbsp</td><td><input type="submit" name="x1_submit" class="logowanie" value="zaloguj" /></td></tr>
          </table>
        </form>
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

  echo "<a href='?add_new=true'>Dodaj nową podstronę</a>";
  echo "<table>";
  echo "<tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>";

  //Pętla pomagająca stworzyć wiersze i kolumny
  while ($row = $result->fetch_array()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['page_title']}</td>
            <td>
                <a href='?edit_id={$row['id']}'>Edytuj</a> | 
                <a href='?delete_id={$row['id']}'>Usuń</a>
            </td>
          </tr>";
  }
  echo "</table>";
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

?>