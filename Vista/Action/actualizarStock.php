<?php
include_once '../../configuracion.php';


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPostOrGet = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';


if (!$isAjax && (!$isPostOrGet || !$isValidToken)) {
    header('Location: ../Home/login.php');
    exit;
}
//---------------------------------------------------

header('Content-Type: application/json');
$response = [
    'status' => 'error',
    'message' => 'Error al actualizar el stock',
    'redirect' => '../Home/stock.php'
];
$datos = darDatosSubmitted();

$ABMProducto = new ABMProducto();

$stockActualizado = $ABMProducto->actualizarStock($datos);

if ($stockActualizado) {
    $response['status'] = 'success';
    $response['message'] = 'Stock actualizado correctamente';
    $response['redirect'] = '../Home/stock.php';
}

echo json_encode($response);
exit;
?>