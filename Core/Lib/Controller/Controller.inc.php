<?php

	/**
	 * Controller
	 *
	 * Clase abstracta para todos los controladores, autocarga modelos.
	 *
	 * @author Daniel Lepe 2014
	 * @version 1.0
	 * */

	abstract class Controller extends AppConfig {

		// Configuración para VIEW
		public $view = null;
		public $template = null;
		public $data = array();
		public $plugins = null;

		// En SET, se almacenará un Array
		// con todas las claves que se van a mandar a las vistas.
		public $set = array ( );
		public $templateSet = array ( );
		public $response = array();

		/**
		 * Constructor
		 *
		 * Carga los modelos y plugins de un controlador.
		 * 
		 * @author Daniel Lepe 2014
		 * @version 2.0
		 * */

		public function __construct ( ) {

			// Carga local de sesión
			$this -> load ( 'Session', 'Lib' );

			// Carga local de modelos
			if ( isset ( $this -> models ) and is_array ( $this -> models ) ) {
				foreach ( $this -> models as $model ) {
					$local = APP::path ('Models').ucfirst(strtolower($model)).'Model.php';
					if(file_exists($local)){
						$this -> load ( $model, 'Models', array(), 'Model' );
					} else {
						$this -> load ( $model, 'Lib', array('Model'), 'Model' );
					}

				}
			}
			
			// Carga local de plugins
			if ( is_array ( $this -> plugins ) and !empty ( $this -> plugins ) ) {
				foreach ( $this -> plugins as $plugin ) {
					$local = APP::path ('Plugins', ucfirst(strtolower($plugin))).ucfirst(strtolower($plugin)).'Plugin.php';
					$this -> load ( ucfirst(strtolower($plugin)), 'Plugins', array(ucfirst(strtolower($plugin))), 'Plugin' );
				}
			}

		}


		/**
		 *
		 * Set (Funcion)
		 *
		 * Establece una variable para ser enviada a la vista.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 * */

		public function set ( $var, $alternate = null ) {
			if ( is_array ( $var ) ) {
				$this -> set += $var;
			} elseif ( !is_null ( $var ) ) {
				$this -> set [ $var ] = $alternate;
			}
		}

		/**
		 *
		 * Set (Funcion)
		 *
		 * Establece una variable para ser enviada a la vista.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 * */

		public function setTemplate ( $var, $alternate = null ) {
			if ( is_array ( $var ) ) {
				$this -> templateSet += $var;
			} elseif ( !is_null ( $var ) ) {
				$this -> templateSet [ $var ] = $alternate;
			}
		}

		/**
		 * Json
		 *
		 * Desde cualquier controlador se puede generar un Json, útil para generado de APIS
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function json ( $aData, $debug = false ) {
			if ( $debug ) {
				echo "<pre>";
				print_r($aData);
				echo "</pre>";
			} else {
				if ( is_null ( $aData ) or !is_array ( $aData ) )
					$this -> set_404 ( );

				if ( !headers_sent ( ) )
					header ( 'Content-type: application/json' );

				echo json_encode ( $aData );
			}
			die();

		}

		/**
		 * responseMerge
		 *
		 * Alias de response_merge
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function responseMerge ($modelsToMerge) {
			$this -> response_merge ($modelsToMerge);
		}
		/**
		 * response_merge
		 *
		 * Unifica las respuestas de varios Modelos, lo único que se le debe 
		 * pasar es el nombre de los modelos que se desea unificar.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function response_merge ($modelsToMerge) {
			// INIT {
			$status = true;
			$class = null;
			$msg = null;
			$validationStack = array();
			// }
			// REVISIÓN DE RESPUESTAS {
			$this -> model_response_check ($modelsToMerge);
			// }
			// LOOP MERGE {
			foreach($modelsToMerge as $mdl){
				if(!$this -> {$mdl} -> response['status']){
					$status = false;
					$msg = $this -> {$mdl} -> response['msg'];
					$class = $this -> {$mdl} -> response['class'];
					if(isset($this -> {$mdl} -> response['validation'])){
						foreach($this -> {$mdl} -> response['validation'] as $tblName => $validation){
							$validationStack[$tblName] = $validation;
						}
					}
				}
			}
			$this -> response['status'] = $status;
			$this -> response['class']	= $class;
			$this -> response['msg'] = $msg;
			$this -> response['validation'] = $validationStack;
			// }
		}
		/**
		 * model_response_check
		 *
		 * Recifica los modelos y sus respuestas, además extrae las validaciones a stack.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		private function model_response_check ($modelsToCheck) {
			foreach($modelsToCheck as $model){
				if(!isset($this -> {$model})) die("Modelo inexistente: {$model}");
				if(!isset($this -> {$model} -> response)) die("Respuesta en modelo inexistente: {$model}");
			}
		}
	}
