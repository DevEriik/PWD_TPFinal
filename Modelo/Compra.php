<?php

class Compra{

    private $idcompra;
    private $cofecha;
    private $objUsuario;
    private $mensajeoperacion;

    public function __construct(){
        $this->idcompra = 0;
        $this->cofecha = null;
        $this->objUsuario = new Usuario();
        $this->mensajeoperacion = '';
    }

    /**
     * obtenemos el valor de idcompra
     */ 
    public function getIdcompra()
    {
        return $this->idcompra;
    }

    /**
     * enviamos el valor de idcompra
     *
     */ 
    public function setIdcompra($idcompra)
    {
        $this->idcompra = $idcompra;

         
    }

    /**
     * obtenemos el valor de cofecha
     */ 
    public function getCofecha()
    {
        return $this->cofecha;
    }

    /**
     * enviarmos el valor de cofecha
     *
     * @return  self
     */ 
    public function setCofecha($cofecha)
    {
        $this->cofecha = $cofecha;

         
    }

    /**
     * obtenemos el valor de objUsuario
     */ 
    public function getObjUsuario()
    {
        return $this->objUsuario;
    }

    /**
     * enviarmos el valor de objUsuario
     *
     * @return  self
     */ 
    public function setObjUsuario($objUsuario)
    {
        $this->objUsuario = $objUsuario;

         
    }

    /**
     * obtenemos el valor de mensajeoperacion
     */ 
    public function getMensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    /**
     * enviarmos el valor de mensajeoperacion
     *
     * @return  self
     */ 
    public function setMensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;

         
    }


    public function setear($idcompra,$cofecha,$objUsuario){
        $this->setIdcompra($idcompra);
        $this->setCofecha($cofecha);
        $this->setObjUsuario($objUsuario);
    }

    public function cargar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "SELECT * FROM compra WHERE idcompra = " . $this->getIdcompra();
        if ($base->Iniciar()) {
            $res = $base->Ejecutar($sql);
            if ($res > -1) {
                if ($res > 0) {
                    $row = $base->Registro();
                    $objUsuario = new Usuario();
                    $objUsuario->setIdusuario($row['idusuario']);
                    $objUsuario->cargar();
                    $this->setear($row['idcompra'], $row['cofecha'], $objUsuario);
                }
            }
        } else {
            $this->setmensajeoperacion("compra->cargar: " . $base->getError()[2]);
        }
        return $resp;
    }

    public function insertar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "INSERT INTO compra(cofecha, idusuario) VALUES('" . $this->getCofecha() . "', " . $this->getObjUsuario()->getIdusuario() . ");";
        //echo "SQL: $sql"; // Mostrar la consulta SQL
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion("compra->insertar: " . $base->getError());
            }
        } else {
            $this->setmensajeoperacion("compra->insertar: " . $base->getError());
        }
        return $resp;
    }

    public function modificar(){
        $resp = false;
        $base=new BaseDatos();
        $sql="UPDATE compra SET cofecha ='".$this->getCofecha()."', idusuario= ".$this->getObjUsuario()->getIdusuario()." WHERE idcompra= ".$this->getIdcompra().";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion("compra->modificar: ".$base->getError());
            }
        } else {
            $this->setmensajeoperacion("compra->modificar: ".$base->getError());
        }
        return $resp;
    }

    public function eliminar(){
        $resp = false;
        $base=new BaseDatos();
        $sql="DELETE FROM compra WHERE idcompra =".$this->getIdcompra();
       // echo $sql;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion("compra->eliminar: ".$base->getError());
            }
        } else {
            $this->setmensajeoperacion("compra->eliminar: ".$base->getError());
        }
        return $resp;
    }

    public function listar($parametro=""){
        $arreglo = array();
        $base=new BaseDatos();
        $sql="SELECT * FROM compra ";
        if ($parametro!="") {
            $sql.='WHERE '.$parametro;
        }
        if($base->iniciar()){
            $res = $base->Ejecutar($sql);
            if($res>-1){
                if($res>0){
                    while ($row = $base->Registro()){
                        $objCompra = new Compra();
                        $objUsuario = new Usuario();
                        $objUsuario->setIdusuario($row['idusuario']);
                        $objUsuario->cargar();
                        $objCompra->setear($row['idcompra'], $row['cofecha'], $objUsuario);
                        array_push($arreglo, $objCompra);
                    }
                }
            } else {
                $this->setmensajeoperacion("Compra->listar: ".$base->getError());
            }
        }
        return $arreglo;
    }

    /**
     * toString del objeto Compra
     *
     * @return string
     */
    public function __toString() {
        return "ID Compra: " . $this->getIdcompra() . 
               ", Fecha: " . $this->getCofecha() . 
               ", Usuario: " . $this->getObjUsuario()->getUsnombre();
    }

}




?>