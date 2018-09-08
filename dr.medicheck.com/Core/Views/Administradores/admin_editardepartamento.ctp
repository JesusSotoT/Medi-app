<?php
	$this -> Html -> addCrumb('Configuraciones');
	$this -> Html -> addCrumb ( 'Departamentos', array (
		'controller' => 'administradores',
		'action' => 'departamentos'
	) );
	$this -> Html -> addCrumb('Editar');
?>
<div class="row">

	<div class="col-md-12">
		<?php
			echo $this -> Form -> scaffold ( "Backend.departamentos" );

		?>
	</div>

	<div class="col-md-3">

	</div>

</div>