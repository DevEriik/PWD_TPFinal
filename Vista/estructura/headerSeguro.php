<?php
include_once '../../configuracion.php';

// Llamamos al Control
$controlHeader = new ControlHeader();

// Esta función ahora se encarga de validar. 
// Si no es seguro, redirige sola. Si vuelve, es que todo está bien.
$datosHeader = $controlHeader->obtenerDatosHeader();

// RECUPERAMOS LA SESIÓN para que esté disponible en el resto de las páginas
$session = $datosHeader['objSession'];

// Extraemos variables visuales
$menus = $datosHeader['menus'];
$colorFondo = $datosHeader['colorFondo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript" src="../js/script.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light <?php echo $colorFondo; ?>">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Cel<p class="text-primary d-inline"><b>u-store</b></p></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php
                foreach ($menus as $menu) {
                    $objMenu = $menu->getObjMenu();
                    $deshabilitado = $objMenu->getMedeshabilitado();

                    if ($deshabilitado == null || $deshabilitado == '0000-00-00 00:00:00') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="' . $objMenu->getMedescripcion() . '">' . $objMenu->getMenombre() . '</a>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</nav>