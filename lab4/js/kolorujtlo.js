var boolean = false;
var number = 0;
var length = 0;

function convert(entryform, x, y) {
  convert_x = x.selectedIndex;
  convert_y = y.selectedIndex;
  entryform.display.value =
    (entryform.input.value * x[convert_x].value) / y[convert_y].value;
}

function addChar(entry, word) {
  if ((word == "." && number == 0) || word != ".") {
    entry.value == "" || entry.value == "0"
      ? (entry.value = word)
      : (entry.value += word);
    convert(entry.form, entry.form.measure1, entry.form.measure2);
    boolean = true;
    if (word == ".") {
      number = 1;
    }
  }
}

function openVothcom() {
  window.open(
    "../html/testJS.html",
    "Display window",
    "toolbar=no,directories=no,menubar=no"
  );
}

function clearForm() {
  var imie = document.getElementById("name");
  var email = document.getElementById("email");
  var wiadomosc = document.getElementById("message");

  imie.value = "";
  email.value = "";
  wiadomosc.value = "";
}

function changeBackground() {
  colors = [
    "#FFF000",
    "#000000",
    "#FFFFFF",
    "#00FF00",
    "#0000FF",
    "#FF8000",
    "#c0c0c0",
    "#FF0000",
  ];
  document.getElementById("header-title").style.color = colors[length];
  if (length < colors.length) {
    length++;
  } else {
    length = 0;
  }
}
