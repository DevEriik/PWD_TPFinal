(function(){
    emailjs.init("dgc2Clo0soEeyxVnL"); // Inicializa EmailJS con tu clave pública
})();

function sendEmail(toName, toEmail, message) {
    emailjs.send(/* 'service_ot7ycz5' */'service_0z1cn1z', 'template_fpc9kkb', {
        to_name: toName,
        to_email: toEmail,
        from_name: 'E-Commerce Team',
        message: message
    }).then(function(response) {
        console.log('SUCCESS!', response.status, response.text);
    }, function(error) {
        console.log('FAILED...', error);
    });
}

function hashPassword() {
    var passwordField = document.getElementById('uspass');
    var password = passwordField.value;
    var hashedPassword = CryptoJS.SHA256(password).toString();
    passwordField.value = hashedPassword;
    return true; // Permitir que el formulario se envíe
}