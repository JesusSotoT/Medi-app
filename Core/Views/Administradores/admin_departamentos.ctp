<?php

$this -> Menu -> addModalAction('Nuevo rol de administradores', 'entypo-plus', array('action' => 'departamento', 'controller' => 'administradores'));
$this -> Menu -> addAction('Administradores', 'entypo-list', array('action' => 'administradores', 'controller' => 'administradores'));

foreach($departamentos as $k => $d){

	$departamentos[$k]['acciones'] = "";

	$departamentos[$k]['acciones'] = $this -> Js -> modalLink('Editar', array('controller' => 'administradores', 'action' => 'departamento', $d['id']));
	$departamentos[$k]['acciones'] .= $this -> Html -> btnLink('Permisos', array('controller' => 'administradores', 'action' => 'permisos', $d['id'], 'departamento'), 'primary', 'fa-key');
	$departamentos[$k]['acciones'] .= $this -> Html -> btnLink('Borrar', array('controller' => 'administradores', 'action' => 'borrardepartamento', 'confirm' => '¿Seguro que deseas borrar esto?', $d['id']), 'danger', 'fa-eraser');

	unset($departamentos[$k]['id']);

}
?>
<div class="row">
	<div class="col-md-12">
		<?php echo $this -> Html -> dataTable($departamentos); ?>
	</div>
</div>