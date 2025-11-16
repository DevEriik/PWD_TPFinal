<?php include_once("../estructura/headerSeguro.php");
// creo un objeto de la clase abmUsuarioRol para llamar a la funcion verificarRolUsuario
$abmUsuarioRol = new ABMUsuarioRol(); 

// Verificar si el usuario tiene permisos para acceder a esta página (roles 1 y 2)
$usuarioPermitidoadmin = $abmUsuarioRol->verificarRolUsuario($session->getUsuario()->getIdUsuario(), 1);
$usuarioPermitidoDepo = $abmUsuarioRol->verificarRolUsuario($session->getUsuario()->getIdUsuario(), 2);
if (!$usuarioPermitidoadmin && !$usuarioPermitidoDepo) {
    header('Location: ../Home/login.php');
    exit();
} ?>

<div class="container mt-5">
    <h1 class="text-center">Bienvenido a la Página Segura</h1>
    <p class="text-center">Bienvenido, <?php echo $session->getUsuario()->getUsNombre(); ?></p>
    <div class="mt-5">
        <h2 class="text-center">Info de la sesión</h2>
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <p class="card-text"><strong>ID de Usuario:</strong> <?php echo $session->getUsuario()->getIdusuario(); ?></p>
                <p class="card-text"><strong>Nombre de Usuario:</strong> <?php echo $session->getUsuario()->getUsNombre(); ?></p>
                <p class="card-text"><strong>ID de Sesión:</strong> <?php echo session_id(); ?></p>
            </div>
        </div>
    </div>
</div>
<?php include_once("../estructura/footer.php"); ?>