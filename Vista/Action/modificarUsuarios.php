<?php
include_once '../../configuracion.php';


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

if (!$isAjax) {
    header('Location: ../Home/login.php');
    exit;
}

if (!$isPost || !$isValidToken) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}


header('Content-Type: application/json');
$datos = darDatosSubmitted();

// Verificar Sesión (Muy importante para acciones de edición)
$session = new Session();
if (!$session->activa() || !$session->validar()) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'La sesión ha expirado.', 
        'redirect' => '../Home/login.php'
    ]);
    exit;
}

// Delegar al Control
$abmUsuario = new ABMUsuario();

// La función modificarUsuario del ABM se encarga de validar duplicados y actualizar
$exito = $abmUsuario->modificarUsuario($datos);


$response = [];

if ($exito) {
    $response['status'] = 'success';
    $response['message'] = 'Usuario modificado correctamente.';
    // Redirigimos al listado o formulario de edición para ver los cambios
    $response['redirect'] = '../Home/actualizarUsuario.php'; 
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error al modificar. Verifique que el nombre o email no estén en uso.';
}

echo json_encode($response);
exit;
?>