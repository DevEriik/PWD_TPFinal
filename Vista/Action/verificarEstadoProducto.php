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
    'estado' => null,
    'message' => 'Error al verificar el estado del producto.'
];

$datos = darDatosSubmitted();
if (isset($datos['idproducto'])) {
    $session = new Session();
    // id del usuario actual
    $idUsuario = $session->getUsuario()->getIdusuario();
    $idProducto = $datos['idproducto'];

    $ABMcompraitem = new ABMCompraItem();
    $estado = $ABMcompraitem->verificarEstadoProducto($idProducto, $idUsuario);

    if ($estado !== null) {
        $response['status'] = 'success';
        $response['estado'] = $estado;
        $response['message'] = 'Estado del producto verificado correctamente.';
    } else {
        $response['message'] = 'No se pudo verificar el estado del producto.';
    }
}

echo json_encode($response);
exit();
?>