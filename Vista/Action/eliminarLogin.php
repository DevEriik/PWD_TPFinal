<?php
include_once '../../configuracion.php';


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

if (!$isAjax) {
    // Si intentan entrar por URL directa
    header('Location: ../Home/login.php');
    exit;
}


header('Content-Type: application/json');
$datos = darDatosSubmitted();

// Verificar Sesión (Es crítico porque es una acción administrativa)
$session = new Session();
if (!$session->activa() || !$session->validar()) {
    // Devolvemos JSON con instrucción de redirección, NO un header Location directo
    echo json_encode([
        'status' => 'error', 
        'message' => 'La sesión ha expirado.', 
        'redirect' => '../Home/login.php'
    ]);
    exit;
}

// Delegar al Control
$abmUsuario = new ABMUsuario();
$exito = false;

// Validamos que venga el ID
if (isset($datos['id'])) {
    // La función deshabilitarUsuario del ABM se encarga de la "Baja Lógica"
    // (Poner fecha de baja en lugar de borrar la fila)
    $exito = $abmUsuario->deshabilitarUsuario($datos['id']);
}


$response = [];

if ($exito) {
    $response['status'] = 'success';
    $response['message'] = 'Usuario eliminado correctamente.';
    // Redirigimos a la misma página para refrescar la lista
    $response['redirect'] = '../Home/actualizarUsuario.php'; 
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error al intentar eliminar el usuario.';
}

echo json_encode($response);
exit;
?>