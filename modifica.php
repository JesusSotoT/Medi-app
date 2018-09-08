<?php

include("conexion.php");

$link=Conectarse();

$Sql="UPDATE clase SET nombre='$nombre_med',

id='$id',

email='$correo_med' WHERE nombre='$nombre_med'";

mysql_query($Sql,$link);

?>
