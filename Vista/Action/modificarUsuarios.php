<?php
include_once '../../configuracion.php';

header('Content-Type: application/json');
$response = [
    'status' => 'error',
    'message' => 'Error al modificar el usuario. Asegúrate de que el nombre o email no estén en la base de datos.',
    'redirect' => '../Home/formEdit.php'
];

$session = new Session();
$abmUsuario = new ABMUsuario();
$datos = darDatosSubmitted();

$usuarioModificado = $abmUsuario->modificarUsuario($datos);
if ($usuarioModificado) {
    $response = [
        'status' => 'success',
        'message' => 'Usuario modificado correctamente.',
        'redirect' => '../Home/formEdit.php'
    ];
}

echo json_encode($response);
exit();
?>