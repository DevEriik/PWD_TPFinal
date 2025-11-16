<?php

class ABMCompraEstado {

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
     * @return CompraEstado
     */
    private function cargarObjeto($param) {
        $obj = null;

        if (array_key_exists('idcompraestado', $param) && array_key_exists('idcompra', $param) && array_key_exists('idcompraestadotipo', $param) && array_key_exists('cefechaini', $param) && array_key_exists('cefechafin', $param)) {
            $objCompra = new Compra();
            $objCompra->setIdcompra($param['idcompra']);
            $objCompra->cargar();

            $objCompraEstadoTipo = new CompraEstadoTipo();
            $objCompraEstadoTipo->setIdcompraestadotipo($param['idcompraestadotipo']);
            $objCompraEstadoTipo->cargar();

            $obj = new CompraEstado();
            $obj->setear($param['idcompraestado'], $objCompra, $objCompraEstadoTipo, $param['cefechaini'], $param['cefechafin']);
        }

        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return CompraEstado
     */
    private function cargarObjetoConClave($param) {
        $obj = null;

        if (isset($param['idcompraestado'])) {
            $obj = new CompraEstado();
            $obj->setIdcompraestado($param['idcompraestado']);
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
        if (isset($param['idcompraestado']))
            $resp = true;
        return $resp;
    }

    /**
     * permite ingresar un objeto
     * @param array $param
     */
    public function alta($param) {
        $resp = false;
        $objCompraEstado = $this->cargarObjeto($param);
        if ($objCompraEstado != null and $objCompraEstado->insertar()) {
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
            $objCompraEstado = $this->cargarObjetoConClave($param);
            if ($objCompraEstado != null and $objCompraEstado->eliminar()) {
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
            $objCompraEstado = $this->cargarObjeto($param);
            if ($objCompraEstado != null and $objCompraEstado->modificar()) {
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
            if (isset($param['idcompraestado'])) $where .= " and idcompraestado ='" . $param['idcompraestado'] . "'";
            if (isset($param['idcompra'])) $where .= " and idcompra ='" . $param['idcompra'] . "'";
            if (isset($param['idcompraestadotipo'])) $where .= " and idcompraestadotipo ='" . $param['idcompraestadotipo'] . "'";
            if (isset($param['cefechaini'])) $where .= " and cefechaini ='" . $param['cefechaini'] . "'";
            if (isset($param['cefechafin'])) $where .= " and cefechafin ='" . $param['cefechafin'] . "'";
        }
        $compraEstado = new CompraEstado();
        $arreglo = $compraEstado->listar($where);
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
     * permite buscar compras por idusuario y trae las compras con estado Iniciado y fecha fin null
     * @param int $idusuario
     * @return array|null
     */
    public function buscarCompraIniciadaPorUsuario($idusuario) {
        $abmCompra = new ABMCompra();
        //busca todas las compras por el id de usario
        $compras = $abmCompra->buscarPorUsuario($idusuario);

        // arreglo para almacenar las compras con estado Iniciado
        $compraEstadoIniciado = [];

        foreach ($compras as $compra) {
            if (count($compras) > 0) {
                // de cada compra específica, obtengo su compraEstado específico
                $compraEstado = $this->buscarArray(['idcompra' => $compra->getIdcompra()]);
                if (count($compraEstado) > 0) {

                    // si el 'idcompraestadotipo' de este compraEstado es 1, significa que la compra fue iniciada. Por lo que la almacenamos
                    if($compraEstado[0]['objCompraEstadoTipo']->getIdcompraestadotipo() === 1 &&  $compraEstado[0]['cefechafin'] === '0000-00-00 00:00:00'){
                        $compraEstadoIniciado[] = $compra; 
                    }
                }
            }
        } 

        if(count($compraEstadoIniciado) === 0){
            $compraEstadoIniciado = null;
        }

        return $compraEstadoIniciado;
    }

    /**
     * permite buscar las compras con un idusuario, compraestado y fecha fin especificados 
     * ($fechafin === true === null === '0000-00-00 00:00:00' ).
     * @return array|null 
    */
    public function estadoCompraUsuario($idusuario,$estado,$fechafin){
        $abmCompra = new ABMCompra;
        //busca todas las compras por el id de usario
            // usa un listar, por lo que retorna una coleccion de objetos compra
        $comprasUsuario = $abmCompra->buscarPorUsuario($idusuario);
        // variable (null) que, en caso de encontrar registros, será un arreglo para almacenar las compras con el estado y fecha especificados.
        $comprasEspecificadas = null;
        
        if(count($comprasUsuario) > 0){
            foreach($comprasUsuario as $compraUser){
                // si $fechafin es true, filtro las búsquedas también por el fechafin por defecto (000:000:000) 
                if($fechafin === true){
                    $compraEstado = $this->buscarArray(['idcompraestadotipo' => $estado,'idcompra' => $compraUser->getIdcompra(),'cefechafin' => '0000-00-00 00:00:00']);
                }else{
                    $compraEstado = $this->buscarArray(['idcompraestadotipo' => $estado,'idcompra' => $compraUser->getIdcompra()]);
                }
                if(count($compraEstado) > 0){
                    $comprasEspecificadas[] = $compraUser;
                }
            }
        }
        return $comprasEspecificadas;
    }


    /**
     * permite buscar compras confirmadas sin finalizar
     * @return array
     */
    public function buscarComprasConfirmadasSinFinalizar() {
        $abmCompra = new ABMCompra();
        $compras = $abmCompra->buscar(null); // Buscar todas las compras

        // arreglo para almacenar las compras confirmadas sin finalizar
        $comprasConfirmadasSinFinalizar = [];

        foreach ($compras as $compra) {
            if (count($compras) > 0) {
                // de cada compra específica, obtengo su compraEstado específico
                $compraEstado = $this->buscarArray(['idcompra' => $compra->getIdcompra()]);
                if (count($compraEstado) > 0) {
                    foreach ($compraEstado as $estado) {
                        // si el 'idcompraestadotipo' de este compraEstado es 2 y 'cefechafin' es '0000-00-00 00:00:00', significa que la compra fue confirmada pero no finalizada. Por lo que la almacenamos
                        if ($estado['objCompraEstadoTipo']->getIdcompraestadotipo() === 2 && $estado['cefechafin'] === '0000-00-00 00:00:00') {
                            $comprasConfirmadasSinFinalizar[] = $compra;
                           // break; // Salir del bucle una vez que encontramos un estado que cumple con las condiciones
                        }
                    }
                }
            }
        }

        return $comprasConfirmadasSinFinalizar;
    }


    /**
     * retorna la cantidad de ventas
    */
    public function ventas(){

        $ABMCompraitem = new ABMCompraItem;
        $ABMProducto = new ABMProducto;
        $arrVentas = [];

        foreach($this->buscarArray(null) as $arrCompraEstado){
            // si el estado es enviado:
            if($arrCompraEstado['objCompraEstadoTipo']->getIdcompraestadotipo() === 3){
                $fechaEnviado = $arrCompraEstado['cefechaini'];

                // recorro los compraitem para poder extraer la cantidad vendida
                foreach($ABMCompraitem->buscarArray(['idcompra' => $arrCompraEstado['objCompra']->getIdcompra()]) as $arrCompraitem){

                    foreach($ABMProducto->buscarArray(null) as $arrProducto){
                        // si el compraitem coincide con el id del producto recorrido:
                        if($arrCompraitem['objProducto']->getIdproducto() == $arrProducto['idproducto']){
                            $precioXcantidad = intval($arrCompraitem['cicantidad']) * intval($arrProducto['precioprod']);
                            
                            // Si la fecha ya existe en el arreglo, sumamos el monto; si no, la inicializamos
                            if (isset($arrVentas[$fechaEnviado])) {
                                $arrVentas[$fechaEnviado] += $precioXcantidad;
                            } else {
                                $arrVentas[$fechaEnviado] = $precioXcantidad;
                            }
                        }
                    }

                }
            }
        }
        return $arrVentas;
    }
    /**
 * permite buscar la compra con estado Iniciado y fecha fin null por idusuario
 * @param int $idusuario
 * @return mixed|null
 */
public function buscarCompraIniciada($idusuario) {
    $abmCompra = new ABMCompra();
    // busca todas las compras por el id de usuario
    $compras = $abmCompra->buscarPorUsuario($idusuario);

    foreach ($compras as $compra) {
        // de cada compra específica, obtengo su compraEstado específico
        $compraEstado = $this->buscarArray(['idcompra' => $compra->getIdcompra()]);
        if (count($compraEstado) > 0) {
            // si el 'idcompraestadotipo' de este compraEstado es 1, significa que la compra fue iniciada. Por lo que la retornamos
            if ($compraEstado[0]['objCompraEstadoTipo']->getIdcompraestadotipo() === 1 && $compraEstado[0]['cefechafin'] === '0000-00-00 00:00:00') {
                return $compra;
            }
        }
    }

    return null;
}

/**
     * Confirmar una compra actualizando el estado y creando un nuevo estado
     * @param int $idCompra
     * @param string $fechaFin
     * @return array
     */
    public function confirmarCompra($idCompra, $fechaFin) {
        
        $compraConfirmada=false;
        // Buscar el estado de la compra con idcompraestadotipo = 1
        $compraEstado = $this->buscar(['idcompra' => $idCompra, 'idcompraestadotipo' => 1]);

        if (count($compraEstado) > 0) {
            $compraEstado = $compraEstado[0];

            $compraEstadoModificado = [
                'idcompraestado' => $compraEstado->getIdcompraestado(),
                'idcompra' => $idCompra,
                'idcompraestadotipo' => $compraEstado->getObjCompraEstadoTipo()->getIdcompraestadotipo(),
                'cefechaini' => $compraEstado->getCefechaini(),
                'cefechafin' => $fechaFin
            ];

            if ($this->modificacion($compraEstadoModificado)) {
                // Insertar una nueva entrada en la tabla compraestado con idcompraestadotipo = 2
                $paramCompraEstado = [
                    'idcompraestado' => null,
                    'idcompra' => $idCompra,
                    'idcompraestadotipo' => 2, // Estado "confirmada"
                    'cefechaini' => $fechaFin,
                    'cefechafin' => null
                ];

                if ($this->alta($paramCompraEstado)) {
                    $compraConfirmada=true;
                } 
            } 
        } 
        return $compraConfirmada;
    }

    /**
     * Enviar una compra actualizando el estado y modificando el stock de los productos
     * @param int $idCompra
     * @param string $fechaFin
     * @return array
     */
    public function enviarCompra($idCompra, $fechaFin) {
        
        $compraEnviada=false;

        $ABMcompraitem = new ABMCompraItem;
        $ABMproducto = new ABMProducto;
        $ABMcompraEstado = new ABMCompraEstado;

        // Colección de compraitems relacionados con ese idcompra
        $colCompraItems = $ABMcompraitem->buscarArray(['idcompra' => $idCompra]);

        // Buscamos el compraEstado relacionado a ese idcompra y con un idcompraestadotipo = 2
        $compraEstado = $ABMcompraEstado->buscarArray(['idcompra' => $idCompra, 'idcompraestadotipo' => 2])[0];

        foreach ($colCompraItems as $compraitem) {
            // Almaceno la cantidad a descontar del producto
            $cantDescontada = $compraitem['cicantidad'];
            $stockActualizado = $compraitem['objProducto']->getProcantstock() - $cantDescontada;

            // Modifico la cantidad descontada del producto
            $param = [
                'idproducto' => $compraitem['objProducto']->getIdproducto(),
                'pronombre' => $compraitem['objProducto']->getPronombre(),
                'prodetalle' => $compraitem['objProducto']->getProdetalle(),
                'procantstock' => $stockActualizado,
                'precioprod' => $compraitem['objProducto']->getPrecioprod()
            ];

            if ($ABMproducto->modificacion($param)) {

                $param = [
                    'idcompraestado' => $compraEstado['idcompraestado'],
                    'idcompra' => $compraEstado['objCompra']->getIdcompra(),
                    'idcompraestadotipo' => $compraEstado['objCompraEstadoTipo']->getIdcompraestadotipo(),
                    'cefechaini' => $compraEstado['cefechaini'],
                    'cefechafin' => $fechaFin
                ];

                if ($ABMcompraEstado->modificacion($param)) {

                    $param = [
                        'idcompraestado' => null,
                        'idcompra' => $compraEstado['objCompra']->getIdcompra(),
                        'idcompraestadotipo' => 3,
                        'cefechaini' => $fechaFin,
                        'cefechafin' => null
                    ];

                    if (count($ABMcompraEstado->buscarArray(['idcompra' => $compraEstado['objCompra']->getIdcompra(), 'idcompraestadotipo' => 3])) === 0) {
                        if ($ABMcompraEstado->alta($param)) {
                            $compraEnviada=true;
                        } 
                    }
                } 
            } 
        }

        return $compraEnviada;
    }
     /**
     * Cancelar una compra actualizando el estado
     * @param array $datos
     * @param string $fechaFin
     * @param int $idUsuarioActual
     * @return bool
     */
    public function cancelarCompra($datos, $fechaFin, $idUsuarioActual) {
        // Verificar si este action fue llamado desde el cliente (en el botón cancelar de carrito.php) o desde depósito (en el botón de cancelar de ordenes.php)
        $cancelacionExitosa = false;
        if ($datos['comprasRol'] === 'deposito') {
            $colCompras = $this->buscarComprasConfirmadasSinFinalizar();
        } else {
            $colCompras = $this->buscarCompraIniciadaPorUsuario($idUsuarioActual);
        }

        if ($colCompras !== null && count($colCompras) > 0) {
            foreach ($colCompras as $compra) {
                if (!isset($datos['idcompra']) || $compra->getIdcompra() == $datos['idcompra']) {
                    if (!isset($datos['idcompra'])) {
                        $datos['idcompra'] = $compra->getIdcompra();
                    }

                    $compraEstadoBuscado = $datos['comprasRol'] === 'deposito' ? $this->buscarArray(['idcompra' => $datos['idcompra']])[1] : $this->buscarArray(['idcompra' => $datos['idcompra']])[0];

                    $compraEstadoModificado = [
                        'idcompraestado' => $compraEstadoBuscado['idcompraestado'],
                        'idcompra' => $datos['idcompra'],
                        'idcompraestadotipo' => $compraEstadoBuscado['objCompraEstadoTipo']->getIdcompraestadotipo(),
                        'cefechaini' => $compraEstadoBuscado['cefechaini'],
                        'cefechafin' => $fechaFin
                    ];

                    if ($this->modificacion($compraEstadoModificado)) {
                        $paramCompraEstado = [
                            'idcompraestado' => null,
                            'idcompra' => $datos['idcompra'],
                            'idcompraestadotipo' => 4, // Estado "cancelado"
                            'cefechaini' => $fechaFin,
                            'cefechafin' => null
                        ];

                        if ($this->alta($paramCompraEstado)) {
                            $cancelacionExitosa = true;
                        }
                    }
                }
            }
        }

        return $cancelacionExitosa;
    }
    /**
     * Obtener todos los datos necesarios
     * @return array
     */
    public function obtenerDatos() {
        $ABMCompra = new ABMCompra;
        $ABMUsuario = new ABMUsuario;
        $ABMProducto = new ABMProducto;
        $ABMCompraitem = new ABMCompraItem;
        $ABMUsuarioRol = new ABMUsuarioRol;
        $ABMCompraestado = new ABMCompraEstado;

        $arrAsocUsuariosRol = [];
        $arrUsuariosActivos = [];
        $arrCompraEstados = [];
        $arrVentas = [];

        foreach($ABMUsuarioRol->buscarArray(null) as $usRol){
            $arrAsocUsuariosRol[] = ['idusuario' => $usRol['objUsuario']->getIdusuario(),'idrol' => $usRol['objRol']->getIdrol()];
        }

        foreach($ABMUsuario->buscarArray(null) as $usuario){
            $arrUsuariosActivos[] = $usuario['usdeshabilitado'];
        }

        foreach($ABMCompraestado->buscarArray(null) as $CompraEstado){
            $arrCompraEstados[] = $CompraEstado['objCompraEstadoTipo']->getIdcompraestadotipo();
        }

        $arrVentas = $ABMCompraestado->ventas();

        $datos = [
            'compras' => $ABMCompra->buscarArray(null),
            'usuarios' => $ABMUsuario->buscarArray(null),
            'cantUsuariosActivos' => $arrUsuariosActivos, 
            'ventas' => $arrVentas,
            'productos' => $ABMProducto->buscarArray(null),
            'compraitem' => $ABMCompraitem->buscarArray(null),
            'usuariorol' => $arrAsocUsuariosRol,
            'colCompraEstados' => $arrCompraEstados,
            'compraestado' => $ABMCompraestado->buscarArray(null)
        ];

        return $datos;
    }

}
?>