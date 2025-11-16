<?php
//Es llamado por app.js para registrar y actualizar usuarios sin recargar la pagina.
require_once __DIR__ . '/controlUsuario.php';

$control = new Control_Usuario();

$accion = $_GET['a'] ?? '';

switch ($accion) {
    case 'registrar':
        $control->registrar();
        break;

    case 'actualizar':
        $control->actualizar();
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
}