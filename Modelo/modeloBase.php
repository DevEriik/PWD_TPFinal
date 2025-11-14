<?php
require_once __DIR__ . '/../util/conexion.php';

class ModeloBase {
    protected $db;

    public function __construct() {
        $this->db = Conexion::obtenerConexion();
    }

    protected function ejecutarSelect($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function ejecutar($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    protected function ultimoId() {
        return $this->db->lastInsertId();
    }
}