<?php
include_once '../../configuracion.php';
include_once("../estructura/header.php");

$datos = darDatosSubmitted();
?>

<div class="container mt-5">
    <h1 class="text-center">Iniciar Sesión</h1>
    <div id="mensaje" class="text-center"></div>
    <?php
    if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso') {
        echo '<div class="alert alert-success text-center">Cuenta creada exitosamente. Ahora puede iniciar sesión.</div>';
    }
    ?>
    <form id="loginForm" method="POST" onsubmit="return verificarLogin(event)" novalidate>
        <div class="form-group">
            <label for="nombreUsuario">Nombre de Usuario</label>
            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" required pattern="[a-zA-Z0-9]+" title="no puede estar vacío o tener espacios.">
            <div class="invalid-feedback">
                no puede estar vacío o tener espacios.
            </div>
        </div>
        <div class="form-group">
            <label for="uspass">Contraseña</label>
            <input type="password" class="form-control" id="uspass" name="uspass" required pattern="\S+" title="La contraseña no puede estar vacía ni contener espacios.">
            <div class="invalid-feedback">
                La contraseña no puede estar vacía ni contener espacios.
            </div>
        </div>
        <div class="text-center">
            <a href="../Home/registrarUsuario.php">Registrarse</a>
        </div>
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
</div>

<script>
function hashPassword(password) {
    return CryptoJS.SHA256(password).toString();
}

function verificarLogin(event) {
    event.preventDefault(); // Evitar el envío del formulario

    const form = document.getElementById('loginForm');
    const nombreUsuario = document.getElementById('nombreUsuario').value.trim();
    const uspassField = document.getElementById('uspass');
    const uspass = uspassField.value.trim();

    // Validar el formulario
    if (form.checkValidity() === false) {
        event.stopPropagation();
    } else {
        // Hashear la contraseña
        const hashedPassword = hashPassword(uspass);

        $.ajax({
            url: '../Action/verificarLogin.php',
            method: 'POST',
            data: {
                nombreUsuario: nombreUsuario,
                uspass: hashedPassword,
                form_security_token: 'valor_esperado' // Token de seguridad
            },
            success: function(response) {
                const mensajeDiv = document.getElementById('mensaje');
                if (response.status === 'error') {
                    mensajeDiv.innerHTML = '<div class="alert alert-danger text-center">' + response.message + '</div>';
                } else if (response.status === 'success') {
                    mensajeDiv.innerHTML = '<div class="alert alert-success text-center">' + response.message + '</div>';
                    window.location.href = response.redirect;
                }
            },
            error: function() {
                const mensajeDiv = document.getElementById('mensaje');
                mensajeDiv.innerHTML = '<div class="alert alert-danger text-center">Error al verificar las credenciales. Por favor, inténtelo de nuevo.</div>';
            }
        });
    }

    form.classList.add('was-validated');
}
</script>

<?php include_once("../estructura/footer.php"); ?>