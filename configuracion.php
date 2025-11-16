<?php
// Define la raíz del proyecto
$GLOBALS['ROOT'] = $_SERVER['DOCUMENT_ROOT'] . "/PWD_TPFinal/";

include_once($GLOBALS['ROOT'] . "Utils/funciones.php");

spl_autoload_register(function ($nombreClase) {

    // Obtiene la variable de la raíz
    $root = $GLOBALS['ROOT'];

    // Lista de carpetas donde pueden estar tus clases
    // (relativas a la raíz del proyecto)
    $carpetas = [
        'Control/',
        'Modelo/',
        'Modelo/conector/',
        'Utils/'
    ];

    foreach ($carpetas as $carpeta) {
        // Construye la RUTA ABSOLUTA al archivo
        $archivo = $root . $carpeta . $nombreClase . '.php';
        
        if (file_exists($archivo)) {
            // Si encuentra el archivo, lo incluye y deja de buscar
            include_once $archivo;
            return;
        }
    }
});

?>
