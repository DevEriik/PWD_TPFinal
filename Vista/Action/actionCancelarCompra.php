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
    'message' => 'Error al cancelar la compra.',
    'redirect' => '../Home/login.php'
];

$session = new Session();
$fechaFin = date('Y-m-d H:i:s');
$UsuarioActual = $session->getUsuario();

$datos = darDatosSubmitted();

$ABMcompraEstado = new ABMCompraEstado;
$cancelacionExitosa = $ABMcompraEstado->cancelarCompra($datos, $fechaFin, $UsuarioActual->getIdusuario());

if ($cancelacionExitosa) {
    $response['status'] = 'success';
    $response['message'] = 'operacion exitosa';

    if($datos['comprasRol'] === 'deposito'){
        $compra = new ABMCompra();
        $usuario = $compra->clienteAsociadoALaCompra($datos['idcompra']);
        $response['toName'] = $usuario['name'];
        $response['toEmail'] = $usuario['email'];
        $response['redirect'] = '../Home/ordenes.php';
    }else{
        $UsuarioActual = dismount($UsuarioActual);
        $response['toName'] = $UsuarioActual['usnombre'];
        $response['toEmail'] = $UsuarioActual['usmail'];
        $response['redirect'] = '../Home/carrito.php';
    }
} 

echo json_encode($response);
exit;
?>