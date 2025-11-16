
<?php
/**
 * recupera los datos enviados ya sea por GET o POST, manteniendo el arreglo asociativo
 * @return array
 */
function darDatosSubmitted() {
    $datos = [];

    // Recogemos datos de POST y GET
    foreach ($_GET as $key => $value) {
        $datos[$key] = $value;
    }
    foreach ($_POST as $key => $value) {
        $datos[$key] = $value;
    }
    // Añadimos los datos del archivo
    if (array_key_exists('imagen',$_FILES) ) {
        $datos['imagen'] = $_FILES['imagen'];
    }
    return $datos;
}

/**
 * Recibe un objeto y convierte sus propiedades a un arreglo asociativo.
 * @param object $object
 * @return array  
 */
function dismount($object) {
    // con get_class obtenemos el nombre de la clase y reflectionClass obtenemos y manipulamos información sobre el $object
    $reflectionClass = new ReflectionClass(get_class($object));
    $array = array();
    // recorremos la lista de propiedades de $reflectionClass (adquiridas de $object),
    foreach ($reflectionClass->getProperties() as $property) {
        // cambiamos la visibilidad de la propiedad a Accesible (en caso de que fuera private o protected), para poder acceder a sus valores.
        $property->setAccessible(true);
        // ingresamos el nombre de la propiedad como una clave en el arreglo asociativo creado, con su respectivo valor.
        $array[$property->getName()] = $property->getValue($object);
        // reestablecemos la visibilidad de la propiedad a su estado original. 
        $property->setAccessible(false);
    }
    return $array;
}

/**
 * Recibe un arreglo de objetos y devuelve un arreglo con arreglos asociativos.
 * @param array
 */
function convert_array($param) {
    $_AAux= array();
    if (!empty($param)) {
        if (count($param)){
            foreach($param as $obj) {
                // itera sobre el arreglo de objetos y los convierte a arreglos asociativos
                array_push($_AAux,dismount($obj));    
            }
        }
    }
    return $_AAux;
}

spl_autoload_register(function ($clase) {
    // echo "Cargamos la clase  ".$clase."<br>" ;

     $directorys = array(
         $GLOBALS['ROOT'].'Modelo/',
         $GLOBALS['ROOT'].'Control/',
         $GLOBALS['ROOT'].'Modelo/Conector/',
     );
     $found = false;
     $i = 0;
     while ($i < count($directorys) && !$found){
        if (file_exists($directorys[$i].$clase.'.php')){
            require_once($directorys[$i].$clase.'.php');
            $found = true;
        }
        $i++;
     }
 });

 

function SubirArchivoSubmitted() {
    $dir = '../../Archivos/'; // Definimos el Directorio donde se guarda el archivo
    $mensaje = ""; // Inicializamos la variable para almacenar el mensaje

    // Comprobamos que no se hayan producido errores
    if ($_FILES['archivo']["error"] <= 0) {
        $mensaje .= "Nombre: " . $_FILES['archivo']['name'] . "<br />";
        $mensaje .= "Tipo: " . $_FILES['archivo']['type'] . "<br />";
        $mensaje .= "Tamaño: " . ($_FILES['archivo']["size"] / 1024) . " kB<br />";
        $mensaje .= "Carpeta temporal: " . $_FILES['archivo']['tmp_name'] . "<br />";

        // Intentamos copiar el archivo al servidor.
        if (!copy($_FILES['archivo']['tmp_name'], $dir . $_FILES['archivo']['name'])) {
            $mensaje .= "ERROR: no se pudo cargar el archivo.";
        } else {
            $mensaje .= "El archivo " . $_FILES['archivo']['name'] . " se ha copiado con Éxito.<br />";
        }
    } else {
        $mensaje .= "ERROR: no se pudo cargar el archivo. No se pudo acceder al archivo Temporal.";
    }

    return $mensaje; // Devolvemos el mensaje
}

function SubirImagenSubmitted() {
    $dir = '../../Archivos/Imagenes/'; // Definimos el Directorio donde se guarda el archivo
    $mensaje = ""; // Inicializamos la variable para almacenar el mensaje

    // Comprobamos que no se hayan producido errores
    if ($_FILES['imagen']["error"] <= 0) {
        $mensaje .= "Nombre: " . $_FILES['imagen']['name'] . "<br />";
        $mensaje .= "Tipo: " . $_FILES['imagen']['type'] . "<br />";
        $mensaje .= "Tamaño: " . ($_FILES['imagen']["size"] / 1024) . " kB<br />";
        $mensaje .= "Carpeta temporal: " . $_FILES['imagen']['tmp_name'] . "<br />";

        // Intentamos copiar el archivo al servidor.
        if (!copy($_FILES['imagen']['tmp_name'], $dir . $_FILES['imagen']['name'])) {
            $mensaje .= "ERROR: no se pudo cargar el archivo.";
        } else {
            $mensaje .= "El archivo " . $_FILES['imagen']['name'] . " se ha copiado con Éxito.<br />";
        }
    } else {
        $mensaje .= "ERROR: no se pudo cargar el archivo. No se pudo acceder al archivo Temporal.";
    }

    return $mensaje; // Devolvemos el mensaje
}

function mostrarCompras($compras, $ABMCompraItem) {
    foreach ($compras as $compra) {
        echo '<div class="col-12 mb-4">';
        echo '<div class="card">';
        echo '<div class="card-header">';
        echo '<h6 class="card-subtitle mb-2 text-muted">Fecha de Compra: ' . htmlspecialchars($compra->getCofecha()) . '</h6>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<h6>Productos:</h6>';
        echo '<ul>';
        $compraItems = $ABMCompraItem->buscar(['idcompra' => $compra->getIdcompra()]);
        foreach ($compraItems as $item) {
            echo '<li>';
            echo 'Producto: ' . htmlspecialchars($item->getObjProducto()->getPronombre()) . ', ';
            echo 'Cantidad: ' . htmlspecialchars($item->getCicantidad());
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}


?>

