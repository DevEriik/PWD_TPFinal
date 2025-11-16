<?php
//Gestiona todos los usuarios, registra usuarios nuevos y actualiza datos.
session_start();

require_once __DIR__ . '/../Modelo/usuario.php';

class Control_Usuario {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function registrar() {
        header('Content-Type: application/json; charset=utf-8');

        $usnombre = trim($_POST['usnombre'] ?? '');
        $uspass   = trim($_POST['uspass'] ?? '');
        $usmail   = trim($_POST['usmail'] ?? '');

        if ($usnombre === '' || $uspass === '' || $usmail === '') {
            echo json_encode(['success' => false, 'error' => 'Complete todos los campos.']);
            return;
        }

        if (!filter_var($usmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Email inválido.']);
            return;
        }

        if ($this->usuarioModel->obtenerPorNombre($usnombre)) {
            echo json_encode(['success' => false, 'error' => 'El nombre de usuario ya existe.']);
            return;
        }

        $id = $this->usuarioModel->crearUsuario([
            'usnombre' => $usnombre,
            'uspass'   => $uspass, 
            'usmail'   => $usmail
        ]);

        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'No se pudo crear el usuario.']);
            return;
        }

        $idrolCliente = 2;
        $this->usuarioModel->asignarRol($id, $idrolCliente);

        $roles = $this->usuarioModel->obtenerRoles($id);
        $_SESSION['usuario'] = [
            'idusuario' => $id,
            'usnombre'  => $usnombre,
            'usmail'    => $usmail,
            'roles'     => array_map(function($r){ return $r['idrol']; }, $roles)
        ];

        echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente.', 'usuario' => $_SESSION['usuario']]);
    }

    public function actualizar() {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']['idusuario'])) {
            echo json_encode(['success' => false, 'error' => 'No hay sesión activa.']);
            return;
        }

        $idusuario = $_SESSION['usuario']['idusuario'];
        $usmail = trim($_POST['usmail'] ?? '');
        $uspass = trim($_POST['uspass'] ?? '');
        $current_pass = trim($_POST['current_pass'] ?? '');

        if ($uspass !== '') {
            if ($current_pass === '') {
                echo json_encode(['success' => false, 'error' => 'Debe proporcionar la contraseña actual para cambiarla.']);
                return;
            }
            $user = $this->usuarioModel->obtenerPorId($idusuario);
            if (!$user || (string)$user['uspass'] !== (string)$current_pass) {
                echo json_encode(['success' => false, 'error' => 'Contraseña actual incorrecta.']);
                return;
            }
        }

        $updateData = [];
        if ($usmail !== '') $updateData['usmail'] = $usmail;
        if ($uspass !== '') $updateData['uspass'] = $uspass;

        if (empty($updateData)) {
            echo json_encode(['success' => false, 'error' => 'No hay datos para actualizar.']);
            return;
        }

        $ok = $this->usuarioModel->actualizarDatos($idusuario, $updateData);
        if ($ok) {
            if (isset($updateData['usmail'])) $_SESSION['usuario']['usmail'] = $updateData['usmail'];
            echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente.', 'usuario' => $_SESSION['usuario']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar los datos.']);
        }
    }
}