<?php
include_once '../../configuracion.php';

// Verifica si es una solicitud AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Verifica si es una solicitud POST o GET
$isPostOrGet = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET';

// Verifica si el token de seguridad es válido (solo para POST/GET)
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

// Si no es AJAX ni una solicitud válida POST/GET con el token, redirige
if (!$isAjax && (!$isPostOrGet || !$isValidToken)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}
// Configurar la zona horaria a Argentina
date_default_timezone_set('America/Argentina/Buenos_Aires');
//--------------------------------------------------------------------------------------------
header('Content-Type: application/json');
// voy creando el arreglo asociativo que voy a pasar como respuesta en formato JSON
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
    $compraIniciada = dismount($compraIniciada);
    $idCompra = $compraIniciada['idcompra'];
// --------------------------------------------
// VALIDACIÓN DE STOCK ANTES DE CONFIRMAR COMPRA
// --------------------------------------------
//$ABMcompraItem = new ABMCompraItem();
//$ABMproducto = new ABMProducto();

// Obtengo todos los productos del carrito
//$carrito = $ABMcompraItem->obtenerProductosCarrito($UsuarioActual->getIdusuario());
//$productos = $carrito['productosCarrito'];

//foreach ($productos as $prod) {
  //  $idProducto = $prod['idproducto'];
    //$cantidadPedida = $prod['Cantidad'];

    // Obtengo stock real actual
    //$productoBD = $ABMproducto->buscar(['idproducto' => $idProducto]);

    //if (!$productoBD) {
    //    echo json_encode([
     //       'status' => 'error',
     //       'message' => "Error: producto no encontrado (ID $idProducto)"
    //    ]);
    //    exit;
   // }

  //  $productoBD = $productoBD[0];
//    $stockActual = $productoBD->getProcantstock();

    // Si la cantidad pedida supera el stock → NO permitir compra
    //if ($cantidadPedida > $stockActual) {
      //  echo json_encode([
        //    'status' => 'stock_error',
        //    'message' => "No hay stock suficiente de '{$prod['Nombre']}'. Disponible: $stockActual"
        //]);
        //exit;
    //}
//}

    $CompraConfirmada = $ABMcompraEstado->confirmarCompra($idCompra, $fechaFin);
    if($CompraConfirmada){
        $UsuarioActual = dismount($UsuarioActual);
        $response['status'] = 'success';
        $response['message'] = 'Compra confirmada.';
        $response['toName'] = $UsuarioActual['usnombre'];
        $response['toEmail'] = $UsuarioActual['usmail'];
        $response['redirect'] = '../Home/carrito.php';
    }
} 
echo json_encode($response);
exit;
?>