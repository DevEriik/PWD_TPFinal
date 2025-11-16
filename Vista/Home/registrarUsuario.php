<?php include_once("../estructura/header.php"); ?>
    <div class="container mt-5">
        <h1 class="text-center">Registrar Usuario</h1>
        <form id="registroForm" action="../Action/actionRegistrarUsuario.php" method="post" onsubmit="return verificarFormulario(event)" novalidate>
            <input type="hidden" name="form_security_token" value="valor_esperado"> <!-- Token de seguridad -->
            <div class="form-group">
                <label for="usnombre">Nombre:</label>
                <input type="text" id="usnombre" name="usnombre" class="form-control" required pattern="[a-zA-Z0-9]+" title="no puede estar vacío o tener espacios.">
                <div class="invalid-feedback">
                   no puede estar vacío o tener espacios.
                </div>
            </div>
            <div class="form-group">
                <label for="uspass">Contraseña:</label>
                <input type="password" id="uspass" name="uspass" class="form-control" required pattern="\S+" title="La contraseña no puede estar vacía ni contener espacios.">
                <div class="invalid-feedback">
                    La contraseña no puede estar vacía ni contener espacios.
                </div>
            </div>
            <div class="form-group">
                <label for="usmail">Email:</label>
                <input type="email" id="usmail" name="usmail" class="form-control" required>
                <div class="invalid-feedback">
                    Por favor, ingrese un email válido.
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Registrar</button>
        </form>
        <div id="mensaje" class="text-center mt-3"></div>
    </div>

<script>
function hashPassword() {
    var passwordField = document.getElementById('uspass');
    var password = passwordField.value;

    // Verificar si la contraseña no está vacía
    if (password !== '') {
        var hashedPassword = CryptoJS.SHA256(password).toString();
        passwordField.value = hashedPassword;
    }

    return true; // Permitir que el formulario se envíe
}

function verificarFormulario(event) {
    event.preventDefault(); // Evitar el envío del formulario

    var form = document.getElementById('registroForm');
    var nombreField = document.getElementById('usnombre');
    var emailField = document.getElementById('usmail');
    var mensajeDiv = document.getElementById('mensaje');

    // Validar el formulario
    if (form.checkValidity() === false) {
        event.stopPropagation();
    } else {
        // Verificar si el nombre de usuario o el email ya existen en la base de datos
        $.ajax({
            url: '../Action/actionRegistrarUsuario.php',
            method: 'POST',
            data: {
                verificar: true,
                usnombre: nombreField.value.trim(),
                usmail: emailField.value.trim(),
                form_security_token: 'valor_esperado' // Token de seguridad
            },
            success: function(response) {
                var nombreError = document.getElementById('nombreError');
                var emailError = document.getElementById('emailError');

                nombreField.classList.remove('is-invalid');
                emailField.classList.remove('is-invalid');

                if (response.nombreExiste) {
                    nombreField.classList.add('is-invalid');
                    nombreError.innerHTML = 'El nombre de usuario ya existe.';
                }

                if (response.emailExiste) {
                    emailField.classList.add('is-invalid');
                    emailError.innerHTML = 'El correo electrónico ya está asociado a una cuenta.';
                }

                if (!response.nombreExiste && !response.emailExiste) {
                    if (hashPassword()) {
                        // Enviar el formulario y manejar la respuesta
                        $.ajax({
                            url: form.action,
                            method: form.method,
                            data: $(form).serialize(),
                            success: function(response) {
                                if (response.status === 'success') {
                                    //setTimeout(function() {
                                        window.location.href = response.redirect;
                                    //}, 1500); // Esperar 1.5 segundos antes de redirigir
                                } else {
                                    mensajeDiv.innerHTML = '<div class="alert alert-danger">' + response.message + '</div>';
                                }
                            },
                            error: function() {
                                mensajeDiv.innerHTML = '<div class="alert alert-danger">Error al registrar el usuario. Por favor, inténtelo de nuevo.</div>';
                            }
                        });
                    }
                }
            },
            error: function() {
                mensajeDiv.innerHTML = '<div class="alert alert-danger">Error al verificar los datos. Por favor, inténtelo de nuevo.</div>';
            }
        });
    }

    form.classList.add('was-validated');
}
</script>

<?php include_once("../estructura/footer.php"); ?>