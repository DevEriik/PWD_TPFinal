<?php

class MenuRol{

    private $objMenu;
    private $objRol;
    private $mensajeoperacion;


    public function __construct()
    {
        $this->objMenu = new Menu();
        $this->objRol = new Rol();
        $this->mensajeoperacion = '';
    }

    


    /**
     * Get the value of objMenu
     */ 
    public function getObjMenu()
    {
        return $this->objMenu;
    }

    /**
     * Set the value of objMenu
     *
     */ 
    public function setObjMenu($objMenu)
    {
        $this->objMenu = $objMenu;

        
    }

    /**
     * Get the value of objRol
     */ 
    public function getObjRol()
    {
        return $this->objRol;
    }

    /**
     * Set the value of objRol
     *
     */ 
    public function setObjRol($objRol)
    {
        $this->objRol = $objRol;

        
    }

    /**
     * Get the value of mensajeoperacion
     */ 
    public function getMensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    /**
     * Set the value of mensajeoperacion
     *
     */ 
    public function setMensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;

        
    }

    

    public function setear($objMenu,$objRol){
        $this->setObjMenu($objMenu);
        $this->setObjRol($objRol);
    }

    public function setearConClave($idmenu,$idrol){
        $this->getObjMenu()->setIdmenu($idmenu);
        $this->getObjRol()->setIdrol($idrol);
    }

    public function cargar(){
        $resp = false;
        $base=new BaseDatos();
        // $objMenu = new Menu();
        // $objRol = new Rol();
        $sql="SELECT * FROM menurol WHERE idmenu = ".$this->getObjMenu()->getIdmenu()." AND idrol = ".$this->getObjRol()->getIdrol().";";
        if ($base->Iniciar()) {
            $res = $base->Ejecutar($sql);
            if($res>-1){
                if($res>0){
                    $row = $base->Registro();
                    
                    $objMenu = new Menu();
                    $objMenu->setIdmenu($row['idmenu']);
                    $objMenu->cargar();
                    $objRol = new Rol();
                    $objRol->setIdRol($row['idrol']);
                    $objRol->cargar();
                    $this->setear($objMenu->getIdmenu(), $objRol->getIdrol());
                }
            }
        } else {
            $this->setmensajeoperacion("menurol->listar: ".$base->getError());
        }
        return $resp;
     
    }

    public function insertar(){
        $resp = false;
        $base=new BaseDatos();
        // $objUsuario = new Menu();
        // $objRol = new Rol();
        $sql="INSERT INTO usuariorol(idmenu, idrol) VALUES(".$this->getObjMenu()->getIdmenu().", ".$this->getObjRol()->getIdrol().");";
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
                $this->setmensajeoperacion("menurol->insertar: ".$base->getError());
            }
        } else {
            $this->setmensajeoperacion("menurol->insertar: ".$base->getError());
        }
        return $resp;
    }

    public function eliminar(){
        $resp = false;
        $base=new BaseDatos();
        $sql="DELETE FROM menurol WHERE idmenu=".$this->getObjMenu()->getIdmenu() ." AND idrol=".$this->getObjRol()->getIdrol().";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion("menurol->eliminar: ".$base->getError());
            }
        } else {
            $this->setmensajeoperacion("menurol->eliminar: ".$base->getError());
        }
        return $resp;
    }

    public function listar($parametro =""){
        $arreglo = array();
        $base=new BaseDatos();
        $sql="SELECT * FROM menurol ";
        if ($parametro!="") {
            $sql.='WHERE '.$parametro;
        }
        $res = $base->Ejecutar($sql);
        if($res>-1){
            if($res>0){
                
                while ($row = $base->Registro()){
                    
                    $objMenuRol = new MenuRol();
                    $objMenu = new Menu();
                    $objRol = new Rol();

                    $objMenu->setIdmenu($row['idmenu']);
                    $objMenu->cargar();

                    $objRol->setIdrol($row['idrol']);
                    $objRol->cargar();

                    $objMenuRol->setear($objMenu, $objRol);
                    array_push($arreglo, $objMenuRol);

                }
               
            }
            
        } else {
            $this->setmensajeoperacion("menurol->listar: ".$base->getError());
        }
 
        return $arreglo;
    }

    public function modificar() {
        $resp = false;
        $base = new BaseDatos();
        $sql = "UPDATE menurol SET idmenu = " . $this->getObjMenu()->getIdmenu() . ", idrol = " . $this->getObjRol()->getIdrol() . " WHERE idmenu = " . $this->getObjMenu()->getIdmenu() . " AND idrol = " . $this->getObjRol()->getIdrol() . ";";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion("menurol->modificar: " . $base->getError());
            }
        } else {
            $this->setmensajeoperacion("menurol->modificar: " . $base->getError());
        }
        return $resp;
    }

    /**
     * to string del objeto MenuRol
     *
     * @return string
     */
    public function __toString() {
        return "Menu: " . $this->getObjMenu()->getMenombre() . 
               ", Rol: " . $this->getObjRol()->getRodescripcion();
    }

}
?>