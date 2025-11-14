<?php
require_once __DIR__ . '/modeloBase.php';

class Usuario extends ModeloBase {
    protected $table = 'usuario';

    public function crearUsuario(array $data) {
    $sql = "INSERT INTO usuario (usnombre, uspass, usmail)
            VALUES (:usnombre, :uspass, :usmail)";

    $params = [
        ':usnombre' => $data['usnombre'],
        ':uspass' => password_hash($data['uspass'], PASSWORD_DEFAULT),
        ':usmail' => $data['usmail']
    ];

    $ok = $this->ejecutar($sql, $params);
    if ($ok) return $this->ultimoId();
    return false;
}

    public function obtenerPorNombre($usnombre) {
        $sql = "SELECT * FROM usuario WHERE usnombre = :usnombre LIMIT 1";
        $rows = $this->ejecutarSelect($sql, [':usnombre' => $usnombre]);
        return count($rows) ? $rows[0] : null;
    }

    public function obtenerPorId($idusuario) {
        $sql = "SELECT * FROM usuario WHERE idusuario = :idusuario LIMIT 1";
        $rows = $this->ejecutarSelect($sql, [':idusuario' => $idusuario]);
        return count($rows) ? $rows[0] : null;
    }

    public function verificarCredenciales($usnombre, $password) {
        $user = $this->obtenerPorNombre($usnombre);
        if (!$user) return false;
        $dbPass = (string)$user['uspass'];
        $inputPass = (string)$password;

        if (password_verify($inputPass, $dbPass)) {
            return $user;
        }

        return false;
    }

    public function asignarRol($idusuario, $idrol) {
        $sql = "INSERT INTO usuariorol (idusuario, idrol) VALUES (:idusuario, :idrol)";
        return $this->ejecutar($sql, [':idusuario' => $idusuario, ':idrol' => $idrol]);
    }

    public function obtenerRoles($idusuario) {
        $sql = "SELECT r.idrol, r.rodescripcion
                FROM rol r
                JOIN usuariorol ur ON ur.idrol = r.idrol
                WHERE ur.idusuario = :idusuario";
        return $this->ejecutarSelect($sql, [':idusuario' => $idusuario]);
    }

    public function actualizarDatos($idusuario, array $data) {
        $sets = [];
        $params = [':idusuario' => $idusuario];
        if (isset($data['usmail'])) {
            $sets[] = "usmail = :usmail";
            $params[':usmail'] = $data['usmail'];
        }
        if (isset($data['uspass'])) {
            $sets[] = "uspass = :uspass";
            $params[':uspass'] = $data['uspass'];
        }
        if (empty($sets)) return false;
        $sql = "UPDATE usuario SET " . implode(', ', $sets) . " WHERE idusuario = :idusuario";
        return $this->ejecutar($sql, $params);
    }
}