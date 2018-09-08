
    <div class="row">

	<div class="col-md-9">
		<?php
			$this -> Form -> form_types [ 'selected' ] = 'upload';
			echo $this -> Form -> create ( "Backend.administradores" );

			if( $this -> Security -> hasPermission('administradores_root') ):
				echo $this -> Form -> input("_.roles", array(
                    'options' => $departamentos,  
                    'multiple' => true, 
                    'label' => i("Roles de administrador"). ' *'
                ));
			endif;

			echo $this -> Form -> input ( "id", array ( ) );
			echo $this -> Form -> input ( "nombres", array ( 
                'label' => i('Nombre'), 
                'required' => true, 'attr' =>array('maxlength' => 30)) );
			echo $this -> Form -> input ( "email", array ( 
                'label' => i('Correo electrónico'), 
                'attr' =>array( 
                    'title' => 'correo@empresa.com', 
                    'pattern' => '([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}')
            ) );
			echo $this -> Form -> input ( "password", array (
				'label' => i('Contraseña'),
				'type' => 'password', 
				'required' => false
			) );

            echo $this -> Form -> input ( "telefono", array (  
                'label' => i('Teléfono'), 
                'attr' => array('maxlength' => 25, 'pattern' => '([0-9]|[\(]|[\)]|[ ]|[\-])*', 'title' => '(55) 1234-1234')) );
			echo $this -> Form -> input ( "movil", array (  
                'label' => i('Móvil'), 
                'attr' => array('maxlength' => 25, 'pattern' => '([0-9]|[\(]|[\)]|[ ]|[\-])*', 'title' => '(55) 1234-1234')) );
			
			echo $this -> Form -> input ( "_.imagen", array (
				'label' => i('Imágen de perfil'),
				'type' => 'file',
				'attr' => array('accept' => 'image/png,image/jpg,image/jpeg')
			) );

			echo $this -> Form -> end ( i("Actualizar mi perfil"), array ( ) );

		?>
	</div>

	<div class="col-md-3">
		<?php echo $this -> Html -> img ( array (
			'controller' => 'img',
			'action' => 'render',
			$this -> Session -> user ( 'imagenes_id' )
		), array (
			'class' => 'img-thumbnail',
			'width' => '100%'
		) );
		?>
		<p class="text-center">
			<span class="entypo-user"></span>
			<?php echo $this -> Session -> user ( 'nombres' ); ?>
		</p>
	</div>

</div>
