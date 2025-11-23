<?php
class ControlHeader {

    /**
     * Verifica la sesión y obtiene los datos necesarios para el header.
     * @return array|false Retorna un arreglo con menús y configuración, o false si no hay sesión.
     */
    public function obtenerDatosHeader() {
        $session = new Session();
        
        
        if (!$session->activa() || !$session->validar()) {
            return false; // Indica que falló la sesión
        }

        
        $roles = $session->getRol();
        $idRol = null;

        if ($roles != null && count($roles) > 0) {
            $idRol = $roles[0]->getObjRol()->getIdrol();
        } else {
            // Si no tiene rol, forzamos el cierre (o asignamos uno por defecto)
            return false; 
        }

        
        $abmMenuRol = new ABMMenuRol();
        $menus = $abmMenuRol->buscar(['idrol' => $idRol]);

        
        $colorFondo = 'bg-light'; // Por defecto
        if ($idRol == 1) { // Administrador
            $colorFondo = 'bg-warning';
        } elseif ($idRol == 2) { // Depósito
            $colorFondo = 'bg-secondary';
        } elseif ($idRol == 3) { // Cliente
            $colorFondo = 'bg-success';
        }

        
        return [
            'menus' => $menus,
            'colorFondo' => $colorFondo,
            'usuarioNombre' => $session->getUsuario()->getUsnombre()
        ];
    }
}
?>