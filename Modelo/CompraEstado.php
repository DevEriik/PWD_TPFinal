<?php

class CompraEstado {

    private $idcompraestado;
    private $objCompra;
    private $objCompraEstadoTipo;
    private $cefechaini;
    private $cefechafin;
    private $mensajeoperacion;

    public function __construct() {
        $this->idcompraestado = 0;
        $this->objCompra = new Compra();
        $this->objCompraEstadoTipo = new CompraEstadoTipo();
        $this->cefechaini = null;
        $this->cefechafin = null;
        $this->mensajeoperacion = '';
    }

    /**
     * Obtener el valor de idcompraestado
     */ 
    public function getIdcompraestado() {
        return $this->idcompraestado;
    }

    /**
     * Establecer el valor de idcompraestado
     *
     */ 
    public function setIdcompraestado($idcompraestado) {
        $this->idcompraestado = $idcompraestado;
    }

    /**
     * Obtener el valor de objCompra
     */ 
    public function getObjCompra() {
        return $this->objCompra;
    }

    /**
     * Establecer el valor de objCompra
     *
     */ 
    public function setObjCompra($objCompra) {
        $this->objCompra = $objCompra;
    }

    /**
     * Obtener el valor de objCompraEstadoTipo
     */ 
    public function getObjCompraEstadoTipo() {
        return $this->objCompraEstadoTipo;
    }

    /**
     * Establecer el valor de objCompraEstadoTipo
     *
     */ 
    public function setObjCompraEstadoTipo($objCompraEstadoTipo) {
        $this->objCompraEstadoTipo = $objCompraEstadoTipo;
    }

    /**
     * Obtener el valor de cefechaini
     */ 
    public function getCefechaini() {
        return $this->cefechaini;
    }

    /**
     * Establecer el valor de cefechaini
     *
     */ 
    public function setCefechaini($cefechaini) {
        $this->cefechaini = $cefechaini;
    }

    /**
     * Obtener el valor de cefechafin
     */ 
    public function getCefechafin() {
        return $this->cefechafin;
    }

    /**
     * Establecer el valor de cefechafin
     *
     */ 
    public function setCefechafin($cefechafin) {
        $this->cefechafin = $cefechafin;
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
    public function setear($idcompraestado, $objCompra, $objCompraEstadoTipo, $cefechaini, $cefechafin) {
        $this->setIdcompraestado($idcompraestado);
        $this->setObjCompra($objCompra);
        $this->setObjCompraEstadoTipo($objCompraEstadoTipo);
        $this->setCefechaini($cefechaini);
        $this->setCefechafin($cefechafin);
    }

    /**
     * Cargar un objeto desde la base de datos
     */
    public function cargar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "SELECT * FROM compraestado WHERE idcompraestado = " . $this->getIdcompraestado();
        if ($base->Iniciar()) {
            $res = $base->Ejecutar($sql);
            if ($res > -1) {
                if ($res > 0) {
                    $row = $base->Registro();

                    $objCompra = new Compra();
                    $objCompra->setIdcompra($row['idcompra']);
                    $objCompra->cargar();

                    $objCompraEstadoTipo = new CompraEstadoTipo();
                    $objCompraEstadoTipo->setIdcompraestadotipo($row['idcompraestadotipo']);
                    $objCompraEstadoTipo->cargar();

                    $this->setear($row['idcompraestado'], $objCompra, $objCompraEstadoTipo, $row['cefechaini'], $row['cefechafin']);
                    $resp = true;
                }
            }
        } else {
            $this->setMensajeoperacion("compraestado->cargar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Insertar un objeto en la base de datos
     */
    public function insertar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "INSERT INTO compraestado (idcompra, idcompraestadotipo, cefechaini, cefechafin) VALUES (" . $this->getObjCompra()->getIdcompra() . ", " . $this->getObjCompraEstadoTipo()->getIdcompraestadotipo() . ", '" . $this->getCefechaini() . "', '" . $this->getCefechafin() . "');";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraestado->insertar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraestado->insertar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Modificar un objeto en la base de datos
     */
    public function modificar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "UPDATE compraestado SET idcompra = " . $this->getObjCompra()->getIdcompra() . ", idcompraestadotipo = " . $this->getObjCompraEstadoTipo()->getIdcompraestadotipo() . ", cefechaini = '" . $this->getCefechaini() . "', cefechafin = '" . $this->getCefechafin() . "' WHERE idcompraestado = " . $this->getIdcompraestado() . ";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraestado->modificar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraestado->modificar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Eliminar un objeto de la base de datos
     */
    public function eliminar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "DELETE FROM compraestado WHERE idcompraestado = " . $this->getIdcompraestado() . ";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraestado->eliminar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraestado->eliminar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Listar objetos de la base de datos
     */
    public function listar($parametro = "") {
        $arreglo = array();
        $base = new BaseDatos();
        $sql = "SELECT * FROM compraestado ";
        if ($parametro != "") {
            $sql .= 'WHERE ' . $parametro;
        }
        $res = $base->Ejecutar($sql);
        if ($res > -1) {
            if ($res > 0) {
                while ($row = $base->Registro()) {
                    $objCompraEstado = new CompraEstado();

                    $objCompra = new Compra();
                    $objCompra->setIdcompra($row['idcompra']);
                    $objCompra->cargar();

                    $objCompraEstadoTipo = new CompraEstadoTipo();
                    $objCompraEstadoTipo->setIdcompraestadotipo($row['idcompraestadotipo']);
                    $objCompraEstadoTipo->cargar();

                    $objCompraEstado->setear($row['idcompraestado'], $objCompra, $objCompraEstadoTipo, $row['cefechaini'], $row['cefechafin']);
                    array_push($arreglo, $objCompraEstado);
                }
            }
        } else {
            $this->setMensajeoperacion("compraestado->listar: " . $base->getError());
        }

        return $arreglo;
    }

    /**
     * Tostring del objeto CompraEstado
     *
     * @return string
     */
    public function __toString() {
        return "ID CompraEstado: " . $this->getIdcompraestado() . 
               ", ID Compra: " . $this->getObjCompra()->getIdcompra() . 
               ", ID CompraEstadoTipo: " . $this->getObjCompraEstadoTipo()->getIdcompraestadotipo() . 
               ", Fecha Inicio: " . $this->getCefechaini() . 
               ", Fecha Fin: " . $this->getCefechafin();
    }
}
?>