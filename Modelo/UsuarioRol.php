<?php

class UsuarioRol{

    private $objUsuario;
    private $objRol;
    private $mensajeoperacion;

    public function __construct() {
        $this->objUsuario = new Usuario();
        $this->objRol = new Rol();
        $this->mensajeoperacion='';
    } 


    /**
     * obtener el valor de objUsuario
     */ 
    public function getObjUsuario()
    {
        return $this->objUsuario;
    }

    /**
     * enviar el valor de objUsuario
     *
     */ 
    public function setObjUsuario($objUsuario)
    {
        $this->objUsuario = $objUsuario;

        
    }

    /**
     * obtener el valor de objRol
     */ 
    public function getObjRol()
    {
        return $this->objRol;
    }

    /**
     * enviar el valor de objRol
     *
     */ 
    public function setObjRol($objRol)
    {
        $this->objRol = $objRol;

        
    }

    /**
     * obtener el valor de mensajeoperacion
     */ 
    public function getMensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    /**
     * enviar el valor de mensajeoperacion
     *
     */ 
    public function setMensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;

        
    }

   

    public function setear($objUsuario, $objRol){
        $this->setObjRol($objRol);
        $this->setObjUsuario($objUsuario);
    }

    public function setearConClave($idusuario, $idjrol)
    {
        $this->getObjRol()->setIdRol($idjrol);
        $this->getObjUsuario()->setIdUsuario($idusuario);
    }

    public function cargar(){
        $resp = false;
        $base=new BaseDatos();
        // $objUsuario = new Usuario();
        // $objRol = new Rol();
        $sql="SELECT * FROM usuariorol WHERE idusuario = ".$this->getObjUsuario()->getIdusuario()." AND idrol = ".$this->getObjRol()->getIdrol().";";
        if ($base->Iniciar()) {
            $res = $base->Ejecutar($sql);
            if($res>-1){
                if($res>0){
                    $row = $base->Registro();
                    
                    $objUsuario = new Usuario();
                    $objUsuario->setIdUsuario($row['idusuario']);
                    $objUsuario->cargar();
                    $objRol = new Rol();
                    $objRol->setIdRol($row['idrol']);
                    $objRol->cargar();
                    $this->setear($objUsuario->getIdusuario(), $objRol->getIdrol());
                }
            }
        } else {
            $this->setmensajeoperacion("usuariorol->listar: ".$base->getError());
        }
        return $resp;
     
    }

    public function insertar(){
        $resp = false;
        $base=new BaseDatos();
        // $objUsuario = new Usuario();
        // $objRol = new Rol();
        $sql="INSERT INTO usuariorol(idusuario, idrol) VALUES(".$this->getObjUsuario()->getIdusuario().", ".$this->getObjRol()->getIdrol().");";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                /* if($id = $base->lastInsertId($sql)){
                    $this->getobjUsuario()->setIdusuario($id);
                    $this->getobjRol()->setIdrol($id);
                }else{
                    echo "no se encontró el id";
                } */
                $resp = true;
            } else {
                $this->setmensajeoperacion("usuariorol->insertar: ".$base->getError());
            }
        } else {
            $this->setmensajeoperacion("usuariorol->insertar: ".$base->getError());
        }
        return $resp;
    }

    
    public function modificar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "UPDATE usuariorol SET idrol = " . $this->getObjRol()->getIdrol() . " WHERE idusuario = " . $this->getObjUsuario()->getIdusuario() . ";";
        //echo "Consulta SQL: $sql<br>";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeoperacion("usuariorol->modificar: " . $base->getError());
                //echo "Error en la ejecución de la consulta: " . $base->getError() . "<br>";
            }
        } else {
            $this->setMensajeoperacion("usuariorol->modificar: " . $base->getError());
            //echo "Error al iniciar la base de datos: " . $base->getError() . "<br>";
        }
        return $resp;
    }

    public function eliminar(){
        $resp = false;
        $base=new BaseDatos();
        $sql="DELETE FROM usuariorol WHERE idusuario=".$this->getObjUsuario()->getIdusuario() ." AND idrol=".$this->getObjRol()->getIdrol().";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion("usuariorol->eliminar: ".$base->getError());
            }
        } else {
            $this->setmensajeoperacion("usuariorol->eliminar: ".$base->getError());
        }
        return $resp;
    }

    public function listar($parametro =""){
        $arreglo = array();
        $base=new BaseDatos();
        $sql="SELECT * FROM usuariorol ";
        if ($parametro!="") {
            $sql.='WHERE '.$parametro;
        }
        $res = $base->Ejecutar($sql);
        if($res>-1){
            if($res>0){
                
                while ($row = $base->Registro()){
                    
                    $objUsuarioRol = new UsuarioRol();
                    $objUsuario = new Usuario();
                    $objRol = new Rol();

                    $objUsuario->setIdusuario($row['idusuario']);
                    $objUsuario->cargar();

                    $objRol->setIdrol($row['idrol']);
                    $objRol->cargar();

                    $objUsuarioRol->setear($objUsuario, $objRol);
                    array_push($arreglo, $objUsuarioRol);

                }
               
            }
            
        } else {
            $this->setmensajeoperacion("usuariorol->listar: ".$base->getError());
        }
 
        return $arreglo;
    }

    /**
     * toString del objeto UsuarioRol
     *
     * @return string
     */
    public function __toString() {
        return "Usuario: " . $this->getObjUsuario()->getUsnombre() . 
               ", Rol: " . $this->getObjRol()->getRodescripcion();
    }
    

}

?>