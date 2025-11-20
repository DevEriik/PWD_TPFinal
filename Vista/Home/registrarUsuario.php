<?php include_once("../estructura/header.php"); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            
            <div class="card shadow border-0 rounded-3">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4 fw-bold">Crear Cuenta</h2>
                    
                    <div id="mensaje" class="text-center mb-3"></div>

                    <form id="registroForm" action="../Action/actionRegistrarUsuario.php" method="post" onsubmit="return verificarFormulario(event)" novalidate>
                        <input type="hidden" name="form_security_token" value="valor_esperado"> 

                        <div class="mb-3">
                            <label for="usnombre" class="form-label">Nombre de Usuario</label>
                            <input type="text" id="usnombre" name="usnombre" class="form-control" required pattern="[a-zA-Z0-9]+" title="No puede estar vacío o tener espacios." placeholder="Ej: JuanPerez">
                            <div class="invalid-feedback" id="nombreError">
                                No puede estar vacío o tener espacios.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="usmail" class="form-label">Correo Electrónico</label>
                            <input type="email" id="usmail" name="usmail" class="form-control" required placeholder="ejemplo@mail.com">
                            <div class="invalid-feedback" id="emailError">
                                Por favor, ingrese un email válido.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="uspass" class="form-label">Contraseña</label>
                            <input type="password" id="uspass" name="uspass" class="form-control" required pattern="\S+" title="Sin espacios." placeholder="********">
                            <div class="invalid-feedback">
                                La contraseña no puede estar vacía ni contener espacios.
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Registrar</button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="small mb-0">¿Ya tienes cuenta?</p>
                            <a href="../Home/login.php" class="text-decoration-none">Iniciar Sesión</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function hashPassword() {
    var passwordField = document.getElementById('uspass');
    var password = passwordField.value;

    if (password !== '') {
        var hashedPassword = CryptoJS.SHA256(password).toString();
        passwordField.value = hashedPassword;
    }
    return true;
}

function verificarFormulario(event) {
    event.preventDefault(); 

    var form = document.getElementById('registroForm');
    var nombreField = document.getElementById('usnombre');
    var emailField = document.getElementById('usmail');
    var mensajeDiv = document.getElementById('mensaje');
    
    // Limpiamos errores previos visuales
    nombreField.classList.remove('is-invalid');
    emailField.classList.remove('is-invalid');

    if (form.checkValidity() === false) {
        event.stopPropagation();
        form.classList.add('was-validated');
    } else {
        // Verificar si existe usuario/email
        $.ajax({
            url: '../Action/actionRegistrarUsuario.php',
            method: 'POST',
            data: {
                verificar: true,
                usnombre: nombreField.value.trim(),
                usmail: emailField.value.trim(),
                form_security_token: 'valor_esperado'
            },
            success: function(response) {
                // Si el servidor devuelve JSON string, lo parseamos
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch(e){}
                }

                var hayError = false;

                if (response.nombreExiste) {
                    nombreField.classList.add('is-invalid');
                    document.getElementById('nombreError').innerText = 'El nombre de usuario ya existe.';
                    hayError = true;
                }

                if (response.emailExiste) {
                    emailField.classList.add('is-invalid');
                    document.getElementById('emailError').innerText = 'El correo ya está registrado.';
                    hayError = true;
                }

                if (!hayError) {
                    if (hashPassword()) {
                        $.ajax({
                            url: form.action,
                            method: form.method,
                            data: $(form).serialize(),
                            success: function(regResponse) {
                                if (typeof regResponse === 'string') {
                                    try { regResponse = JSON.parse(regResponse); } catch(e){}
                                }

                                if (regResponse.status === 'success') {
                                    window.location.href = regResponse.redirect;
                                } else {
                                    mensajeDiv.innerHTML = '<div class="alert alert-danger">' + regResponse.message + '</div>';
                                }
                            },
                            error: function() {
                                mensajeDiv.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>';
                            }
                        });
                    }
                }
            },
            error: function() {
                mensajeDiv.innerHTML = '<div class="alert alert-danger">Error al verificar datos.</div>';
            }
        });
    }
}
</script>

