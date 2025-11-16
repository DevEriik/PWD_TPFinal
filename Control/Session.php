<?php

class Session {
    public function __construct(){
        /* if (!session_start()) {
            return false;
        } else {
            return true;
        } */
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
      }

    /**
     * Actualiza las variables de sesión con los valores ingresados.
     */
    public function iniciar($nombreUsuario, $psw) {
        $resp = false;
        $obj = new ABMUsuario();
        $param['usnombre'] = $nombreUsuario;      //nombre de usuario ingresado
        $param['uspass'] = $psw;                //contraseña ingresada
        $param['usdeshabilitado'] = '0000-00-00 00:00:00'; // Usuario no deshabilitado

        $resultado = $obj->buscar($param);   //busca el usuario en la base de datos con esas caracteristias
        if (count($resultado) > 0) {
            $usuario = $resultado[0];
            session_regenerate_id(true); // Regenerar el ID de sesión para evitar fijación de sesión
            $_SESSION['idusuario'] = $usuario->getIdUsuario();
            $resp = true;
        } else {
            $this->cerrar();
        }
        return $resp;
    }

    /**
    * Valida si la sesión actual tiene usuario y psw válidos. Devuelve true o false.
    */
    public function validar() {
        $resp = false;
        if ($this->activa() && isset($_SESSION['idusuario'])) {
            $resp = true;
        }
        return $resp;
    }

    /**
    * Devuelve true o false si la sesión está activa o no.
    */
    public function activa() {
        $resp = false;
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                $resp = session_status() === PHP_SESSION_ACTIVE ? true : false;
            } else {
                $resp = session_id() === '' ? false : true;
            }
        }
        return $resp;
    }

    /**
    * Devuelve el usuario logeado.
    */
    public function getUsuario() {
        $usuario = null;
        if ($this->validar()) {
            $abmUsuario = new ABMUsuario();
            $param = ['idusuario' => $_SESSION['idusuario']];
            $resultado = $abmUsuario->buscar($param);
            if (count($resultado) > 0) {
                $usuario = $resultado[0];
            }
        }
        return $usuario;
    }

    
    /**
    * Devuelve el rol del usuario logeado.
    */
    public function getRol() {
        $list_rol = null;
        if ($this->validar()) {
            $abmUsuarioRol = new ABMUsuarioRol();
            $param = ['idusuario' => $_SESSION['idusuario']];
            $resultado = $abmUsuarioRol->buscar($param);
            if (count($resultado) > 0) {
                $list_rol = $resultado;
            }
        }
        return $list_rol;
    }

    /**
    * Cierra la sesión actual.
    */
    public function cerrar() {
        $resp = true;
        session_unset(); // Eliminar todas las variables de sesión
        session_destroy();
        return $resp;
    }
}