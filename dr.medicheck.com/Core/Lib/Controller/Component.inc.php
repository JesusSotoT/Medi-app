<?php

	/**
	 * Component
	 *
	 * Extiende la funcionalidad de los componentes a los controladores
	 *
	 * @Author Daniel Lepe 2014
	 */
	class Component extends Controller {

		public $var;

		function __construct ( ) {
			parent::__construct ( );
			
			// Carga local de modelos
			if ( isset ( $this -> components ) and is_array ( $this -> components ) ) {
				foreach ( $this -> components as $component ) {
					$local = APP::path ('Controllers', array('Components')).ucfirst(strtolower($component)).'Component.php';
					if(file_exists($local)){
						$this -> load ( $component, 'Controllers', array('Components'), 'Component' );
					} else {
						$this -> load ( $component, 'Lib', array('Controller', 'Components'), 'Component' );
					}

				}
			}
		}

	}
