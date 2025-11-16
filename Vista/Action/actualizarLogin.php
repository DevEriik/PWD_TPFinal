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

header('Content-Type: application/json');
$response = [
    'status' => 'error',
    'message' => 'No se pudo modificar el usuario. Nombre o email ya están en la base de datos.',
    'redirect' => '../Home/cuenta.php'
];

$session = new Session();
$abmUsuario = new ABMUsuario();

$datos = darDatosSubmitted();

$usuarioActualizado = $abmUsuario->actualizarUsuario($datos);

if ($usuarioActualizado) {
    $response['status'] = 'success';
    $response['message'] = 'Actualización exitosa.';
    $response['redirect'] = '../Home/cuenta.php';
}
echo json_encode($response);
exit;
?>