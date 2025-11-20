<?php
include_once '../../configuracion.php';

// 1. Validaciones de Seguridad (Se mantienen igual)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPostOrGet = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

if (!$isAjax && (!$isPostOrGet || !$isValidToken)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}

// 2. Preparación
date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: application/json');

$session = new Session();
$UsuarioActual = $session->getUsuario(); // Obtenemos el objeto usuario
$ABMCompra = new ABMCompra();

// 3. Delegar al Control (MVC Puro)
// El Action solo llama a la función y espera la respuesta
$resultado = $ABMCompra->procesarConfirmacionCompra($UsuarioActual);

// 4. Preparar respuesta para el cliente
$response = $resultado; // Ya trae 'status' y 'message' desde el ABM

// Agregamos datos extra para el EmailJS solo si fue exitoso
if ($response['status'] === 'success') {
    $response['toName'] = $UsuarioActual->getUsnombre();
    $response['toEmail'] = $UsuarioActual->getUsmail();
}

// Siempre redirigimos al carrito (ya sea para mostrar éxito o el error de stock)
$response['redirect'] = '../Home/carrito.php';

echo json_encode($response);
exit;
?>