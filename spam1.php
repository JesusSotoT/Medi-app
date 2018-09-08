<?php

if(isset($_POST['dejarenblanco'])){
    $dejarenblanco = $_POST['dejarenblanco'];
}
if(isset($_POST['nocambiar'])){
    $nocambiar = $_POST['nocambiar'];
}


if ($dejarenblanco == '' && $nocambiar == 'http://') { 
    // código para enviar el formulario

    // Enviarlo
    mail('tu@correo.com', 'Asunto: Probando formulario de contacto...', $msj);



}
    
    
?>