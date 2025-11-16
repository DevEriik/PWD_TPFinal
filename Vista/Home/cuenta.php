<?php
include_once('../estructura/headerSeguro.php');

// creo un objeto de la clase abmUsuarioRol para llamar a la funcion verificarRolUsuario
$abmUsuarioRol = new ABMUsuarioRol(); 

// Obtener el ID del usuario en la sesion para verificar si tiene permisos
$idUsuarioActual = $session->getUsuario()->getIdUsuario();

// Verificar si el usuario tiene permisos para acceder a esta página (el 3 es el cliente o sea que le estoy
// diciendo que si el usuario no es cliente lo redirija al login)
$usuarioPermitido = $abmUsuarioRol->verificarRolUsuario($idUsuarioActual, 3);
if (!$usuarioPermitido) {
    header('Location: ../Home/login.php');
    exit();
}

$ABMUsuario = new ABMUsuario;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta</title>
</head>
<body>
<div class="container mt-1">
    <h2 class="text-center">Cambiar Datos de Usuario</h2>
    <p class=" text-center text-muted">Deje el campo vacío de los datos que no desea actualizar.</p>
    <form action="../Action/actualizarLogin.php" method="POST" id="modificarDatos">
        <input type="hidden" name="form_security_token" value="valor_esperado"> <!-- Token de seguridad -->

        <!-- Nombre de Usuario -->
        <div class="form-floating m-2">
            <input type="text" class="form-control" name="nombreActual" id="nombreActual" placeholder="Ingrese su nombre de usuario actual">
            <label for="nombreActual" for="floatingUsername">Nombre de Usuario Actual <small><b>(obligatorio)</b></small></label>
        </div>

        <div class="form-floating m-2">
            <input type="text" class="form-control" name="nuevoNombre" id="nuevoNombre" placeholder="Ingrese su nuevo nombre de usuario">
            <label for="nuevoNombre" for="floatingUsername">Nuevo Nombre de Usuario </label>
        </div>

        <!-- Contraseña -->
        <div class=" bg-secondary p-2">
            <div class="form-floating m-2">
                <input type="text" class="form-control" name="contraseñaActual" id="contraseñaActual" placeholder="Ingrese su contraseña actual">
                <label for="contraseñaActual" for="floatingPassword">Contraseña Actual <small><b>(obligatorio)</b></small></label>
            </div>
    
            <div class="form-floating m-2">
                <input type="password" class="form-control" name="nuevaContraseña" id="nuevaContraseña" placeholder="Ingrese la nueva contraseña">
                <label for="nuevaContraseña" for="floatingPassword">Nueva Contraseña</label>
            </div>
    
            <div class="form-floating m-2">
                <input type="password" class="form-control" name="nuevaContraseñaConfirm" id="nuevaContraseñaConfirm" placeholder="verifique la nueva contraseña">
                <label for="contraNuevaConfirmada" for="floatingPassword">Confirmar Nueva Contraseña</label>
            </div>
        </div>

        <!-- Correo Electrónico -->
        <div class="form-floating m-2">
            <input type="email" class="form-control" name="emailActual" id="emailActual" placeholder="Ingrese su email actual">
            <label for="emailActual" for="floatingEmail">Email actual <small><b>(obligatorio)</b></small></label>
        </div>

        <div class="form-floating m-2">
            <input type="email" class="form-control" name="nuevoEmail" id="nuevoEmail" placeholder="Ingrese el nuevo email">
            <label for="nuevoEmail" for="floatingEmail">Nuevo Email</label>
        </div>

        <!-- Botón de Enviar -->
        <button type="submit" class="btn btn-primary">Cambiar Datos</button>
    </form>
    <div id="mensaje" class="text-center mt-3"></div>
</div>

<script>
function hashPassword(password) {
    return CryptoJS.SHA256(password).toString();
}

