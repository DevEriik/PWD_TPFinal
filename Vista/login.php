<?php 
//Muestra el formulario de incio de sesion.
session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../public/css/estilos.css">
</head>
<body>

<h1>Iniciar sesión</h1>

<form id="form-login">
    <label>Usuario</label>
    <input type="text" name="usnombre" required>

    <label>Contraseña</label>
    <input type="password" name="uspass" required>

    <button type="submit">Ingresar</button>
</form>

<div id="login-msg"></div>

<p>¿No tenes cuenta? <a href="crearUsuario.php">Crear una</a></p>

<script src="../public/js/app.js"></script>
</body>
</html>