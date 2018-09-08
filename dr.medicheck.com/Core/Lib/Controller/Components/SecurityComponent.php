<?php
    
	class SecurityComponent extends Component {

		public $models = array ( 'Backend' );

		public $permisos_usuario = NULL;

		public function build_permissions ( ) {

			if(!$this -> Session -> is_logged())
				return false;

			$this -> permisos_usuario = $this -> Backend -> permisos_for_execution ( 
                $this -> Session -> user ( 'id' ),
                $this -> Session -> user ( MultidepartmentComponent::$roleFiledIDInUserSession )
            );

			$permisos = array ( );

			foreach ( $this -> permisos_usuario as $p ) {
				$permisos [ $p [ 'clave' ] ] = $p [ 'permiso' ];
			}

			$this -> permisos_usuario = $permisos;
			$this -> set('permisos', $this -> permisos_usuario);
		}

		public function has_permission ( $key ) {
			if(is_null($this -> permisos_usuario))
				$this -> build_permissions();

			if(isset($this -> permisos_usuario[$key]))
				return $this -> permisos_usuario[$key];

			die("Permiso '$key' no encontrado");
		}

		public function hasPermission($key){
			return $this -> has_permission($key);
		}

	}
