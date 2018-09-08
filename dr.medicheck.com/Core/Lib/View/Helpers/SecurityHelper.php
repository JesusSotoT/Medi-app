<?php

	class SecurityHelper extends Helper {

		public function has_permission ( $key ) {
			$this -> checkComponent ( );

			if ( isset ( $this -> Security -> permisos [ $key ] ) )
				return $this -> Security -> permisos [ $key ];

			die ( "Permiso '$key' no encontrado" );
		}


		public function hasPermission ( $key ) {
			return $this -> has_permission ( $key );
		}


		public function checkComponent ( ) {
			if ( !isset ( $this -> Security -> permisos ) )
				die ( 'El helper Security requiere el componente Security, habilitalo para poder usarlo.' );
		}


	}
