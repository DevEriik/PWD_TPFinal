<?php

class CompraItem {

    private $idcompraitem;
    private $objProducto;
    private $objCompra;
    private $cicantidad;
    private $mensajeoperacion;

    public function __construct() {
        $this->idcompraitem = 0;
        $this->objProducto = new Producto();
        $this->objCompra = new Compra();
        $this->cicantidad = 0;
        $this->mensajeoperacion = '';
    }

    /**
     * Obtener el valor de idcompraitem
     */ 
    public function getIdcompraitem() {
        return $this->idcompraitem;
    }

    /**
     * Establecer el valor de idcompraitem
     *
     */ 
    public function setIdcompraitem($idcompraitem) {
        $this->idcompraitem = $idcompraitem;
    }

    /**
     * Obtener el valor de objProducto
     */ 
    public function getObjProducto() {
        return $this->objProducto;
    }

    /**
     * Establecer el valor de objProducto
     *
     */ 
    public function setObjProducto($objProducto) {
        $this->objProducto = $objProducto;
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
     * Obtener el valor de cicantidad
     */ 
    public function getCicantidad() {
        return $this->cicantidad;
    }

    /**
     * Establecer el valor de cicantidad
     *
     */ 
    public function setCicantidad($cicantidad) {
        $this->cicantidad = $cicantidad;
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
    public function setear($idcompraitem, $objProducto, $objCompra, $cicantidad) {
        $this->setIdcompraitem($idcompraitem);
        $this->setObjProducto($objProducto);
        $this->setObjCompra($objCompra);
        $this->setCicantidad($cicantidad);
    }

    /**
     * Cargar un objeto desde la base de datos
     */
    public function cargar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "SELECT * FROM compraitem WHERE idcompraitem = " . $this->getIdcompraitem();
        if ($base->Iniciar()) {
            $res = $base->Ejecutar($sql);
            if ($res > -1) {
                if ($res > 0) {
                    $row = $base->Registro();

                    $objProducto = new Producto();
                    $objProducto->setIdproducto($row['idproducto']);
                    $objProducto->cargar();

                    $objCompra = new Compra();
                    $objCompra->setIdcompra($row['idcompra']);
                    $objCompra->cargar();

                    $this->setear($row['idcompraitem'], $objProducto, $objCompra, $row['cicantidad']);
                    $resp = true;
                }
            }
        } else {
            $this->setMensajeoperacion("compraitem->cargar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Insertar un objeto en la base de datos
     */
    public function insertar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "INSERT INTO compraitem (idproducto, idcompra, cicantidad) VALUES (" . $this->getObjProducto()->getIdproducto() . ", " . $this->getObjCompra()->getIdcompra() . ", " . $this->getCicantidad() . ");";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraitem->insertar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraitem->insertar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Modificar un objeto en la base de datos
     */
    public function modificar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "UPDATE compraitem SET idproducto = " . $this->getObjProducto()->getIdproducto() . ", idcompra = " . $this->getObjCompra()->getIdcompra() . ", cicantidad = " . $this->getCicantidad() . " WHERE idcompraitem = " . $this->getIdcompraitem() . ";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraitem->modificar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraitem->modificar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Eliminar un objeto de la base de datos
     */
    public function eliminar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "DELETE FROM compraitem WHERE idcompraitem = " . $this->getIdcompraitem() . ";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("compraitem->eliminar: " . $base->getError());
            }
        } else {
            $this->setMensajeoperacion("compraitem->eliminar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * Listar objetos de la base de datos
     */
    public function listar($parametro = "") {
        $arreglo = array();
        $base = new BaseDatos();
        $sql = "SELECT * FROM compraitem ";
        if ($parametro != "") {
            $sql .= 'WHERE ' . $parametro;
        }
        $res = $base->Ejecutar($sql);
        if ($res > -1) {
            if ($res > 0) {
                while ($row = $base->Registro()) {
                    $objCompraItem = new CompraItem();

                    $objProducto = new Producto();
                    $objProducto->setIdproducto($row['idproducto']);
                    $objProducto->cargar();

                    $objCompra = new Compra();
                    $objCompra->setIdcompra($row['idcompra']);
                    $objCompra->cargar();

                    $objCompraItem->setear($row['idcompraitem'], $objProducto, $objCompra, $row['cicantidad']);
                    array_push($arreglo, $objCompraItem);
                }
            }
        } else {
            $this->setMensajeoperacion("compraitem->listar: " . $base->getError());
        }

        return $arreglo;
    }

    /**
     * toString del objeto CompraItem
     *
     * @return string
     */
    public function __toString() {
        return "ID CompraItem: " . $this->getIdcompraitem() . 
               ", ID Producto: " . $this->getObjProducto()->getIdproducto() . 
               ", ID Compra: " . $this->getObjCompra()->getIdcompra() . 
               ", Cantidad: " . $this->getCicantidad();
    }
}
?>