<?php
session_start();

require_once __DIR__ . '/../Modelo/usuario.php';

class Control_Session {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function login() {
        header('Content-Type: application/json; charset=utf-8');

        $usnombre = $_POST['usnombre'] ?? null;
        $uspass = $_POST['uspass'] ?? null;

        if (!$usnombre || !$uspass) {
            echo json_encode(['success' => false, 'error' => 'Faltan credenciales.']);
            return;
        }

        $user = $this->usuarioModel->verificarCredenciales($usnombre, $uspass);
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Usuario o contraseña incorrectos.']);
            return;
        }

        $roles = $this->usuarioModel->obtenerRoles($user['idusuario']);

        $_SESSION['usuario'] = [
            'idusuario' => $user['idusuario'],
            'usnombre'  => $user['usnombre'],
            'usmail'    => $user['usmail'],
            'roles'     => array_map(function($r){ return $r['idrol']; }, $roles)
        ];

        echo json_encode(['success' => true, 'usuario' => $_SESSION['usuario']]);
    }

    public function logout() {
        header('Content-Type: application/json; charset=utf-8');
        session_unset();
        session_destroy();
        echo json_encode(['success' => true]);
    }

    public static function verificarSession() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['idusuario']);
    }

    public function whoami() {
        header('Content-Type: application/json; charset=utf-8');
        if (self::verificarSession()) {
            echo json_encode(['logged' => true, 'usuario' => $_SESSION['usuario']]);
        } else {
            echo json_encode(['logged' => false]);
        }
    }
}