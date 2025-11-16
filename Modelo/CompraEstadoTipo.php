<?php

class CompraEstadoTipo {

    private $idcompraestadotipo;
    private $cetdescripcion;
    private $cetdetalle;
    private $mensajeoperacion;

    public function __construct() {
        $this->idcompraestadotipo = 0;
        $this->cetdescripcion = '';
        $this->cetdetalle = '';
        $this->mensajeoperacion = '';
    }

    /**
     * Obtener el valor de idcompraestadotipo
     */ 
    public function getIdcompraestadotipo() {
        return $this->idcompraestadotipo;
    }

    /**
     * Establecer el valor de idcompraestadotipo
     *
     */ 
    public function setIdcompraestadotipo($idcompraestadotipo) {
        $this->idcompraestadotipo = $idcompraestadotipo;
    }

    /**
     * Obtener el valor de cetdescripcion
     */ 
    public function getCetdescripcion() {
        return $this->cetdescripcion;
    }

    /**
     * Establecer el valor de cetdescripcion
     *
     */ 
    public function setCetdescripcion($cetdescripcion) {
        $this->cetdescripcion = $cetdescripcion;
    }

    /**
     * Obtener el valor de cetdetalle
     */ 
    public function getCetdetalle() {
        return $this->cetdetalle;
    }

    /**
     * Establecer el valor de cetdetalle
     *
     */ 
    public function setCetdetalle($cetdetalle) {
        $this->cetdetalle = $cetdetalle;
    }

    /**
     * Obtener el valor de mensajeoperacion
     */ 
    public function getMensajeoperacion() {
        return $this->mensajeoperacion;
    }

    /**
     * Establecer el valor de mensajeoperacion
     *
     */ 
    public function setMensajeoperacion($mensajeoperacion) {
        $this->mensajeoperacion = $mensajeoperacion;
    }

    /**
     * Establecer los valores de los atributos de la clase
     */
    public function setear($idcompraestadotipo, $cetdescripcion, $cetdetalle) {
        $this->setIdcompraestadotipo($idcompraestadotipo);
        $this->setCetdescripcion($cetdescripcion);
        $this->setCetdetalle($cetdetalle);
    }

    /**
     * Cargar un objeto desde la base de datos
     */
    public function cargar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "SELECT * FROM compraestadotipo WHERE idcompraestadotipo = " . $this->getIdcompraestadotipo();
        if ($base->Iniciar()) {
            $res = $base->Ejecutar($sql);
            if ($res > -1) {
                if ($res > 0) {
                    $row = $base->Registro();
                    $this->setear($row['idcompraestadotipo'], $row['cetdescripcion'], $row['cetdetalle']);
                    $resp = true;
                }
            }
        } else {
            $this->setMensajeoperacion("compraestadotipo->cargar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Insertar un objeto en la base de datos
     */
    public function insertar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "INSERT INTO compraestadotipo (cetdescripcion, cetdetalle) VALUES ('" . $this->getCetdescripcion() . "', '" . $this->getCetdetalle() . "');";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraestadotipo->insertar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraestadotipo->insertar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Modificar un objeto en la base de datos
     */
    public function modificar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "UPDATE compraestadotipo SET cetdescripcion = '" . $this->getCetdescripcion() . "', cetdetalle = '" . $this->getCetdetalle() . "' WHERE idcompraestadotipo = " . $this->getIdcompraestadotipo() . ";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraestadotipo->modificar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraestadotipo->modificar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Eliminar un objeto de la base de datos
     */
    public function eliminar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "DELETE FROM compraestadotipo WHERE idcompraestadotipo = " . $this->getIdcompraestadotipo() . ";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraestadotipo->eliminar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraestadotipo->eliminar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Listar objetos de la base de datos
     */
    public function listar($parametro = "") {
        $arreglo = array();
        $base = new BaseDatos();
        $sql = "SELECT * FROM compraestadotipo ";
        if ($parametro != "") {
            $sql .= 'WHERE ' . $parametro;
        }
        $res = $base->Ejecutar($sql);
        if ($res > -1) {
            if ($res > 0) {
                while ($row = $base->Registro()) {
                    $objCompraEstadoTipo = new CompraEstadoTipo();
                    $objCompraEstadoTipo->setear($row['idcompraestadotipo'], $row['cetdescripcion'], $row['cetdetalle']);
                    array_push($arreglo, $objCompraEstadoTipo);
                }
            }
        } else {
            $this->setMensajeoperacion("compraestadotipo->listar: " . $base->getError());
        }

        return $arreglo;
    }

    /**
     * toString del objeto CompraEstadoTipo
     *
     * @return string
     */
    public function __toString() {
        return "ID CompraEstadoTipo: " . $this->getIdcompraestadotipo() . 
               ", Descripción: " . $this->getCetdescripcion() . 
               ", Detalle: " . $this->getCetdetalle();
    }
}
?>