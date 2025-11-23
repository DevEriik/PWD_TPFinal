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
$ABMUsuario = new ABMUsuario;
$session = new Session;

$colUsers = $ABMUsuario->buscarArray(null);
// almaceno al usuario actual en formato de arreglo asociativo
$userActual = $ABMUsuario->buscarArray($session->getUsuario());

$datos = [
    'usersBD' => $colUsers,
    'userActual' => $userActual
];

// codifico el arreglo asociativo en formato JSON
$jsonDatos = json_encode($datos);

echo $jsonDatos;

?>