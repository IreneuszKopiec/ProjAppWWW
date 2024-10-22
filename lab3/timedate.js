function gettheDate() {
  Now = new Date();
  Today =
    " " +
    (Now.getMonth() + 1) +
    "/" +
    Now.getDate() +
    "/" +
    (Now.getYear() - 100);
  document.getElementById("data").innerHTML = Today;
}

var id = null;
var isRunning = false;

function stopclock() {
  if (isRunning) clearTimeout(id);
  isRunning = false;
}

function startclock() {
  stopclock();
  gettheDate();
  showtime();
}

function showtime() {
  var dzis = new Date();
  var godziny = dzis.getHours();
  var minuty = dzis.getMinutes();
  var sekundy = dzis.getSeconds();
  var time = "" + (godziny > 12 ? godziny - 12 : godziny);
  time += (minuty < 10 ? ":0" : ":") + minuty;
  time += (sekundy < 10 ? ":0" : ":") + sekundy;
  time += godziny >= 12 ? " P.M." : " A.M.";
  document.getElementById("zegarek").innerHTML = time;
  id = setTimeout("showtime()", 1000);
  isRunning = true;
}
