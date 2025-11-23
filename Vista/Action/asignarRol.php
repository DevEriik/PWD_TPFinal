<?php
include_once '../../configuracion.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPostOrGet = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';


if (!$isAjax && (!$isPostOrGet || !$isValidToken)) {
    header('Location: ../Home/login.php');
    exit;
}

header('Content-Type: application/json');
$response = [
    'status' => 'error',
    'message' => 'Error en la solicitud.'
];

$datos = darDatosSubmitted();

$abmUsuarioRol = new ABMUsuarioRol();
$rolAsignado = $abmUsuarioRol->asignarRolUnico($datos);

if($rolAsignado){
    $response['status'] = 'success';
    $response['message'] = 'Rol asignado correctamente.';
}

echo json_encode($response);
exit;
?>