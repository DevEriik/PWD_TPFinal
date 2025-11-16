<?php include_once("../estructura/headerSeguro.php"); ?>
<?php
include_once '../../configuracion.php';

// creo un objeto de la clase abmUsuarioRol para llamar a la funcion verificarRolUsuario
$abmUsuarioRol = new ABMUsuarioRol(); 

// Obtener el ID del usuario en la sesion para verificar si tiene permisos
$idUsuarioActual = $session->getUsuario()->getIdUsuario();

// Verificar si el usuario tiene permisos para acceder a esta página (el 1 es el administrador o sea que le estoy
// diciendo que si el usuario no es administrador lo redirija al login)
$usuarioPermitido = $abmUsuarioRol->verificarRolUsuario($idUsuarioActual, 1);
if (!$usuarioPermitido) {
    header('Location: ../Home/login.php');
    exit();
}

//$session = new Session();
$abmUsuario = new ABMUsuario();

$datos = darDatosSubmitted();

// Obtener el usuario a editar
$idUsuario = $datos['id'];
if($idUsuario == null){ // Si no se envió un ID, redirigir al formulario de actualización
    header('Location: ../Home/actualizarUsuario.php');
    exit();
}
$usuario = $abmUsuario->buscar(['idusuario' => $idUsuario])[0];
?>

<div class="container mt-5">
    <h1 class="text-center">Actualizar Usuario</h1>
    <form id="actualizarForm" action="../Action/modificarUsuarios.php" method="post" onsubmit="return verificarFormulario(event)" novalidate>
        <input type="hidden" name="idusuario" value="<?php echo $usuario->getIdUsuario(); ?>">
        <div class="form-group">
            <label for="usnombre">Nombre:</label>
            <input type="text" id="usnombre" name="usnombre" class="form-control" value="<?php echo $usuario->getUsNombre(); ?>" required pattern="[a-zA-Z0-9]+">
            <div class="invalid-feedback">
                no puede estar vacío.
            </div>
        </div>
        <div class="form-group">
            <label for="uspass">Contraseña:</label>
            <input type="password" id="uspass" name="uspass" class="form-control">
        </div>
        <div class="form-group">
            <label for="usmail">Email:</label>
            <input type="email" id="usmail" name="usmail" class="form-control" value="<?php echo $usuario->getUsMail(); ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese un email válido.
            </div>
        </div>
        <input type="hidden" name="usdeshabilitado" value="<?php echo $usuario->getUsDeshabilitado(); ?>">
        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
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

    var form = document.getElementById('actualizarForm');
    var nombreField = document.getElementById('usnombre');
    var emailField = document.getElementById('usmail');
    var mensajeDiv = document.getElementById('mensaje');

    // Validar el formulario
    if (form.checkValidity() === false) {
        event.stopPropagation();
    } else {
        if (hashPassword()) {
            // Enviar el formulario utilizando AJAX
            $.ajax({
                url: form.action,
                method: form.method,
                data: $(form).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        mensajeDiv.innerHTML = '<div class="alert alert-success text-center">Datos modificados correctamente.</div>';
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500); // Esperar 1.5 segundos antes de redirigir
                    } else {
                        mensajeDiv.innerHTML = '<div class="alert alert-danger text-center">' + response.message + '</div>';
                    }
                },
                error: function() {
                    mensajeDiv.innerHTML = '<div class="alert alert-danger text-center">Error al actualizar los datos. Asegúrate de que el nombre o email no estén en la base de datos.</div>';
                }
            });
        }
    }

    form.classList.add('was-validated');
}
</script>

<?php include_once("../estructura/footer.php"); ?>