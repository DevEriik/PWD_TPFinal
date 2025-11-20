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
$idCompra = $datos['idcompra'];
$fechaFin = date('Y-m-d H:i:s');

// Instanciamos los controles necesarios
$ABMCompraEstado = new ABMCompraEstado();
$ABMCompra = new ABMCompra();

//  Delegar al Control (MVC Puro)
// La lógica de cambiar estado y restar stock está DENTRO de esta función del ABM
$exito = $ABMCompraEstado->enviarCompra($idCompra, $fechaFin);


$response = [
    'status' => 'error',
    'message' => 'Error al enviar la compra (Revise stock o estado).',
    'redirect' => '../Home/ordenes.php'
];

if ($exito) {
    // Buscamos datos del cliente solo para el mail
    $datosCliente = $ABMCompra->clienteAsociadoALaCompra($idCompra);
    
    $response['status'] = 'success';
    $response['message'] = 'Producto Enviado con éxito.';
    $response['toName'] = $datosCliente['name'];
    $response['toEmail'] = $datosCliente['email'];
}

echo json_encode($response);
exit;
?>