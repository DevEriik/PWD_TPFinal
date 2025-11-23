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

$ABMProducto = new ABMProducto;
$session = new Session;

$colProductos = $ABMProducto->buscarArray(null);

$datos = [
    'redirect' => '../Home/stock.php',
    'status' => 'default',
    'colProds' => $colProductos
];

echo json_encode($datos);

?>