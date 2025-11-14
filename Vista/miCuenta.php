<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: inicioSesion.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="../public/css/estilos.css">
</head>
<body>

<h1>Mi Cuenta</h1>

<p><strong>Usuario:</strong> <?= $_SESSION['usuario']['usnombre'] ?></p>
<p><strong>Email actual:</strong> <?= $_SESSION['usuario']['usmail'] ?></p>

<form id="form-mi-cuenta">
    <label>Nuevo Email</label>
    <input type="email" name="usmail">

    <label>Contraseña actual</label>
    <input type="password" name="current_pass">

    <label>Nueva Contraseña</label>
    <input type="password" name="uspass">

    <button type="submit">Actualizar</button>
</form>

<div id="micuenta-msg"></div>

<p><a href="inicioSesion.php">Volver al inicio</a></p>

<script src="../public/js/app.js"></script>
</body>
</html>