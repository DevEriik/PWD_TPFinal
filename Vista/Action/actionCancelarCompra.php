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

$datos = darDatosSubmitted();
$session = new Session();
$UsuarioActual = $session->getUsuario();

// Delegar al Control (MVC Puro)
$ABMcompraEstado = new ABMCompraEstado();

// El Action llama a UNA sola función y recibe la respuesta lista
$response = $ABMcompraEstado->procesarCancelacionCompleta($datos, $UsuarioActual);

echo json_encode($response);
exit;
?>