<?php 
$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
$url .= $this -> url(array(
	'context' => 'admin', 
	'controller' => 'administradores', 
	'action' => 'resetpassword', 
	$userId, 
	$userHash
)); 
?>

	<link rel="stylesheet" href="css/main.css">
	<h1><?php echo $appName; ?> | Recuperación de contraseña</h1>
	<h4>¡Hola <?php echo $nombreUsuario; ?>!</h4>
	<p>
		Estás recibiendo este correo electrónico porque has solicitado restaurar tu contraseña, <strong>si tu no has hecho esta solicitud por favor ignora y elimina este correo electrónico.</strong> De otra forma, haz click en el siguente enlace para establecer el nuevo password:
		<br/>
		<a href="<?php echo $url ?>"><?php echo $url?></a>
	</p>
</body>