<?php
include_once '../../configuracion.php';
$session = new Session();
// verificación para saber si el usuario está logueado o no y así saber si mostrarle la vista pública o privada.
if ($session->activa() && $session->validar()) { //si la sesion es valida se muestra headerSeguro.php
    include_once('../estructura/headerSeguro.php');
    $sesionActiva = true;
    // creo un objeto de la clase abmUsuarioRol para llamar a la funcion verificarRolUsuario
    $abmUsuarioRol = new ABMUsuarioRol(); 

    // Obtener el ID del usuario en la sesion para verificar si tiene permisos
    $idUsuario = $session->getUsuario()->getIdUsuario();

    // Verificar si el usuario tiene permisos para acceder a esta página (el 3 es el cliente o sea que le estoy
    // diciendo que si el usuario no es cliente lo redirija al login)
    $usuarioPermitido = $abmUsuarioRol->verificarRolUsuario($idUsuario, 3);
    if (!$usuarioPermitido) {
        header('Location: ../Home/login.php');
        exit();
    }
} else {
    include_once('../estructura/header.php');  //si la sesion no es valida se muestra header.php
    $sesionActiva = false; 
}

$ABMcompraitem = new ABMCompraItem;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Nuestros productos</h1>
    <div class="row mb-5" id="prodContainer">
        <!-- Aquí se insertarán los productos -->
    </div>
</div>

<script>
    $(document).ready(function(){
        var colIMGS = [
            '../imagenes/notebookIMG1.jpg',
            '../imagenes/notebookIMG2.jpg',
            '../imagenes/notebookIMG3.jpg',
            '../imagenes/notebookIMG4.jpg',
            '../imagenes/notebookIMG5.jpg'
        ];

        $.ajax({
            url: '../Action/buscarProductos.php',
            method: 'GET',
            success: function(data){
                var productos = JSON.parse(data);
                $('#prodContainer').empty();
                function itemStock(cantStock){
                    let opciones = '';
                    for(var i = 1; i <= cantStock; i++){
                        opciones += `<option value="${i}">${i}</option>`;
                    }
                    return opciones;
                }
                productos.forEach(function(producto, index){
                    var imgSrc = colIMGS[index % colIMGS.length];

                    // Crear el contenedor del producto
                    var productCard = $(`
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="${imgSrc}" class="card-img-top" alt="Product 1">
                                <div class="card-body">
                                    <h5 class="card-title">` + producto.pronombre + `</h5>
                                    <p class="card-text">` + producto.prodetalle + `</p>
                                    <p class="text-success">precio (unidad): $` + producto.precioprod + `.00</p>
                                    <div class="form-group">
                                        <div class=" d-flex justify-content-between">
                                            <label for="cantidadSelect">Cantidad:</label>
                                            <small class="text-muted cantStock">Stock disponible: ` + producto.procantstock + ` unidades</small>
                                        </div>
                                        <select class="form-select cantidadSelect">
                                            ` + itemStock(producto.procantstock) + `
                                        </select>
                                    </div>
                                    <button class="btn btn-primary m-2" data-id="` + producto.idproducto + `">Agregar al carrito</button>
                                </div>
                            </div>
                        </div>`);

                    // Enviar petición para verificar el estado del producto
                    $.ajax({
                        url: '../Action/verificarEstadoProducto.php', // Ruta del archivo PHP creado
                        method: 'POST',
                        data: { idproducto: producto.idproducto, form_security_token: 'valor_esperado' },
                        success: function(response) {
                            // Determinar si se deshabilita el botón según el estado retornado
                            if (response.estado == 1) {
                                productCard.find('.btn-primary').attr('disabled', 'disabled');
                            }
                        }
                    });

                    // Añadir el contenedor del producto al DOM
                    $('#prodContainer').append(productCard);
                });
            }
        });

        /* evento click que se activa para cada producto INDIVIDUAL  */
        $(document).on('click','.btn-primary',function(event){
            event.preventDefault();
            <?php if (!$sesionActiva): ?>
                window.location.href = '../Home/login.php';
            <?php else: ?>
                var card = $(this).closest('.card');
                var idProducto = $(this).data('id');
                var productoIMG = card.find('.card-img-top').attr('src');
                let productoNombre = $(this).siblings('.card-title').first().text();
                let productoDetalle = $(this).siblings('.card-text').first().text();
                let precioTexto = $(this).siblings('.text-success').first().text();
                let productoPrecioRegex = precioTexto.match(/\$(\d+)\.00/);
                let productoPrecio = productoPrecioRegex ? parseInt(productoPrecioRegex[1], 10) : 0;
                let productoCantStock = card.find('.cantStock').text().match(/Stock disponible:\s*(\d+)\s*unidades/);
                let productoCantSelec = card.find('.cantidadSelect').val();

                let objProducto = {
                    idproducto: idProducto,
                    prodNombre: productoNombre,
                    prodDetalle: productoDetalle,
                    prodPrecio: productoPrecio,
                    prodCantStock: productoCantStock ? parseInt(productoCantStock[1], 10) : 0,
                    prodCantSelec: parseInt(productoCantSelec, 10),
                    prodIMG: productoIMG
                };

                // Enviar datos a la base de datos
                $.ajax({
                    url: '../Action/agregarCompra.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(objProducto),
                    success: function(response){
                        console.log('Producto agregado al carrito', response);
                    },
                    error: function() {
                        console.log('Error al agregar el producto al carrito.');
                    }
                });
            <?php endif; ?>
        });
    });
</script>
</body>
</html>

<?php
include_once('../estructura/footer.php');
?>