/* validaciones de cuenta.php */
$(document).ready(function(){

    jQuery.validator.addMethod("nombreValido",function(value,element){
        return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
    })

    $('#modificarDatos').validate({

        rules:{
            nombreActual:{
                required: true,
                nombreValido: true,
                maxlength: 40
            }
        }

    });

});