<?php
include_once '../../configuracion.php';
include_once("../estructura/header.php");

$datos = darDatosSubmitted();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
<script src="../js/jquery-3.7.1.js"></script>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            
            <div class="card shadow border-0 rounded-3">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4 fw-bold">Iniciar Sesión</h2>
                    
                    <div id="mensaje" class="text-center mb-3"></div>
                    
                    <?php
                    if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso') {
                        echo '<div class="alert alert-success text-center small">Cuenta creada exitosamente.<br>Ahora puede iniciar sesión.</div>';
                    }
                    ?>

                    <form id="loginForm" novalidate>
                        
                        <div class="mb-3">
                            <label for="nombreUsuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" required placeholder="Usuario">
                        </div>

                        <div class="mb-3">
                            <label for="uspass" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="uspass" name="uspass" required placeholder="********">
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="small mb-0">¿No tienes cuenta?</p>
                            <a href="../Home/registrarUsuario.php" class="text-decoration-none">Registrarse</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Manejamos el envío con jQuery, que es más seguro
    $('#loginForm').on('submit', function(e) {
        // 1. EVITAR que la página se recargue
        e.preventDefault();

        var nombreUsuario = $('#nombreUsuario').val();
        var password = $('#uspass').val();

        // Validaciones simples
        if(nombreUsuario === "" || password === "") {
            $('#mensaje').html('<div class="alert alert-warning">Por favor complete todos los campos</div>');
            return;
        }

        // 2. Intentar encriptar (usando try-catch por si falla la librería)
        var hashedPassword = "";
        try {
            hashedPassword = CryptoJS.SHA256(password).toString();
        } catch (err) {
            console.error("Error en CryptoJS:", err);
            $('#mensaje').html('<div class="alert alert-danger">Error interno: Librería de seguridad no cargada.</div>');
            return;
        }

        // 3. Enviar AJAX
        $.ajax({
            url: '../Action/verificarLogin.php',
            method: 'POST',
            data: {
                nombreUsuario: nombreUsuario,
                uspass: hashedPassword,
                form_security_token: 'valor_esperado'
            },
            success: function(response) {
                // Asegurar que response sea un objeto JSON
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                    }
                }

                if (response.status === 'error') {
                    $('#mensaje').html('<div class="alert alert-danger text-center">' + response.message + '</div>');
                } else if (response.status === 'success') {
                    $('#mensaje').html('<div class="alert alert-success text-center">¡Bienvenido! Redirigiendo...</div>');
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                $('#mensaje').html('<div class="alert alert-danger text-center">Error de conexión con el servidor.</div>');
            }
        });
    });
});
</script>

