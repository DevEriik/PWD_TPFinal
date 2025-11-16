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
    header('Location: ../Home/login.php');
    exit;
}

// Configurar la zona horaria a Argentina
date_default_timezone_set('America/Argentina/Buenos_Aires');

$session = new Session();
if (!$session->activa() || !$session->validar()) {
    header('Location: ../Home/login.php');
    exit();
}

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Error al intentar eliminar el usuario',
    'redirect' => '../Home/actualizarUsuario.php'
];

$abmUsuario = new ABMUsuario();
$datos = darDatosSubmitted(); 

$deshabilitado = $abmUsuario->deshabilitarUsuario($datos['id']);
if ($deshabilitado) {
    $response['status'] = 'success';
    $response['message'] = 'Usuario eliminado correctamente';
    $response['redirect'] = '../Home/actualizarUsuario.php';
}

echo json_encode($response);
exit();
?>