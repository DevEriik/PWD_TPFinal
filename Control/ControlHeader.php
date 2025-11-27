<?php
class ControlHeader {

    /**
     * Verifica la sesión, los roles y obtiene los datos para el header.
     * Si la validación falla, REDIRIGE automáticamente al login.
     * @return array Retorna configuración del header y la sesión activa.
     */
    public function obtenerDatosHeader() {
        $session = new Session();
        
        
        if (!$session->activa() || !$session->validar()) {
            header('Location: ../Home/login.php');
            exit(); 
        }

        // Obtener Roles
        $roles = $session->getRol();
        $idRol = null;

        if ($roles != null && count($roles) > 0) {
            $idRol = $roles[0]->getObjRol()->getIdrol();
        } else {
            
            header('Location: ../Home/login.php');
            exit();
        }

        
        $abmMenuRol = new ABMMenuRol();
        $menus = $abmMenuRol->buscar(['idrol' => $idRol]);

        
        $colorFondo = 'bg-light';
        if ($idRol == 1) { 
            $colorFondo = 'bg-warning';
        } elseif ($idRol == 2) { 
            $colorFondo = 'bg-secondary';
        } elseif ($idRol == 3) { 
            $colorFondo = 'bg-success';
        }

        
        return [
            'menus' => $menus,
            'colorFondo' => $colorFondo,
            'usuarioNombre' => $session->getUsuario()->getUsnombre(),
            'objSession' => $session
        ];
    }
}
?>