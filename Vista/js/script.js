(function () {
  emailjs.init("vMQflWJRGofnRADyQ"); // Inicializa EmailJS con tu clave pública
})();

function sendEmail(toName, toEmail, message) {
  emailjs
    .send("service_o5pr1y6", "template_fsf82c5", {
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
