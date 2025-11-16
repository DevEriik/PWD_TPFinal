<?php
include_once("../../configuracion.php");
// $objSession = new Session();
// $usuario = $objSession->getUsuario(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/jquery-3.7.1.js"></script>

    <!--ESTO ES LO QUE YO TENIA EN MI HEADER DESPUES LO VAMO VIENDO-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/crypto-js.min.js"></script>
    <script src="../js/jquery.validate.min.js"></script>
    <!-- <script src="../js/validaciones.js"></script> -->
    
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">comahu<p class="text-primary d-inline">e-shop</p></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../Home/productos.php">Productos</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="../Home/login.php">Iniciar Sesion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>