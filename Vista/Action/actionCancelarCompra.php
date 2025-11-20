<?php
include_once '../../configuracion.php';

// 1. Validaciones de Seguridad (AJAX y Token)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPostOrGet = $_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

if (!$isAjax && (!$isPostOrGet || !$isValidToken)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}

// 2. Preparación de Entorno
date_default_timezone_set('America/Argentina/Buenos_Aires');
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Error al cancelar la compra.',
    'redirect' => '../Home/carrito.php' // Redirección por defecto
];

// 3. Obtener Datos y Sesión
$datos = darDatosSubmitted();
$session = new Session();
$UsuarioActual = $session->getUsuario();
$idUsuarioSesion = $UsuarioActual->getIdusuario();
$fechaFin = date('Y-m-d H:i:s');

// 4. Llamar al CONTROL (ABM)
$ABMcompraEstado = new ABMCompraEstado();

// Delegamos la lógica de cancelación al ABM
// (El ABM se encargará de buscar la compra correcta y cambiar los estados)
$cancelacionExitosa = $ABMcompraEstado->cancelarCompra($datos, $fechaFin, $idUsuarioSesion);

// 5. Procesar Respuesta
if ($cancelacionExitosa) {
    $response['status'] = 'success';
    $response['message'] = 'Operación exitosa';

    // Lógica para el envío de MAIL (Definir destinatario)
    if (isset($datos['comprasRol']) && $datos['comprasRol'] === 'deposito') {
        // CASO A: Cancela el Depósito
        // Necesitamos buscar los datos del dueño de la compra
        $ABMCompra = new ABMCompra();
        $datosCliente = $ABMCompra->clienteAsociadoALaCompra($datos['idcompra']);
        
        $response['toName'] = $datosCliente['name'];
        $response['toEmail'] = $datosCliente['email'];
        $response['redirect'] = '../Home/ordenes.php';
    
    } else {
        // CASO B: Cancela el Cliente (Usuario logueado)
        // Usamos los datos de la sesión directamente (Getters)
        $response['toName'] = $UsuarioActual->getUsnombre();
        $response['toEmail'] = $UsuarioActual->getUsmail();
        $response['redirect'] = '../Home/carrito.php';
    }
}

echo json_encode($response);
exit;
?>