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