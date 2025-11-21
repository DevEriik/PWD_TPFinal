<?php
include_once '../../configuracion.php';

// Validaciones de Seguridad
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
// En registro usualmente solo esperamos POST
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

// Validar también si es solo verificación de datos (el JS a veces manda verificar: true)
$esVerificacion = isset($_POST['verificar']);

if (!$isAjax) {
    // Si intentan entrar directo por URL, los sacamos
    header('Location: ../Home/login.php');
    exit;
}


header('Content-Type: application/json');
$datos = darDatosSubmitted();

$abmUsuario = new ABMUsuario();
$response = [];


if ($esVerificacion) {
    // Si el JS solo quiere saber si el usuario existe antes de enviar
    $nombreExiste = count($abmUsuario->buscar(['usnombre' => $datos['usnombre']])) > 0;
    $emailExiste = count($abmUsuario->buscar(['usmail' => $datos['usmail']])) > 0;
    
    echo json_encode([
        'nombreExiste' => $nombreExiste,
        'emailExiste' => $emailExiste
    ]);
    exit;
}

// Delegar al Control (Registro Real)
// La función registrarUsuario del ABM se encarga del hash y la inserción
$exito = $abmUsuario->registrarUsuario($datos);

// 5. Responder
if ($exito) {
    $response['status'] = 'success';
    $response['message'] = 'Registro exitoso.';
    $response['redirect'] = '../Home/login.php?registro=exitoso';
} else {
    $response['status'] = 'error';
    // Como el ABM devuelve false genérico, asumimos duplicidad
    $response['message'] = 'No se pudo registrar. El usuario o el email ya existen.';
    $response['nombreExiste'] = true; // Bandera para que el JS marque el error
}

echo json_encode($response);
exit;
?>