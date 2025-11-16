<?php

class ABMProducto {

    /**
     * Función utilizada en caso que esperemos un arreglo de un solo elemento.
     * Llama a Buscar, convierte el obj del indice 0 a arreglo y lo retorna.
     * Si retorna un arreglo vacío, devuelve null.
     * @return array|null
     */
    public function arrayOnull($arrAsoc) {
        $objetoOnull = $this->buscar($arrAsoc);
        $element = null;

        if (count($objetoOnull) === 1) {
            $element = dismount($objetoOnull[0]);
        }

        return $element;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $datos
     * @return bool
     */
    public function abm($datos) {
        $resp = false;
        if ($datos['accion'] == 'editar') {
            if ($this->modificacion($datos)) {
                $resp = true;
            }
        }
        if ($datos['accion'] == 'borrar') {
            if ($this->baja($datos)) {
                $resp = true;
            }
        }
        if ($datos['accion'] == 'nuevo') {
            if ($this->alta($datos)) {
                $resp = true;
            }
        }
        return $resp;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return Producto
     */
    private function cargarObjeto($param) {
        $obj = null;

        if (array_key_exists('idproducto', $param) && array_key_exists('pronombre', $param) && array_key_exists('prodetalle', $param) && array_key_exists('precioprod', $param) && array_key_exists('procantstock', $param)) {
            $obj = new Producto();
            $obj->setear($param['idproducto'], $param['pronombre'], $param['prodetalle'], $param['precioprod'], $param['procantstock']);
        }

        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Producto
     */
    private function cargarObjetoConClave($param) {
        $obj = null;

        if (isset($param['idproducto'])) {
            $obj = new Producto();
            $obj->setIdproducto($param['idproducto']);
        }
        return $obj;
    }

    /**
     * Corrobora que dentro del arreglo asociativo estan seteados los campos claves
     * @param array $param
     * @return boolean
     */
    private function seteadosCamposClaves($param) {
        $resp = false;
        if (isset($param['idproducto']))
            $resp = true;
        return $resp;
    }

    /**
     * permite ingresar un objeto
     * @param array $param
     */
    public function alta($param) {
        $resp = false;
        $objProducto = $this->cargarObjeto($param);
        if ($objProducto != null and $objProducto->insertar()) {
            $resp = true;
        }
        return $resp;
    }

    /**
     * permite eliminar un objeto 
     * @param array $param
     * @return boolean
     */
    public function baja($param) {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objProducto = $this->cargarObjetoConClave($param);
            if ($objProducto != null and $objProducto->eliminar()) {
                $resp = true;
            }
        }

        return $resp;
    }

    /**
     * permite modificar un objeto
     * @param array $param
     * @return boolean
     */
    public function modificacion($param) {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objProducto = $this->cargarObjeto($param);
            if ($objProducto != null and $objProducto->modificar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    /**
     * permite buscar un objeto
     * @param array $param
     * @return array
     */
    public function buscar($param) {
        $where = " true ";
        if ($param <> NULL) {
            if (isset($param['idproducto'])) $where .= " and idproducto ='" . $param['idproducto'] . "'";
            if (isset($param['pronombre'])) $where .= " and pronombre ='" . $param['pronombre'] . "'";
            if (isset($param['prodetalle'])) $where .= " and prodetalle ='" . $param['prodetalle'] . "'";
            if (isset($param['precioprod'])) $where .= " and precioprod ='" . $param['precioprod'] . "'";
            if (isset($param['procantstock'])) $where .= " and procantstock ='" . $param['procantstock'] . "'";
        }
        $producto = new Producto();
        $arreglo = $producto->listar($where);
        return $arreglo;
    }

    /**
     * Si recibe como parámetro un arreglo asociativo clave-valor, llama al Buscar  
     * y retorna un arreglo con arreglos asociativos. 
     * Si recibe como parámetro un objeto, convierte sus propiedades en un arreglo asociativo.
     * @param array|object $param
     * @return array  
     */
    public function buscarArray($param) {
        $arreglo = [];
        if (is_object($param)) {
            $arreglo = dismount($param);
        } else {
            $arreglo = convert_array($this->buscar($param));
        }
        return $arreglo;
    }

    /**
     * retorna el número de cierto producto que se encuentran dentro de los carritos.
     * Para evitar que el depósito actualice el stock a un número inferior que la cantidad de productos reservados. 
     */
    public function productosReservados($idproducto){
        // $productoBuscado = $this->buscarArray(['idproducto' => $idproducto]);

        // variable para almacenar la cantidad de productos reservados según el id
        $cantProds = 0;

        $ABMCompraitem = new ABMCompraItem;
        $ABMcompraestado = new ABMCompraEstado;

        $colCompraItems = $ABMCompraitem->buscarArray(null);
        $colCompraEstados = $ABMcompraestado->buscarArray(null);

        foreach($colCompraItems as $compraitem){

            foreach($colCompraEstados as $compraestado){

                // si los idcompra coinciden Y el compraestado es igual a 1 Y la fechafin es la que está por defecto
                if(($compraestado['objCompraEstadoTipo']->getIdcompraestadotipo() === 1) && ($compraestado['cefechafin'] === '0000-00-00 00:00:00')){

                    if($compraestado['objCompra']->getIdcompra() === $compraitem['objCompra']->getIdcompra()){
                        if($compraitem['objProducto']->getIdproducto() === $idproducto){
                            $cantProds += $compraitem['cicantidad'];
                        }
                    }

                }
            }
        }
        return $cantProds;
    }
    /**
     * Actualizar el stock de un producto
     * @param array $datos
     * @return array
     */
    public function actualizarStock($datos) {
        
        $stockActualizado = false;
        $productoBuscado = $this->buscarArray(['idproducto' => $datos['idproducto']]);

        if ($datos['nuevoStock'] > 0) {
            if (count($productoBuscado) > 0) {
                $param = [
                    'idproducto' => $productoBuscado[0]['idproducto'],
                    'pronombre' => $productoBuscado[0]['pronombre'],
                    'prodetalle' => $productoBuscado[0]['prodetalle'],
                    'precioprod' => $productoBuscado[0]['precioprod'],
                    'procantstock' => $datos['nuevoStock']
                ];
                if ($this->modificacion($param)) {
                    $stockActualizado = true;
                }
            } 
        } 

        return $stockActualizado;
    }

}
?>