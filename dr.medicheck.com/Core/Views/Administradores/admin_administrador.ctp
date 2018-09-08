<?php
	$this -> Menu -> addAction ( 'Regresar', 'entypo-back', array (
		'action' => 'administradores',
		'controller' => 'administradores'
	) );
?>
<div class="row">

	<div class="col-md-9">
		<?php
			$this -> Form -> form_types [ 'selected' ] = 'upload';
			echo $this -> Form -> create ( "Backend.administradores", array('id' => 'BackendAdministradorForm') );
			echo $this -> Form -> input ( "id", array ( ) );
			echo $this -> Form -> input ( "nombres", array ( 'label' => 'Nombre', 'required' => true, 'attr' =>array('maxlength' => 30)) );
			echo $this -> Form -> input ( "email", array ( 'attr' =>array( 'title' => 'correo@empresa.com', 'pattern' => '([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}') ) );

			if ( !isset ( $this -> data ['administradores'][ 'id' ] ) ) {
				echo $this -> Form -> input ( "password", array (
					'value' => '',
					'label' => 'Contraseña',
					'required' => true, 'attr' => array('minlength' => 6)
				) );
			} else {
				echo $this -> Form -> input ( "password", array ( 'value' => '', 'label' => 'Contraseña', 'required' => false ) );
			}

			echo $this -> Form -> input ( "telefono", array (  'label' => 'Teléfono', 'attr' => array('maxlength' => 25, 'pattern' => '([0-9]|[\(]|[\)]|[ ]|[\-])*', 'title' => '(55) 1234-1234')) );
			echo $this -> Form -> input ( "movil", array (  'label' => 'Móvil', 'attr' => array('maxlength' => 25, 'pattern' => '([0-9]|[\(]|[\)]|[ ]|[\-])*', 'title' => '(55) 1234-1234')) );
			echo $this -> Form -> input ( "_.imagen", array (
				'type' => 'file',
				'attr' => array('accept' => 'image/png,image/jpg,image/jpeg'),
				'label' => 'Imagen de perfil'
			) );
            echo $this -> Form -> input("_.roles", array('options' => $departamentos,  'multiple' => true, 'label' => 'Roles de administrador *'));
			echo $this -> Form -> input ( "bloquear_acceso", array ( 'required' => false ) );
			echo $this -> Form -> end ( "Actualizar usuario", array ( ) );
		?>
	</div>
   


	<?php if(!empty($this -> data)):?>
	<div class="col-md-3">
		<?php echo $this -> Html -> img ( array (
			'controller' => 'img',
			'action' => 'render',
			$this -> data [ 'administradores' ] [ 'imagenes_id' ]
		), array (
			'class' => 'img-thumbnail',
			'width' => '100%'
		) );
		?>
		<p class="text-center">
			<span class="entypo-user"></span>
			<?php echo $this -> data [ 'administradores' ] [ 'nombres' ]; ?>
		</p>

	</div>
	<?php endif ?>
</div>
<script>
    $('#BackendAdministradorForm').mvclite();
    $('#BackendAdministradorForm').on('success', function(){
        $.ajax({
            url: "<?php echo $this -> referer() ?>", 
            success: function (data){
                $('#action').html(data);
            }
        });
    });
</script>