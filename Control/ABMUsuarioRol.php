<?php

class ABMUsuarioRol {

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
     * @return UsuarioRol
     */
    private function cargarObjeto($param) {
        $obj = null;

        if (array_key_exists('idusuario', $param) && array_key_exists('idrol', $param)) {
            $usuario = new Usuario();
            $usuario->setIdusuario($param['idusuario']);
            $usuario->cargar();

            $rol = new Rol();
            $rol->setIdrol($param['idrol']);
            $rol->cargar();

            $obj = new UsuarioRol();
            $obj->setear($usuario, $rol);
        }

        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return UsuarioRol
     */
    private function cargarObjetoConClave($param) {
        $obj = null;

        if (isset($param['idusuario']) && isset($param['idrol'])) {
            $usuario = new Usuario();
            $usuario->setIdusuario($param['idusuario']);
            $usuario->cargar();

            $rol = new Rol();
            $rol->setIdrol($param['idrol']);
            $rol->cargar();

            $obj = new UsuarioRol();
            $obj->setear($usuario, $rol);
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
        if (isset($param['idusuario']) && isset($param['idrol']))
            $resp = true;
        return $resp;
    }

    /**
     * permite ingresar un objeto
     * @param array $param
     */
    public function alta($param) {
        $resp = false;
        $objUsuarioRol = $this->cargarObjeto($param);
        if ($objUsuarioRol != null and $objUsuarioRol->insertar()) {
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
            $objUsuarioRol = $this->cargarObjetoConClave($param);
            if ($objUsuarioRol != null and $objUsuarioRol->eliminar()) {
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
            $objUsuarioRol = $this->cargarObjeto($param);
            if ($objUsuarioRol != null and $objUsuarioRol->modificar()) {
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
            if (isset($param['idusuario'])) $where .= " and idusuario ='" . $param['idusuario'] . "'";
            if (isset($param['idrol'])) $where .= " and idrol ='" . $param['idrol'] . "'";
        }
        $usuarioRol = new UsuarioRol();
        $arreglo = $usuarioRol->listar($where);
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
     * Obtiene la descripción del rol de un usuario
     */
    public function obtenerDescripcionRol($idUsuario) {
        $rolesUsuario = $this->buscar(['idusuario' => $idUsuario]);
        $descripciones = [];
        foreach ($rolesUsuario as $usuarioRol) {
            $rol = $usuarioRol->getObjRol();
            $descripciones[] = $rol->getRodescripcion();
        }
        return $descripciones;
    }

     /**
     * Obtiene el menú basado en los roles del usuario
     */
    public function obtenerMenuUsuario($idUsuario) {
        $rolesUsuario = $this->obtenerDescripcionRol($idUsuario);

        // Definir los permisos para cada rol
        $permisos = [
            'Cliente' => ['Inicio', 'Listar Usuarios'],
            'Deposito' => ['Inicio', 'Listar Usuarios', 'Actualizar Usuarios', 'Eliminar Usuarios'],
            'Administrador' => ['Inicio', 'Listar Usuarios', 'Actualizar Usuarios', 'Eliminar Usuarios', 'Asignar Roles']
        ];

        // Generar el menú basado en los roles del usuario
        $menuItems = [];
        foreach ($rolesUsuario as $rolUsuario) {
            if (isset($permisos[$rolUsuario])) {
                $menuItems = array_merge($menuItems, $permisos[$rolUsuario]);
            }
        }
        return array_unique($menuItems);
    }

    /**
     * Modifica el rol de un usuario
     * @param array $param
     * @return boolean
     */
    public function modificarRol($param) {
        $resp = false;
        $objUsuarioRol = $this->cargarObjetoConClave($param);
        if ($objUsuarioRol != null) {
            $objUsuarioRol->setObjRol(new Rol());
            $objUsuarioRol->getObjRol()->setIdrol($param['idrol']);
            //echo "Objeto UsuarioRol cargado: " . $objUsuarioRol->__toString() . "<br>";
            if ($objUsuarioRol->modificar()) {
                $resp = true;
            } else {
                //echo "Error al modificar el objeto UsuarioRol.<br>";
                $resp=false;
            }
        } else {
            //echo "Error al cargar el objeto UsuarioRol.<br>";
            $resp=false;
        }
        return $resp;
    }
    /**
     * Verifica si un usuario tiene un rol específico
     * @param int $idUsuario
     * @param int $idRol
     * @return boolean
     */
    public function verificarRolUsuario($idUsuario, $idRol) {
        $rolesUsuario = $this->buscar(['idusuario' => $idUsuario]);
        $rolPermitido = false;
        foreach ($rolesUsuario as $usuarioRol) {
            if ($usuarioRol->getObjRol()->getIdrol() == $idRol) {
               $rolPermitido = true;
            }
        }
        return $rolPermitido;
    }
    /**
     * Asignar un único rol a un usuario
     * @param array $datos
     * @return array
     */
    public function asignarRolUnico($datos) {

        $rolAsignado = false;

        $datos['idusuario'] = $datos['id']; // Se espera que el ID del usuario esté en 'id'
        $datos['idrol'] = $datos['rol'];  // Se espera que el ID del rol esté en 'rol'

        $idUsuario = $datos['idusuario'];
        $idRol = $datos['idrol'];

        // Verificar si el usuario ya tiene algún rol asignado
        $usuarioRolesExistentes = $this->buscar(['idusuario' => $idUsuario]);
        if (count($usuarioRolesExistentes) > 0) {
            // Eliminar todos los roles existentes del usuario
            foreach ($usuarioRolesExistentes as $usuarioRol) {
                $this->baja(['idusuario' => $idUsuario, 'idrol' => $usuarioRol->getObjRol()->getIdrol()]);
            }
        }

        // Asignar el nuevo rol al usuario
        $param = [
            'idusuario' => $idUsuario,
            'idrol' => $idRol
        ];

        if ($this->alta($param)) {
            $rolAsignado = true;
        } 

        return $rolAsignado;
    }
}
?>