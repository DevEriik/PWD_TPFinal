<?php
include_once '../../configuracion.php';


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPostOrGet = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

if (!$isAjax) {
    header('Location: ../Home/login.php');
    exit;
}

if (!$isPostOrGet || !$isValidToken) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}


header('Content-Type: application/json');
$datos = darDatosSubmitted();

//  Validar Sesión (CRÍTICO para evitar Fatal Errors)
$session = new Session();
if (!$session->activa() || !$session->validar()) {
    echo json_encode([
        'status' => 'error',
        'estado' => null,
        'message' => 'La sesión ha expirado.',
        'redirect' => '../Home/login.php'
    ]);
    exit;
}

$response = [
    'status' => 'error',
    'estado' => null,
    'message' => 'Error al verificar el estado del producto.'
];


if (isset($datos['idproducto'])) {
    
    $idUsuario = $session->getUsuario()->getIdusuario();
    $idProducto = $datos['idproducto'];

    $ABMcompraitem = new ABMCompraItem();
    
    // Delegamos al Control
    $estado = $ABMcompraitem->verificarEstadoProducto($idProducto, $idUsuario);

    if ($estado !== null) {
        $response['status'] = 'success';
        $response['estado'] = $estado;
        $response['message'] = 'Estado del producto verificado correctamente.';
    } else {
        $response['message'] = 'No se pudo verificar el estado del producto.';
    }
} else {
    $response['message'] = 'Falta el ID del producto.';
}

echo json_encode($response);
exit;
?>