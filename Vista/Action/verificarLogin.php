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
    'message' => 'Error al verificar el login'
];

$datos = darDatosSubmitted();

$abmUsuario = new ABMUsuario();
$loginVerificado = $abmUsuario->verificarLogin($datos);
if ($loginVerificado) {
    $response['status'] = 'success';
    $response['message'] = 'Login verificado';
    // Obtener el rol del usuario
    $idRolUsuario = $abmUsuario->obtenerRolUsuario($datos['nombreUsuario']);
    if ($idRolUsuario == 1 || $idRolUsuario == 2) {
        $response['redirect'] = '../Home/paginaSegura.php';
    } else {
        $response['redirect'] = '../Home/productos.php';
    }
} else {
    $response['message'] = 'Credenciales incorrectas. Por favor, inténtelo de nuevo.';
}

echo json_encode($response);
exit;
?>