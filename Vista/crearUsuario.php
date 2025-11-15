<?php 
//Muestra el formulario para crear un usuario nuevo.
session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../public/css/estilos.css">
</head>
<body>

<h1>Crear Usuario</h1>

<form id="form-registro" method="post" action="../Control/controlSession.php">
    <label>Usuario</label>
    <input type="text" name="usnombre" required>

    <label>Contraseña</label>
    <input type="password" name="uspass" required>

    <label>Email</label>
    <input type="email" name="usmail" required>

    <button type="submit">Registrarse</button>
</form>

<div id="registro-msg"></div>

<p><a href="login.php">Volver al login</a></p>

<script src="../public/js/app.js"></script>
</body>
</html>