<?php

class ABMCompra {

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
     * @return Compra
     */
    private function cargarObjeto($param) {
        $obj = null;

        if (array_key_exists('idcompra', $param) && array_key_exists('cofecha', $param) && array_key_exists('idusuario', $param)) {
            $objUsuario = new Usuario();
            $objUsuario->setIdusuario($param['idusuario']);
            $objUsuario->cargar();

            $obj = new Compra();
            $obj->setear($param['idcompra'], $param['cofecha'], $objUsuario);
        }

        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Compra
     */
    private function cargarObjetoConClave($param) {
        $obj = null;

        if (isset($param['idcompra'])) {
            $obj = new Compra();
            $obj->setIdcompra($param['idcompra']);
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
        if (isset($param['idcompra']))
            $resp = true;
        return $resp;
    }

    /**
     * permite ingresar un objeto
     * @param array $param
     */
    public function alta($param) {
        $resp = false;
        $compra = new Compra();
        $usuario = new Usuario();
        $usuario->setIdusuario($param['idusuario']);
        $compra->setear($param['idcompra'], $param['cofecha'], $usuario);
        if ($compra->insertar()) {
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
            $objCompra = $this->cargarObjetoConClave($param);
            if ($objCompra != null and $objCompra->eliminar()) {
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
            $objCompra = $this->cargarObjeto($param);
            if ($objCompra != null and $objCompra->modificar()) {
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
            if (isset($param['idcompra'])) $where .= " and idcompra ='" . $param['idcompra'] . "'";
            if (isset($param['cofecha'])) $where .= " and cofecha ='" . $param['cofecha'] . "'";
            if (isset($param['idusuario'])) $where .= " and idusuario ='" . $param['idusuario'] . "'";
        }
        $compra = new Compra();
        $arreglo = $compra->listar($where);
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
     * permite buscar compras por idusuario
     * @param int $idusuario
     * @return array
     */
    public function buscarPorUsuario($idusuario) {
        $where = "idusuario = $idusuario";
        $compra = new Compra();
        $arreglo = $compra->listar($where);
        return $arreglo;
    }

    public function obtenerComprasPorEstado($idUsuario, $estadoTipo) {
        $ABMCompraEstado = new ABMCompraEstado();
        $comprasUsuario = $this->buscar(['idusuario' => $idUsuario]);
        $compras = [];

        foreach ($comprasUsuario as $compra) {
            $compraEstado = $ABMCompraEstado->buscar(['idcompra' => $compra->getIdcompra()]);
            if (count($compraEstado) > 0) {
                foreach ($compraEstado as $estado) {
                    if ($estado->getObjCompraEstadoTipo()->getIdcompraestadotipo() == $estadoTipo) {
                        if ($estadoTipo == 2 && $estado->getCefechafin() == '0000-00-00 00:00:00') {
                            $compras[] = $compra;
                        } elseif ($estadoTipo != 2) {
                            $compras[] = $compra;
                        }
                    }
                }
            }
        }

        return $compras;
    }

    /**
     * actualiza una compra y las tablas relacionadas
     * ¿hay alguun problema con instanciar otros ABM dentro de este?
     */
    public function actualizarCompra($idUsuario, $idProducto, $cantSeleccionada){
        $ABMCompraEstado = new ABMCompraEstado();
        $ABMCompraItem = new ABMCompraItem();
        $bandera = false;
        $fechaCompra = date('Y-m-d H:i:s');
        
        // 1. Buscamos si ya tiene carrito abierto (Manejamos si devuelve null o array vacío)
        $compraIniciada = $ABMCompraEstado->buscarCompraIniciadaPorUsuario($idUsuario);
        
        // Si NO hay compra iniciada (es null o array vacío)
        if (empty($compraIniciada)) {

            // A. Crear la Compra
            if ($this->alta(['idcompra' => null, 'cofecha' => $fechaCompra, 'idusuario' => $idUsuario])) {
                
                // B. Obtener el ID de forma SEGURA (La última compra de este usuario)
                $comprasDelUsuario = $this->buscar(['idusuario' => $idUsuario]);
                $ultimaCompra = end($comprasDelUsuario); // Obtiene el último elemento del array
                $idCompra = $ultimaCompra->getIdcompra();

                // C. Crear el Estado (Iniciada)
                // Nota: cefechafin va como null, nuestro arreglo anterior en CompraEstado lo manejará bien.
                if ($ABMCompraEstado->alta(['idcompraestado' => null, 'idcompra' => $idCompra, 'idcompraestadotipo' => 1, 'cefechaini' => $fechaCompra, 'cefechafin' => null])) {
                    
                    // D. Agregar el Ítem
                    if ($ABMCompraItem->alta(['idcompraitem' => null, 'idproducto' => $idProducto, 'idcompra' => $idCompra, 'cicantidad' => $cantSeleccionada])) {
                        $bandera = true;
                    }
                }
            }
        } else { 
            // Si YA tiene compra iniciada
            // Obtenemos el objeto compra (el primer elemento del array)
            $objCompraIniciada = $compraIniciada[0];
            $idCompraIniciada = $objCompraIniciada->getIdcompra();
            
            // Buscamos si ya tiene ese producto en el carrito
            $compraItemExistente = $ABMCompraItem->buscar(['idcompra' => $idCompraIniciada, 'idproducto' => $idProducto]);
            
            if (!empty($compraItemExistente)) {
                // Si existe, sumamos cantidad
                $item = $compraItemExistente[0];
                $nuevaCantidad = $item->getCicantidad() + $cantSeleccionada;
                
                $param = [
                    'idcompraitem' => $item->getIdcompraitem(),
                    'idproducto' => $idProducto,
                    'idcompra' => $idCompraIniciada,
                    'cicantidad' => $nuevaCantidad
                ];
                if ($ABMCompraItem->modificacion($param)) {
                    $bandera = true;
                }
            } else {
                // Si no existe, lo creamos
                if ($ABMCompraItem->alta(['idcompraitem' => null, 'idproducto' => $idProducto, 'idcompra' => $idCompraIniciada, 'cicantidad' => $cantSeleccionada])) {
                    $bandera = true;
                }
            }
        }
        return $bandera;
    }
    /**
     * Obtener el usuario asociado a una compra
     * @param int $idCompra
     * @return mixed|null
     */
    public function clienteAsociadoALaCompra($idCompra) {
        $cliente = null;
        $objCompra = $this->buscar(['idcompra' => $idCompra]);
        if (count($objCompra) > 0) {
            $objCompra = $objCompra[0];
            $cliente = $objCompra->getObjUsuario();
        }
        $datosCliente=[
            'name' => $cliente->getUsnombre(),
            'email' => $cliente->getUsmail()
        ];

        return  $datosCliente;
    }
    
}
?>