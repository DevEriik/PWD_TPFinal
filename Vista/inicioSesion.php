<?php 
//Muestra si el usuario pudo entrar correctamente.
session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
    <link rel="stylesheet" href="../public/css/estilos.css">
</head>
<body>

<h1>TP Final</h1>

<div id="menu"></div>

<?php if (!isset($_SESSION['usuario'])): ?>
    <p><a href="login.php">Iniciar sesión</a></p>
<?php else: ?>
    <p>Bienvenido, <strong><?= $_SESSION['usuario']['usnombre'] ?></strong></p>
    <button id="btn-logout">Cerrar Sesión</button>
<?php endif; ?>

<script src="../public/js/app.js"></script>
<script>
    cargarMenu();
</script>

</body>
</html>