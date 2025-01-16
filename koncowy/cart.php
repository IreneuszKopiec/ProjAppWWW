
<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
/* po tym komentarzu będzie kod do dynamicznego ładowania stron */

//Dołączenie plików php:
//-Nawiązywania połączenia
include 'cfg.php';
//-Pokazania treści strony
include 'showpage.php';
//-Pokazania tytułu strony
include 'showtitle.php';

$idp = $_GET['idp'];
?>



<!DOCTYPE html>
<html lang="pl">
  <head>
  <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Language" content="pl" />
  <meta name="Author" content="Ireneusz Kopiec" />
  <link rel="stylesheet" href="css/style.css" />
  <script src="js/clearform.js" type="text/javascript"></script>
  <script src="js/timedate.js" type="text/javascript"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <title>Największe mosty świata v1.8</title>
  </head>
  <!-- 
  Uruchomienie funkcji startclock() w momencie załadowania strony
  -->
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
          <!-- 
          <li><a href="index.php?idp=7" class="list-item">testJS</a></li>
          -->
          <li><a href="index.php?idp=8" class="list-item">Filmy</a></li>
          <li><a href="cart.php" class="list-item">Sklep</a></li>
        </ul>
      </nav>
      <h1 class="header-title" id="header-title">
        Sklep
      </h1>
    </header>
    <main>
    <?php
    session_start(); // Rozpoczęcie sesji

    include 'cfg.php';

    // Funkcja dodawania do koszyka
    function addToCart($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            include 'cfg.php';
            $query = $link->prepare("SELECT id, tytul, cena_netto, podatek_vat FROM produkt WHERE id = ? LIMIT 1");
            $query->bind_param("i", $productId);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $_SESSION['cart'][$productId] = [
                    'name' => $product['tytul'],
                    'price' => $product['cena_netto'],
                    'vat' => $product['podatek_vat'],
                    'quantity' => $quantity,
                ];
            }
        }
    }

    // Funkcja usuwania z koszyka
    function removeFromCart($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    // Funkcja aktualizacji ilości w koszyku
    function updateCart($productId, $quantity) {
        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            } else {
                removeFromCart($productId);
            }
        }
    }

    // Funkcja wyświetlania koszyka
    function showCart() {
        echo "<section class='about'>";
        echo "<h2 class='section-title'>Twój koszyk</h2>";

        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            echo "<p class='section-text'>Twój koszyk jest pusty.</p>";
            echo "</section>";
            return;
        }

        echo "<div class='cart-container'>";
        echo "<table  style='border-collapse:collapse; border-color:black'>";
        echo "<tr style='border: 1px solid black'>
        <th style='border: 1px solid black'>Nazwa</th>
        <th style='border: 1px solid black'>Cena netto</th>
        <th style='border: 1px solid black'>VAT</th>
        <th style='border: 1px solid black'>Ilość</th>
        <th style='border: 1px solid black'>Cena brutto</th>
        <th style='border: 1px solid black'>Akcje</th></tr>";

        $total = 0;
        foreach ($_SESSION['cart'] as $productId => $item) {
            $priceBrutto = $item['price'] * (1 + $item['vat'] / 100) * $item['quantity'];
            $total += $priceBrutto;

            echo "<tr style='border: 1px solid black'>";
            echo "<td style='border: 1px solid black'>{$item['name']}</td>";
            echo "<td style='border: 1px solid black'>{$item['price']} zł</td>";
            echo "<td style='border: 1px solid black'>{$item['vat']}%</td>";
            echo "<td style='border: 1px solid black'>{$item['quantity']}</td>";
            echo "<td style='border: 1px solid black'>" . number_format($priceBrutto, 2) . " zł</td>";
            echo "<td style='border: 1px solid black'>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='product_id' value='$productId'>";
            echo "<input type='hidden' name='action' value='remove'>";
            echo "<button type='submit' class='cart-button'>Usuń</button>";
            echo "</form>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='product_id' value='$productId'>";
            echo "<input type='hidden' name='action' value='update'>";
            echo "<input type='number' name='quantity' value='{$item['quantity']}' min='1' style='width:50px;'>";
            echo "<button type='submit' class='cart-button'>Zmień ilość</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }

        echo "<tr style='border: 1px solid black'>
        <td colspan='4' style='border: 1px solid black'>Razem</td>
        <td style='border: 1px solid black'>" . number_format($total, 2) . " zł</td>";
        echo "<td style='border: 1px solid black'><form method='post' style='display:inline;'>";
        echo "<button type='submit' class='cart-button'>Zapłać</button>";
        echo "</td></tr>";
        echo "</table>";
        echo "</div>";
        echo "</section>";
    }

    // Funkcja wyświetlania produktów z bazy danych
    function showProducts() {
        include 'cfg.php';

        $query = "SELECT id, tytul, cena_netto, podatek_vat, zdjecie FROM produkt";
        $result = $link->query($query);

        if ($result->num_rows > 0) {
            echo "<section class='row'>";
            echo "<h2 class='section-title'>Produkty</h2>";
            echo "<div class='product-card'>";

            while ($product = $result->fetch_assoc()) {
                $priceBrutto = $product['cena_netto'] * (1 + $product['podatek_vat'] / 100);

                echo "<div style='width:100%; padding-bottom:15px'>";
                echo "<img src='{$product['zdjecie']}' alt='{$product['tytul']}' style='width:100%; height:auto; border-radius:5px;'>";
                echo "<h3 class='product-title'>{$product['tytul']}</h3>";
                echo "<p class='product-price'>Cena netto: {$product['cena_netto']} zł</p>";
                echo "<p class='product-price'>Cena brutto: " . number_format($priceBrutto, 2) . " zł</p>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='product_id' value='{$product['id']}'>";
                echo "<input type='hidden' name='action' value='add'>";
                echo "<input type='number' name='quantity' value='1' min='1'>";
                echo "<button type='submit' class='product-button'>Dodaj do koszyka</button>";
                echo "</form>";
                echo "</div>";
            }

            echo "</div>";
            echo "</section>";
        } else {
            echo "<p class='section-text'>Brak produktów w bazie danych.</p>";
        }
    }

    // Obsługa żądań koszyka
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $productId = $_POST['product_id'];
            $quantity = $_POST['quantity'] ?? 1;

            if ($action === 'add') {
                addToCart($productId, $quantity);
            } elseif ($action === 'remove') {
                removeFromCart($productId);
            } elseif ($action === 'update') {
                updateCart($productId, $quantity);
            }
        }
    }

    // Wyświetlenie koszyka
    showCart();

    // Wyświetlenie produktów z bazy danych
    showProducts();
    ?>
    </main>
    <footer>
      <div class="section-text">
        <div id="zegarek"></div>
        <div id="data"></div>
      </div>
      <p class="section-text">Copyright: &copy; Ireneusz Kopiec</p>
    </footer>
    
    
    <?php
    $nr_indeksu = 169256;
    $nrGrupy = 2;
    echo 'Ireneusz Kopiec '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
    ?>
  </body>
</html>
