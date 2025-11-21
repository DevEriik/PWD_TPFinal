<?php
include_once '../../configuracion.php';


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
// Para agregar al carrito solemos usar POST
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

if (!$isAjax) {
    // Si intentan entrar por URL, redirigir al login o home
    header('Location: ../Home/login.php');
    exit;
}


header('Content-Type: application/json');

// Leer el JSON que envía Javascript (fetch/axios envían raw body)
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $session = new Session();
    $ABMCompra = new ABMCompra();
    
    // Obtenemos datos necesarios
    $idUsuario = $session->getUsuario()->getIdusuario();
    $idProducto = $data['idproducto'];
    $cantSeleccionada = $data['prodCantSelec'];

    // Delegar al Control
    // La función actualizarCompra del ABM se encarga de:
    // - Buscar si hay carrito abierto.
    // - Si no, crearlo.
    // - Agregar o sumar el producto.
    $exito = $ABMCompra->actualizarCompra($idUsuario, $idProducto, $cantSeleccionada);


    if ($exito) {
        echo json_encode(['status' => 'success', 'message' => 'Producto agregado al carrito.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo agregar el producto.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos no válidos o vacíos.']);
}

exit;
?>