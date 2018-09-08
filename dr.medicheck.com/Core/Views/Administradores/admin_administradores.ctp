<?php

$this -> Menu -> addAction('Departamentos', 'entypo-list', array('action' => 'departamentos', 'controller' => 'administradores'));
$this -> Menu -> addModalAction('Nuevo Administrador', 'entypo-plus', array('action' => 'administrador', 'controller' => 'administradores'));

foreach($admins as $k => $a){
	// IMG
	$admins[$k]['img'] = $this -> Html -> img(array('controller' => 'img', 'action' => 'render', $a['imagenes_id']));
	
	// EDITORS
	$admins[$k]['acciones'] = $this -> Js -> modal_link('<span class="fa fa-edit"></span> Editar ', 
														array(
															'controller' => 'administradores', 
															'action' => 'administrador', 
															$a['id']), 
														array('class' => 'btn btn-xs btn-primary'));
	
	$admins[$k]['acciones'] .= "&nbsp;";
	
	// PERMISSION
	$admins[$k]['acciones'] .= $this -> Html -> link('<span class="fa fa-key"></span> Permisos ', 
													 array(
														 'controller' => 'administradores', 
														 'action' => 'permisos', 
														 $a['id'], 
														 'administrador'), 
														array('class' => 'btn btn-xs btn-default'));
	
	$admins[$k]['acciones'] .= "&nbsp;";
	
	// REMOVE
	$admins[$k]['acciones'] .= $this -> Html -> link('<span class="fa fa-trash-o"></span> Borrar ', 
													 array(
														 'controller' => 'administradores', 
														 'action' => 'borraradministrador', 
														 $a['id']), 
														array('class' => 'btn btn-xs btn-danger'),
														'¿Seguro que desas eliminar al usuario?');

	// CLEANNERS
	unset($admins[$k]['id']);
	unset($admins[$k]['password']);
	unset($admins[$k]['imagenes_id']);
	unset($admins[$k]['bloquear_acceso']);

}
?>
<div class="row">
	<div class="col-md-12">
		<?php echo $this -> Html -> cards($admins, 'nombres', 'img', 'acciones');?>
	<?php 
// echo $this -> Html -> dataTable($admins, array('show' => array('nombres', 'email', 
                                                                         # 'roles',
                                                                         #'acciones'))); ?>
	</div>
</div>
