<?php

class ABMCompraItem {

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
     * @return CompraItem
     */
    private function cargarObjeto($param) {
        $obj = null;

        if (array_key_exists('idcompraitem', $param) && array_key_exists('idproducto', $param) && array_key_exists('idcompra', $param) && array_key_exists('cicantidad', $param)) {
            $objProducto = new Producto();
            $objProducto->setIdproducto($param['idproducto']);
            $objProducto->cargar();

            $objCompra = new Compra();
            $objCompra->setIdcompra($param['idcompra']);
            $objCompra->cargar();

            $obj = new CompraItem();
            $obj->setear($param['idcompraitem'], $objProducto, $objCompra, $param['cicantidad']);
        }

        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return CompraItem
     */
    private function cargarObjetoConClave($param) {
        $obj = null;

        if (isset($param['idcompraitem'])) {
            $obj = new CompraItem();
            $obj->setIdcompraitem($param['idcompraitem']);
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
        if (isset($param['idcompraitem']))
            $resp = true;
        return $resp;
    }

    /**
     * permite ingresar un objeto
     * @param array $param
     */
    public function alta($param) {
        $resp = false;
        $objCompraItem = $this->cargarObjeto($param);
        if ($objCompraItem != null and $objCompraItem->insertar()) {
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
            $objCompraItem = $this->cargarObjetoConClave($param);
            if ($objCompraItem != null and $objCompraItem->eliminar()) {
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
            $objCompraItem = $this->cargarObjeto($param);
            if ($objCompraItem != null and $objCompraItem->modificar()) {
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
            if (isset($param['idcompraitem'])) $where .= " and idcompraitem ='" . $param['idcompraitem'] . "'";
            if (isset($param['idproducto'])) $where .= " and idproducto ='" . $param['idproducto'] . "'";
            if (isset($param['idcompra'])) $where .= " and idcompra ='" . $param['idcompra'] . "'";
            if (isset($param['cicantidad'])) $where .= " and cicantidad ='" . $param['cicantidad'] . "'";
        }
        $compraItem = new CompraItem();
        $arreglo = $compraItem->listar($where);
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
     * recibe el id de un producto y retorna su ULTIMO estado
     * @param int
     * @return null|int
     */
    public function estadoCompraItem($IDprod){

        $compraEstadoTipo = null;
        $ABMCompraEstado = new ABMCompraEstado;
        // primero busco el compraItem vinculado a ese idProducto
        $compraItemBuscado = $this->buscar(['idproducto' => $IDprod]);
        // si se encontraron productos:
        if(count($compraItemBuscado) > 0){
            // echo "<br>--entra--<br>";
            // busco un compraestado con el idcompra en cuestión
            $ultimoCompraEstado = end($ABMCompraEstado->buscar(['idcompra' => $compraItemBuscado[0]->getObjCompra()->getIdcompra()]));
            if(count($ultimoCompraEstado) > 0){
                // verifico con el compraestado el tipo de estado 
                $compraEstadoTipo = $ultimoCompraEstado->getObjCompraEstadoTipo()->getIdcompraestadotipo();
            }
        }
    
        return $compraEstadoTipo;
    }

    /**
     * Obtiene los productos del carrito para un usuario específico
     * @param int $idUsuario
     * @return array
     */
    public function obtenerProductosCarrito($idUsuario) {
        $ABMCompraEstado = new ABMCompraEstado();

        // Buscar carritos iniciados por el usuario y sin finalizar
        $carritosIniciados = $ABMCompraEstado->buscarCompraIniciadaPorUsuario($idUsuario);

        $productosCarrito = [];
        $totalCarrito = 0;

        // Si se encontraron carritos iniciados
        if ($carritosIniciados !== null) {
            // Recorrer los carritos iniciados
            foreach ($carritosIniciados as $compraIni) {

                // del compraitem obtengo la cantidad de elementos comprados que serian
                // los elementos del carrito que estan sin finalizar
                $compraItems = $this->buscar(['idcompra' => $compraIni->getIdcompra()]);

                foreach ($compraItems as $compraItem) {
                    if (null !== $compraItem->getObjProducto()) {
                        $precioTotalProducto = $compraItem->getObjProducto()->getPrecioprod() * $compraItem->getCicantidad();
                        $productoCarrito = [
                            'Nombre' => $compraItem->getObjProducto()->getPronombre(),
                            'Detalle' => $compraItem->getObjProducto()->getProdetalle(),
                            'Precio' => $precioTotalProducto,
                            'Cantidad' => $compraItem->getCicantidad()
                        ];

                        // Verificar si el producto ya esta en el carrito
                        //agregado por que hay veces que se repiten los productos en el carrito
                        //para que se sume y no cree otro compraitem
                        $productoExistente = false;
                        foreach ($productosCarrito as &$prodCarrito) {
                            if ($prodCarrito['Nombre'] === $productoCarrito['Nombre']) {
                                $prodCarrito['Cantidad'] += $productoCarrito['Cantidad'];
                                $prodCarrito['Precio'] += $precioTotalProducto;
                                $productoExistente = true;
                                //break;
                            }
                        }

                        if (!$productoExistente) {
                            $productosCarrito[] = $productoCarrito;
                        }

                        $totalCarrito += $precioTotalProducto;
                    } else {
                        //echo "<br>No se encontraron productos para la compra con ID: " . $compraIni->getIdcompra() . "<br>";
                    }
                }
            }
        }

        $resultado = ['productosCarrito' => $productosCarrito, 'totalCarrito' => $totalCarrito];
        return $resultado;
    }
    /**
     * Verificar el estado de un producto
     * @param int $idProducto
     * @param int $idUsuario
     * @return string|null
     */
    public function verificarEstadoProducto($idProducto, $idUsuario) {
        $estado = null;
        // Verificar que existan compraitem con ese idproducto
        $compraItem = $this->buscarArray(['idproducto' => $idProducto]);
        if (isset($compraItem[0])) {
            $compraItem = $compraItem[0];

            // Obtener el idcompra del compraitem
            $idCompra = $compraItem['objCompra']->getIdcompra();

            $ABMcompra = new ABMCompra();
            // Verificar que la compra pertenezca al usuario actual
            if (count($ABMcompra->buscar(['idcompra' => $idCompra, 'idusuario' => $idUsuario])) > 0) {
                // Obtener el estado del compraitem
                $estado = $this->estadoCompraItem($idProducto);
            } 
        } 

        return $estado;
    }


}
?>