<?php
include_once '../../configuracion.php';


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';
$esVerificacion = isset($_POST['verificar']);

if (!$isAjax) {
    header('Location: ../Home/login.php');
    exit;
}


header('Content-Type: application/json');
$datos = darDatosSubmitted();
$abmUsuario = new ABMUsuario();

//  Lógica de Verificación (Ahora delegada al ABM)
if ($esVerificacion) {
    // El Action ya no busca ni cuenta, solo pregunta al ABM
    $resultado = $abmUsuario->verificarDuplicados($datos['usnombre'], $datos['usmail']);
    echo json_encode($resultado);
    exit;
}

//  Lógica de Registro (Delegada al ABM)
if (!$isPost || !$isValidToken) {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}

$exito = $abmUsuario->registrarUsuario($datos);


if ($exito) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Registro exitoso.',
        'redirect' => '../Home/login.php?registro=exitoso'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se pudo registrar. El usuario o el email ya existen.',
        'nombreExiste' => true 
    ]);
}
exit;
?>