<?php include_once("../estructura/headerSeguro.php"); ?>
<?php
include_once '../../configuracion.php';

// creo un objeto de la clase abmUsuarioRol para llamar a la funcion verificarRolUsuario
$abmUsuarioRol = new ABMUsuarioRol(); 

// Obtener el ID del usuario en la sesion para verificar si tiene permisos
$idUsuarioSesion = $session->getUsuario()->getIdUsuario();

// Verificar si el usuario tiene permisos para acceder a esta página (el 1 es el administrador o sea que le estoy
// diciendo que si el usuario no es administrador lo redirija al login)
$usuarioPermitido = $abmUsuarioRol->verificarRolUsuario($idUsuarioSesion, 1);
if (!$usuarioPermitido) {
    header('Location: ../Home/login.php');
    exit();
}

$nombreUsuarioSesion = $session->getUsuario()->getUsNombre();

$abmUsuario = new ABMUsuario();

// Separar usuarios habilitados y deshabilitados
$usuariosSeparados = $abmUsuario->separarUsuariosHabilitadosYDeshabilitados();
$usuariosHabilitados = $usuariosSeparados['habilitados'];
$usuariosDeshabilitados = $usuariosSeparados['deshabilitados'];
?>

<div class="container mt-5">
    <h1 class="text-center">Lista de Usuarios</h1>
    <div class="mb-4">
        <h3 class="text-left">Administrador: <?php echo $nombreUsuarioSesion; ?></h3>
    </div>
    <div id="mensaje" class="text-center"></div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($usuariosHabilitados as $usuario) {
                    echo "<tr>";
                    echo "<td>" . $usuario->getIdUsuario() . "</td>";
                    echo "<td>" . $usuario->getUsNombre() . "</td>";
                    echo "<td>" . $usuario->getUsMail() . "</td>";
                    echo "<td>";
                    echo "<div class='btn-group' role='group'>";
                    echo "<a href='formEdit.php?id=" . $usuario->getIdUsuario() . "' class='btn btn-warning btn-sm'>Editar</a>";
                    if ($usuario->getIdUsuario() !== $idUsuarioSesion) { // No permitir eliminar al administrador de la sesión
                        echo "<button class='btn btn-danger btn-sm eliminar-usuario' data-id='" . $usuario->getIdUsuario() . "'>Eliminar</button>";
                    }
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php if (count($usuariosDeshabilitados) > 0): ?>
        <h2 class="text-center mt-5">Usuarios Eliminados</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Email</th>
                        <th>Fecha Deshabilitado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($usuariosDeshabilitados as $usuario) {
                        echo "<tr>";
                        echo "<td>" . $usuario->getIdUsuario() . "</td>"; 
                        echo "<td>" . $usuario->getUsNombre() . "</td>";
                        echo "<td>" . $usuario->getUsMail() . "</td>";
                        echo "<td>" . $usuario->getUsDeshabilitado() . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?> 
</div>

<script>
$(document).ready(function() {
    $('.eliminar-usuario').on('click', function() {
        var idUsuario = $(this).data('id');

        $.ajax({
            url: '../Action/eliminarLogin.php',
            method: 'POST',
            data: {
                id: idUsuario,
                form_security_token: 'valor_esperado' // Token de seguridad
            },
            success: function(response) {
                const mensajeDiv = document.getElementById('mensaje');
                if (response.status === 'success') {
                    mensajeDiv.innerHTML = '<div class="alert alert-success text-center">Usuario eliminado correctamente.</div>';
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500); // Esperar 1.5 segundos antes de redirigir
                } else {
                    mensajeDiv.innerHTML = '<div class="alert alert-danger text-center">' + response.message + '</div>';
                }
            },
            error: function() {
                const mensajeDiv = document.getElementById('mensaje');
                mensajeDiv.innerHTML = '<div class="alert alert-danger text-center">Error al intentar eliminar el usuario. Por favor, inténtelo de nuevo.</div>';
            }
        });
    });
});
</script>

<?php include_once("../estructura/footer.php"); ?>