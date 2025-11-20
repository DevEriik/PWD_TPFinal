<?php
include_once '../../configuracion.php';

// Verifica si es una solicitud AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPostOrGet = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

if (!$isAjax && (!$isPostOrGet || !$isValidToken)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}

date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Error al confirmar la compra.',
    'redirect' => '../Home/carrito.php'
];

$session = new Session();
$fechaFin = date('Y-m-d H:i:s');
$UsuarioActual = $session->getUsuario();

$ABMcompraEstado = new ABMCompraEstado;
$compraIniciada = $ABMcompraEstado->buscarCompraIniciada($UsuarioActual->getIdusuario());

if ($compraIniciada !== null) {
    // No usamos dismount para mantenerlo como objeto y usar getters
    $idCompra = $compraIniciada->getIdcompra();

    $ABMcompraItem = new ABMCompraItem();
    $ABMproducto = new ABMProducto();

    // Obtengo todos los items del carrito
    $itemsCarrito = $ABMcompraItem->buscar(['idcompra' => $idCompra]);
    $errorStock = false;
    $mensajeError = "";

    foreach ($itemsCarrito as $item) {
        $producto = $item->getObjProducto();
        $cantidadPedida = $item->getCicantidad();
        $stockActual = $producto->getProcantstock();
        $nombreProd = $producto->getPronombre();

        // Si pedimos más de lo que hay
        if ($cantidadPedida > $stockActual) {
            $errorStock = true;
            $mensajeError = "No hay suficiente stock de '$nombreProd'. Disponibles: $stockActual.";
            break; // Cortamos el bucle al primer error
        }
    }

    if ($errorStock) {
        echo json_encode([
            'status' => 'stock_error',
            'message' => $mensajeError
        ]);
        exit; // DETENEMOS TODO AQUÍ
    }

    // Si hay stock, procedemos a confirmar y RESTAR
    $CompraConfirmada = $ABMcompraEstado->confirmarCompra($idCompra, $fechaFin);
    
    if($CompraConfirmada){
        // Obtenemos datos para el mail
        $response['status'] = 'success';
        $response['message'] = 'Compra confirmada.';
        $response['toName'] = $UsuarioActual->getUsnombre();
        $response['toEmail'] = $UsuarioActual->getUsmail();
        $response['redirect'] = '../Home/carrito.php';
    }
} 
echo json_encode($response);
exit;
?>