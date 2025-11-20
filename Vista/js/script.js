(function () {
  emailjs.init("vMQflWJRGofnRADyQ"); // Inicializa EmailJS con tu clave pública
})();

function sendEmail(toName, toEmail, message, templateID) {
  emailjs
    .send("service_o5pr1y6", templateID, {
      // <-- Aquí usamos la variable, no el texto fijo
      to_name: toName,
      to_email: toEmail,
      from_name: "Cel-uStore Team",
      message: message,
    })
    .then(
      function (response) {
        console.log("SUCCESS!", response.status, response.text);
      },
      function (error) {
        console.log("FAILED...", error);
      }
    );
}

function hashPassword() {
  var passwordField = document.getElementById("uspass");
  var password = passwordField.value;
  var hashedPassword = CryptoJS.SHA256(password).toString();
  passwordField.value = hashedPassword;
  return true; // Permitir que el formulario se envíe
}