$(document).ready(function(){
    $(document).on('click','.btn-primary',function(event){
        // Evitar el comportamiento por defecto del botón
        event.preventDefault();
        
        let actualName = $('#nombreActual').val();
        let newName = $('#nuevoNombre').val();
        let actualPass = $('#contraseñaActual').val();
        let newPass = $('#nuevaContraseña').val();
        let confirmPass = $('#nuevaContraseñaConfirm').val();
        let actualEmail = $('#emailActual').val();
        let newEmail = $('#nuevoEmail').val();
        
        // Hashear la contraseña actual
        let passHash = hashPassword(actualPass);
        
        // Hashear la nueva contraseña si no está vacía
        let newPassHash = newPass ? hashPassword(newPass) : '';
        
        // Crear la solicitud AJAX que buscará los datos 
        $.ajax({
            url: '../Action/compararDatos.php', // Ruta al script en Action
            method: 'POST',
            data: {
                nombreActual: actualName,
                nuevoNombre: newName,
                contraseñaActual: passHash,
                nuevaContraseña: newPassHash,
                emailActual: actualEmail,
                nuevoEmail: newEmail,
                form_security_token: 'valor_esperado' // Token de seguridad
            },
            success: function(data){
                console.log("colusers: "+data.usersBD[0]['usnombre']);
                
                // Acceder a la clave colUsers, que contiene un arreglo indexado con los usuarios del sistema
                var colUsers = data.usersBD;

                // Banderas para verificar los datos actuales:
                var nombreCorrecto = true;
                var passCorrecta = true;
                var emailCorrecto = true;

                // Banderas para verificar los datos nuevos:
                var newNombreValid = true;
                var newPassValid = true;
                var newEmailValid = true;

                if(data.userActual['usnombre'] === actualName){
                    nombreCorrecto = true;
                    $('#nombreActual').css('border', '2px solid green'); 
                    $('#nombreActual').next('label').text('Nombre de Usuario correcto').css('color', 'green');
                    console.log("nombres coincidentes");
                } else {
                    nombreCorrecto = false;
                    $('#nombreActual').css('border', '2px solid red'); 
                    $('#nombreActual').next('label').text('nombre de usuario incorrecto').css('color', 'red');
                    console.log("nombres no coinciden");
                }
                
                if(passHash === data.userActual['uspass']){
                    passCorrecta = true;
                    $('#contraseñaActual').css('border', '4px solid green'); 
                    $('#contraseñaActual').next('label').text('Contraseña correcta').css('color', 'green');
                    console.log("contraseñas hasheadas coincidentes");
                } else {
                    passCorrecta = false;
                    $('#contraseñaActual').css('border', '2px solid red'); 
                    $('#contraseñaActual').next('label').text('contraseña ingresada incorrecta').css('color', 'red');
                    console.log("contraseñas hasheadas distintas. La actual: "+data.userActual['uspass']+" la ingresada: "+passHash);
                }
                
                if(actualEmail === data.userActual['usmail']){
                    emailCorrecto = true;
                    $('#emailActual').css('border', '2px solid green'); 
                    $('#emailActual').next('label').text('Email correcto').css('color', 'green');
                    console.log("emails coincidentes");
                } else {
                    emailCorrecto = false;
                    $('#emailActual').css('border', '2px solid red'); 
                    $('#emailActual').next('label').text('email ingresado incorrecto').css('color', 'red');
                    console.log("emails distintos");
                }

                // Verificar los nuevos campos
                if(newName.trim().length > 0){
                    userEncontrado = colUsers.find(user => user.usnombre.toLowerCase() === newName.toLowerCase());
                    if(userEncontrado !== undefined){
                        newNombreValid = false;
                        $('#nuevoNombre').css('border', '2px solid red');
                        $('#nuevoNombre').next('label').text('Nombre de Usuario no disponible').css('color', 'red');
                    } else {
                        newNombreValid = true;
                        $('#nuevoNombre').css('border', '2px solid green');
                        $('#nuevoNombre').next('label').text('Nombre de Usuario disponible').css('color', 'green');
                    }
                } else {
                    $('#nuevoNombre').css('border', '');
                    $('#nuevoNombre').next('label').text('Nuevo Nombre de Usuario').css('color', '');
                }

                if(newEmail.trim().length > 0){
                    emailEncontrado = colUsers.find(user => user.usmail === newEmail);
                    if(emailEncontrado){
                        newEmailValid = false;
                        $('#nuevoEmail').css('border', '2px solid red');
                        $('#nuevoEmail').next('label').text('Email no disponible').css('color', 'red');
                    } else {
                        newEmailValid = true;
                        $('#nuevoEmail').css('border', '2px solid green');
                        $('#nuevoEmail').next('label').text('Email disponible').css('color', 'green');
                    }
                } else {
                    $('#nuevoEmail').css('border', '');
                    $('#nuevoEmail').next('label').text('Nuevo Email').css('color', '');
                }

                if(newPass.trim().length > 0 || confirmPass.trim().length > 0){
                    if(newPass !== confirmPass){
                        newPassValid = false;
                        $('#nuevaContraseña').css('border', '2px solid red');
                        $('#nuevaContraseña').next('label').text('Las contraseñas son distintas').css('color', 'red');
                        $('#nuevaContraseñaConfirm').css('border', '2px solid red');
                        $('#nuevaContraseñaConfirm').next('label').text('Las contraseñas son distintas').css('color', 'red');
                    } else {
                        newPassValid = true;
                        $('#nuevaContraseña').css('border', '2px solid green');
                        $('#nuevaContraseña').next('label').text('Contraseñas iguales').css('color', 'green');
                        $('#nuevaContraseñaConfirm').css('border', '2px solid green');
                        $('#nuevaContraseñaConfirm').next('label').text('Contraseñas iguales').css('color', 'green');
                    }
                } else {
                    $('#nuevaContraseña').css('border', '');
                    $('#nuevaContraseña').next('label').text('Nueva Contraseña').css('color', '');
                    $('#nuevaContraseñaConfirm').css('border', '');
                    $('#nuevaContraseñaConfirm').next('label').text('Confirmar Nueva Contraseña').css('color', '');
                }

                // Si todos los datos ingresados por el usuario coinciden con los que tiene
                if(emailCorrecto && nombreCorrecto && passCorrecta && newNombreValid && newPassValid && newEmailValid){
                    // Enviar el formulario con las contraseñas hasheadas
                    $('#contraseñaActual').val(passHash);
                    $('#nuevaContraseña').val(newPassHash);

                    // Crear la solicitud AJAX para actualizar los datos
                    $.ajax({
                        url: '../Action/actualizarLogin.php', // Ruta al script en Action
                        method: 'POST',
                        data: {
                            nombreActual: actualName,
                            nuevoNombre: newName,
                            contraseñaActual: passHash,
                            nuevaContraseña: newPassHash,
                            emailActual: actualEmail,
                            nuevoEmail: newEmail,
                            form_security_token: 'valor_esperado' // Token de seguridad
                        },
                        success: function(response){
                            console.log(response); 
                            if (response.status === 'success') {
                                $('#mensaje').html('<div class="alert alert-success">Datos actualizados con éxito.</div>');
                                setTimeout(function() {
                                    window.location.href = response.redirect;
                                }, 1500); // Esperar 1.5 segundos antes de redirigir
                            } else {
                                $('#mensaje').html('<div class="alert alert-danger">' + response.message + '</div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText); 
                            $('#mensaje').html('<div class="alert alert-danger">Error al actualizar los datos. Por favor, inténtelo de nuevo.</div>');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText); 
                console.log('Error al recibir los datos.');
            }
        });
    });
});
</script>
</body>
</html>
<?php 
include_once('../estructura/footer.php');
?>