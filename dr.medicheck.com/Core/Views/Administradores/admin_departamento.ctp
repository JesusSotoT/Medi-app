<?php

	$this -> Menu -> addAction ( 'Regresar', 'entypo-back', array (
		'action' => 'departamentos',
		'controller' => 'administradores'
	) );

?>
<div class="row">

	<div class="col-md-12">
		<?php
			echo $this -> Form -> scaffold ( "Backend.departamentos" );
		?>
	</div>

</div>