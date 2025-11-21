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

if (!$isPost || !$isValidToken) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}


header('Content-Type: application/json');
$datos = darDatosSubmitted();

// Delegar al Control
$abmUsuario = new ABMUsuario();

// La función actualizarUsuario del ABM se encarga de la lógica
// (verificar si cambió el pass, si el mail está libre, etc.)
$exito = $abmUsuario->actualizarUsuario($datos);


$response = [];

if ($exito) {
    $response['status'] = 'success';
    $response['message'] = 'Datos actualizados correctamente.';
    $response['redirect'] = '../Home/cuenta.php';
} else {
    $response['status'] = 'error';
    $response['message'] = 'No se pudieron actualizar los datos (Verifique los campos).';
}

echo json_encode($response);
exit;
?>