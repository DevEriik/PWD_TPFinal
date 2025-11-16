<?php

require_once __DIR__ . '/../util/conexion.php'; 


class ModeloBase {
    
    protected $db;           
    protected $tabla;        
    protected $id_nombre;    

    public function __construct() {
        $this->db = Conexion::obtenerConexion();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    
    /**
     * Busca un único registro en la tabla por su clave primaria ($id).
     */
    public function buscar($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE {$this->id_nombre} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Lista todos los registros de la tabla.
     */
    public function listar() {
        $sql = "SELECT * FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Inserta un nuevo registro en la tabla.
     */
    public function insertar(array $datos) {
        $columnas = implode(', ', array_keys($datos));
        $placeholders = ':' . implode(', :', array_keys($datos));
        
        $sql = "INSERT INTO {$this->tabla} ({$columnas}) VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos); 
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Modifica un registro existente en la tabla.
     */
    public function modificar(array $datos, $id) {
        $set = [];
        foreach (array_keys($datos) as $col) {
            $set[] = "{$col} = :{$col}";
        }
        $set_string = implode(', ', $set);
        
        $sql = "UPDATE {$this->tabla} SET {$set_string} WHERE {$this->id_nombre} = :id";
        
        $datos['id'] = $id; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        
        return $stmt->rowCount();
    }
    
    /**
     * Elimina un registro por su clave primaria.
     */
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE {$this->id_nombre} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    /**
     * Ejecuta un SELECT y retorna los resultados (igual que listar/buscar, pero más genérico).
     */
    protected function ejecutarSelect($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta INSERT, UPDATE o DELETE y retorna TRUE/FALSE (similar a modificar/eliminar).
     */
    protected function ejecutar($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Retorna el ID del último registro insertado.
     */
    protected function ultimoId() {
        return $this->db->lastInsertId();
    }
}

/**require_once __DIR__ . '/../util/conexion.php';

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
} **/