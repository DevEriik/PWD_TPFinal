<?php
include_once '../../configuracion.php';


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isValidToken = isset($_POST['form_security_token']) && $_POST['form_security_token'] === 'valor_esperado';

// Si intentan entrar por URL directa (GET)
if (!$isAjax) {
    header('Location: ../Home/login.php');
    exit;
}

// Si fallan los tokens o el método
if (!$isPost || !$isValidToken) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}


header('Content-Type: application/json');
$datos = darDatosSubmitted();

// Delegar al Control
$abmUsuario = new ABMUsuario();

// La función verificarLogin del ABM verifica pass y, si es correcto, INICIA LA SESIÓN.
$exito = $abmUsuario->verificarLogin($datos);

// Responder y Enrutar
$response = [];

if ($exito) {
    $response['status'] = 'success';
    $response['message'] = 'Login verificado. Redirigiendo...';
    
    // Lógica de Enrutamiento (Routing):
    // Como la sesión ya se inició en el ABM, podemos preguntar a la clase Session qué rol tiene.
    $session = new Session();
    $roles = $session->getRol();
    $idRol = 3; // Rol por defecto (Cliente)

    if ($roles != null && count($roles) > 0) {
        $idRol = $roles[0]->getObjRol()->getIdrol();
    }

    // Decidimos a dónde va según su rol
    if ($idRol == 1 || $idRol == 2) { // Admin o Depósito
        $response['redirect'] = '../Home/paginaSegura.php'; 
    } else { // Cliente
        $response['redirect'] = '../Home/productos.php';
    }

} else {
    $response['status'] = 'error';
    $response['message'] = 'Credenciales incorrectas o usuario deshabilitado.';
}

echo json_encode($response);
exit;
?>