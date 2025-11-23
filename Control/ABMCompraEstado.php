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
        $compras = $abmCompra->buscarPorUsuario($idusuario);
        $compraEstadoIniciado = [];

        if ($compras !== null) {
            foreach ($compras as $compra) {
                $compraEstado = $this->buscarArray(['idcompra' => $compra->getIdcompra()]);
                
                if (count($compraEstado) > 0) {
                    $estado = $compraEstado[0];
                    
                    // CORREGIDO: Acepta NULL o 0000-00-00
                    $esIniciada = $estado['objCompraEstadoTipo']->getIdcompraestadotipo() === 1;
                    $fechaFin = $estado['cefechafin'];
                    $esActiva = ($fechaFin === null || $fechaFin === '0000-00-00 00:00:00');

                    if($esIniciada && $esActiva){
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
     * @return array|null 
    */
    public function estadoCompraUsuario($idusuario,$estado,$fechafin){
        $abmCompra = new ABMCompra;
        $comprasUsuario = $abmCompra->buscarPorUsuario($idusuario);
        $comprasEspecificadas = null;
        
        if(count($comprasUsuario) > 0){
            foreach($comprasUsuario as $compraUser){
                // CORREGIDO: Búsqueda flexible
                if($fechafin === true){
                    // Busca manual porque el array search estricto fallaría con null
                    $estados = $this->buscarArray(['idcompraestadotipo' => $estado,'idcompra' => $compraUser->getIdcompra()]);
                    $compraEstado = [];
                    foreach($estados as $est){
                        if($est['cefechafin'] === null || $est['cefechafin'] === '0000-00-00 00:00:00'){
                            $compraEstado[] = $est;
                        }
                    }
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
     * permite buscar compras confirmadas sin finalizar (Para Depósito)
     * @return array
     */
    public function buscarComprasConfirmadasSinFinalizar() {
        $abmCompra = new ABMCompra();
        $compras = $abmCompra->buscar(null); 

        $comprasConfirmadasSinFinalizar = [];

        foreach ($compras as $compra) {
            if (count($compras) > 0) {
                $compraEstado = $this->buscarArray(['idcompra' => $compra->getIdcompra()]);
                if (count($compraEstado) > 0) {
                    foreach ($compraEstado as $estado) {
                        // CORREGIDO: Acepta NULL o 0000-00-00
                        $esConfirmada = $estado['objCompraEstadoTipo']->getIdcompraestadotipo() === 2;
                        $fechaFin = $estado['cefechafin'];
                        $esActiva = ($fechaFin === null || $fechaFin === '0000-00-00 00:00:00');

                        if ($esConfirmada && $esActiva) {
                            $comprasConfirmadasSinFinalizar[] = $compra;
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
            if($arrCompraEstado['objCompraEstadoTipo']->getIdcompraestadotipo() === 3){
                $fechaEnviado = $arrCompraEstado['cefechaini'];

                foreach($ABMCompraitem->buscarArray(['idcompra' => $arrCompraEstado['objCompra']->getIdcompra()]) as $arrCompraitem){

                    foreach($ABMProducto->buscarArray(null) as $arrProducto){
                        if($arrCompraitem['objProducto']->getIdproducto() == $arrProducto['idproducto']){
                            $precioXcantidad = intval($arrCompraitem['cicantidad']) * intval($arrProducto['precioprod']);
                            
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
        $compras = $abmCompra->buscarPorUsuario($idusuario);

        foreach ($compras as $compra) {
            $compraEstado = $this->buscarArray(['idcompra' => $compra->getIdcompra()]);
            if (count($compraEstado) > 0) {
                $estado = $compraEstado[0];
                
                // CORREGIDO: Acepta NULL o 0000-00-00 (ESTA ERA LA QUE FALLABA AL CONFIRMAR)
                $esIniciada = $estado['objCompraEstadoTipo']->getIdcompraestadotipo() === 1;
                $fechaFin = $estado['cefechafin'];
                $esActiva = ($fechaFin === null || $fechaFin === '0000-00-00 00:00:00');
                
                if ($esIniciada && $esActiva) {
                    return $compra;
                }
            }
        }

        return null;
    }

    /**
     * Confirmar una compra: Actualiza estado, crea nuevo estado Y DESCUENTA STOCK
     * @param int $idCompra
     * @param string $fechaFin
     * @return bool
     */
    public function confirmarCompra($idCompra, $fechaFin) {
        
        $compraConfirmada = false;
        
        // 1. Buscar el estado 'Iniciada' activo para cerrarlo
        $estados = $this->buscar(['idcompra' => $idCompra, 'idcompraestadotipo' => 1]);
        $compraEstado = null;

        foreach($estados as $est) {
            if ($est->getCefechafin() == null || $est->getCefechafin() == '0000-00-00 00:00:00') {
                $compraEstado = $est;
                break;
            }
        }

        if ($compraEstado != null) {
            // Cerramos el estado actual (Iniciada)
            $compraEstadoModificado = [
                'idcompraestado' => $compraEstado->getIdcompraestado(),
                'idcompra' => $idCompra,
                'idcompraestadotipo' => $compraEstado->getObjCompraEstadoTipo()->getIdcompraestadotipo(),
                'cefechaini' => $compraEstado->getCefechaini(),
                'cefechafin' => $fechaFin
            ];

            if ($this->modificacion($compraEstadoModificado)) {
                
                // Creamos el nuevo estado (Aceptada/Confirmada)
                $paramCompraEstado = [
                    'idcompraestado' => null,
                    'idcompra' => $idCompra,
                    'idcompraestadotipo' => 2, // Estado "Aceptada"
                    'cefechaini' => $fechaFin,
                    'cefechafin' => null
                ];

                if ($this->alta($paramCompraEstado)) {
                    
                    $ABMcompraItem = new ABMCompraItem();
                    $ABMproducto = new ABMProducto();
                    
                    // Buscamos los items de esta compra
                    $items = $ABMcompraItem->buscar(['idcompra' => $idCompra]);
                    
                    foreach ($items as $item) {
                        $prod = $item->getObjProducto();
                        $nuevaCantidad = $prod->getProcantstock() - $item->getCicantidad();
                        
                        // Actualizamos el producto con la nueva cantidad
                        $paramProd = [
                            'idproducto' => $prod->getIdproducto(),
                            'pronombre' => $prod->getPronombre(),
                            'prodetalle' => $prod->getProdetalle(),
                            'procantstock' => $nuevaCantidad, // Stock restado
                            'precioprod' => $prod->getPrecioprod()
                        ];
                        $ABMproducto->modificacion($paramProd);
                    }

                    $compraConfirmada = true;
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

        $colCompraItems = $ABMcompraitem->buscarArray(['idcompra' => $idCompra]);

        // Buscamos el estado confirmado activo
        $estados = $ABMcompraEstado->buscar(['idcompra' => $idCompra, 'idcompraestadotipo' => 2]);
        $compraEstado = null;
        foreach($estados as $est) {
            if ($est->getCefechafin() == null || $est->getCefechafin() == '0000-00-00 00:00:00') {
                $compraEstado = $this->buscarArray(['idcompraestado' => $est->getIdcompraestado()])[0]; // Convertimos a array para mantener tu lógica abajo
                break;
            }
        }

        if ($compraEstado != null) {
            foreach ($colCompraItems as $compraitem) {
                $cantDescontada = $compraitem['cicantidad'];
                $stockActualizado = $compraitem['objProducto']->getProcantstock() - $cantDescontada;

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
                        // Quitamos verificación redundante para simplificar
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

                    // Búsqueda del estado activo (NULL)
                    $estados = $this->buscar(['idcompra' => $datos['idcompra']]);
                    $compraEstadoBuscado = null;
                    
                    // Buscamos el último estado activo
                    foreach($estados as $est) {
                        if ($est->getCefechafin() == null || $est->getCefechafin() == '0000-00-00 00:00:00') {
                            // Convertimos a array para mantener tu lógica
                            $compraEstadoBuscado = $this->buscarArray(['idcompraestado' => $est->getIdcompraestado()])[0];
                            break; 
                        }
                    }

                    if ($compraEstadoBuscado != null) {
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

    /**
     * Maneja la cancelación completa: BD, decisión de redirección y datos de mail.
     * @param array $datos Datos del formulario
     * @param object $objUsuarioSesion Objeto del usuario logueado
     * @return array Respuesta lista para el JSON
     */
    public function procesarCancelacionCompleta($datos, $objUsuarioSesion) {
        $fechaFin = date('Y-m-d H:i:s');
        $idUsuario = $objUsuarioSesion->getIdusuario();

        // 1. Ejecutar la lógica de base de datos (usando tu función existente)
        $exito = $this->cancelarCompra($datos, $fechaFin, $idUsuario);

        if (!$exito) {
            return ['status' => 'error', 'message' => 'No se pudo realizar la cancelación.'];
        }

        // 2. Lógica de "A quién notificamos y a dónde vamos" (Lo que sacamos del Action)
        $response = ['status' => 'success', 'message' => 'Operación exitosa'];

        if (isset($datos['comprasRol']) && $datos['comprasRol'] === 'deposito') {
            // CASO A: Cancela el Depósito -> Buscamos al dueño de la compra
            $ABMCompra = new ABMCompra();
            $datosCliente = $ABMCompra->clienteAsociadoALaCompra($datos['idcompra']);
            
            $response['toName'] = $datosCliente['name'];
            $response['toEmail'] = $datosCliente['email'];
            $response['redirect'] = '../Home/ordenes.php';
        } else {
            // CASO B: Cancela el Cliente -> Usamos sus propios datos
            $response['toName'] = $objUsuarioSesion->getUsnombre();
            $response['toEmail'] = $objUsuarioSesion->getUsmail();
            $response['redirect'] = '../Home/carrito.php';
        }

        return $response;
    }

    /**
     * Procesa el envío: Cambia estados, resta stock y prepara datos de respuesta.
     * @param int $idCompra
     * @return array Respuesta lista para el JSON
     */
    public function procesarEnvioCompra($idCompra) {
        $fechaFin = date('Y-m-d H:i:s');
        
        // 1. Ejecutar la lógica dura (BD)
        $exito = $this->enviarCompra($idCompra, $fechaFin);

        // 2. Preparar respuesta
        $response = [
            'status' => 'error',
            'message' => 'Error al enviar la compra (Revise stock o estado).',
            'redirect' => '../Home/ordenes.php'
        ];

        if ($exito) {
            // Si salió bien, buscamos los datos del cliente AQUÍ, no en el Action
            $ABMCompra = new ABMCompra();
            $datosCliente = $ABMCompra->clienteAsociadoALaCompra($idCompra);
            
            $response['status'] = 'success';
            $response['message'] = 'Producto Enviado con éxito.';
            $response['toName'] = $datosCliente['name'];
            $response['toEmail'] = $datosCliente['email'];
        }

        return $response;
    }

}
?>