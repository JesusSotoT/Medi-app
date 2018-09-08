<?php
if(isset($_POST['email'])) {

// Debes editar las próximas dos líneas de código de acuerdo con tus preferencias
$email_to = "info@medicheck.mx";
$email_subject = "Contacto desde el sitio web";

// Aquí se deberían validar los datos ingresados por el usuario
if(!isset($_POST['first-name']) ||
!isset($_POST['email']) ||
!isset($_POST['telephone']) ||
!isset($_POST['career']) ||
!isset($_POST['linkedin']) ||
!isset($_POST['comments']) ||
!isset($_POST['choose2'])) 
{

echo "<b>Ocurrió un error y el formulario no ha sido enviado. </b><br />";
echo "Por favor, vuelva atrás y verifique la información ingresada<br />";
die();
}

$email_message = "Detalles del formulario de contacto:\n\n";
$email_message .= "Nombre:  " . $_POST['first-name'] . "\n";
$email_message .= "Email:  " . $_POST['email'] . "\n";
$email_message .= "Telephone  " . $_POST['telephone'] . "\n";
$email_message .= "Puesto  " . $_POST['career'] . "\n";
$email_message .= "Linkedin  " . $_POST['linkedin'] . "\n\n";
$email_message .= "Mensaje   " . $_POST['comments'] . "\n";
$email_message .= "Medio en que conocio medicheck:  " . $_POST['choose2'] . "\n";



// Ahora se envía el e-mail usando la función mail() de PHP
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
@mail($email_to, $email_subject, $email_message, $headers);
echo"<script type=\"text/javascript\">window.location='form.html';</script>";  
}
?>