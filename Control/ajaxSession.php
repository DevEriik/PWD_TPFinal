<?php
session_start();
require_once __DIR__ . '/../Modelo/usuario.php';

$usuarioModel = new Usuario();

$accion = $_GET['a'] ?? '';

switch ($accion) {
    
    case 'login':

        $usnombre = trim($_POST['usnombre'] ?? '');
        $uspass = trim($_POST['uspass'] ?? '');

        if ($usnombre === '' || $uspass === '') {
            echo json_encode(['success' => false, 'error' => 'Complete todos los campos.']);
            exit;
        }

        $user = $usuarioModel->obtenerPorNombre($usnombre);
        if (!$user || !password_verify($uspass, $user['uspass'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incorrectos.']);
            exit;
        }

        $roles = $usuarioModel->obtenerRoles($user['idusuario']);

        $_SESSION['usuario'] = [
            'idusuario' => $user['idusuario'],
            'usnombre' => $user['usnombre'],
            'usmail' => $user['usmail'],
            'roles' => array_column($roles, 'idrol')
        ];

        echo json_encode(['success' => true]);
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
}