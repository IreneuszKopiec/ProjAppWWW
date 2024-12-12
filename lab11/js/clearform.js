//Funckja do czyszczenia formularza
function clearForm() {
  var imie = document.getElementById("name");
  var email = document.getElementById("email");
  var wiadomosc = document.getElementById("message");

  imie.value = "";
  email.value = "";
  wiadomosc.value = "";
}
