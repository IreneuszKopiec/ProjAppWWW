//Funkcja do pobrania aktualnej daty
function gettheDate() {
  //Tworzenie obiektu z aktualną datą i czasem
  Now = new Date();
  Today =
    " " +
    //Wyświatlanie aktualnego miesiaca
    (Now.getMonth() + 1) +
    "/" +
    //Wyświatlanie aktualnego dnia
    Now.getDate() +
    "/" +
    //Wyświatlanie aktualnego roku w postaci dwóch ostatnich liczb
    (Now.getYear() - 100);
  document.getElementById("data").innerHTML = Today;
}

var id = null;
var isRunning = false;

//Funkcja do zatrzymywania zegarka
function stopclock() {
  if (isRunning) clearTimeout(id);
  isRunning = false;
}

//Funkcja do aktywacji zagarka
function startclock() {
  stopclock();
  gettheDate();
  showtime();
}

//Funkcja do pokazywania aktualnego czasu
function showtime() {
  //Tworzenie obiektu z aktualną datą i czasem
  var dzis = new Date();
  //Pobranie odpowiednich wartości czasu (godziny, minuty i sekundy)
  var godziny = dzis.getHours();
  var minuty = dzis.getMinutes();
  var sekundy = dzis.getSeconds();
  //Formatowanie godzin z systemu zegara 24 godzinnego do postaci systemu zegara 12-godzinnego
  var time = "" + (godziny > 12 ? godziny - 12 : godziny);
  //Formatowanie minut i sekund (Dodanie 0 dla minut/sekund mniejszych od 10)
  time += (minuty < 10 ? ":0" : ":") + minuty;
  time += (sekundy < 10 ? ":0" : ":") + sekundy;
  //Dodanie końcówek P.M. lub A.M.
  time += godziny >= 12 ? " P.M." : " A.M.";
  //Wyświetlanie czasu
  document.getElementById("zegarek").innerHTML = time;
  //Odświeżanie strony co sekunde
  id = setTimeout("showtime()", 1000);
  isRunning = true;
}
