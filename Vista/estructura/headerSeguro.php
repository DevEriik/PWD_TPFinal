<?php
include_once '../../configuracion.php';

//primero si no tenes los roles andate al sql y hacele un insert a rol con estos valores
//INSERT INTO `rol`(`idrol`, `rodescripcion`) VALUES ('1','Administrador')
//INSERT INTO `rol`(`idrol`, `rodescripcion`) VALUES ('2','Deposito')
//INSERT INTO `rol`(`idrol`, `rodescripcion`) VALUES ('3','Cliente')

//ahora andate al bdcarritocompras.sql
//busca la tabla del menu deje dos insert con las posibles opciones del menu (estas las podemos cambiar pero es para probar ahora)

//busca la tabla menuRol hace los dos insert que tengo tambien ahi

$session = new Session();
if (!$session->activa() || !$session->validar()) {
    
    header('Location: login.php');
    exit();
}

$userID = $session->getRol()[0]->getObjRol()->getIdrol();

$abmMenuRol = new ABMMenuRol();

$menus = $abmMenuRol->buscar(['idrol'=>$userID]);

/* estilos personalizados para el navbar dependiendo el rol */
if($userID == 1){ #administrador
    $colorFondo = ' bg-warning ';
}elseif($userID == 2){ #deposito
    $colorFondo = ' bg-secondary ';
}else{ #cliente
    $colorFondo = ' bg-success ';
}


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
    <!--mailsjs-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript" src="../js/script.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light <?= $colorFondo ?>">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">comahu<p class="text-primary d-inline"><b>e-shop</b></p></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php
                foreach ($menus as $menu) {
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="' . $menu->getObjMenu()->getMedescripcion() . '">' . $menu->getObjMenu()->getMenombre() . '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
</body>
</html>