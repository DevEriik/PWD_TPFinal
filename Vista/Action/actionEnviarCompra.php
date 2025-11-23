<?php
include_once '../../configuracion.php';


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

//  Obtener Datos
$datos = darDatosSubmitted();
$idCompra = $datos['idcompra'];

//  Delegar TOTALMENTE al Control
$ABMCompraEstado = new ABMCompraEstado();

// El ABM hace todo: cambio de estado, stock y búsqueda de datos del cliente
$response = $ABMCompraEstado->procesarEnvioCompra($idCompra);

//  Responder
echo json_encode($response);
exit;
?>