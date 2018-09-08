<?php
	require_once "MySqlDb.inc.php";
	require_once "ValidationKit.inc.php";

	abstract class Model extends MySqlDb {
		// DEFINITIONS {
		protected static $inst = null;	
		private $ValidationKit =  null;
		public $schemas;
		public $validation = array();
		public $id = null;				// Depósito común de IDs.
		public $status = null;			// Depósito común de estatus.
		public $response = array(
			'msg' 		=> null,		// Algun mensaje específico.
			'status' 	=> null, 		
			'class'		=> null			// success, fail.
		);
		// }
        
		// INITS {
        
		/**
		 * protected to prevent clonning
		 **/
		protected function __clone ( ) { }
        
		/**
		 * Constructor
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.1
		 */
        
		public function __construct ( ) {
			// Crea conexiòn.
			$this -> create_connection ( );
            
			// Carga los comportamientos si los tiene
			$this -> loadBehaviors ( );
            
            // Carga datos de paginado
            $this -> load_pagination_details();
            
			// Inicializa Estáticas del Model
			if ( isset ( $this -> tables ) and !empty ( $this -> tables ) and !isset ( $this -> schemas ) ){
				if( $this -> retrieveModelSchemas ( ) ) {
					// Carga el kit de validado
					$this -> load_validation_kit();
				}
			}
		}
        
        /**
         * Load Pagination Details
         * 
         * Carga los datos de paginado real.
         *
         * @Author Daniel Lepe 2015
         * @Version 1.0
         */
        protected function load_pagination_details(){
            if(is_numeric(Routes::$pagination['page'])){
                $this -> paginationCFG['current_page'] = Routes::$pagination['page'];
            }
            // breakpoint($this -> paginationCFG['current_page']);
        }
        
		/**
		 * Llama a este método para obtener una instancia
		 **/
		public static function getInstance ( ) {
			if ( self::$inst === null ) {
				self::$inst = new self ( );
			}
			return NDb::$inst;
		}
        
		/**
		 * create_connection
		 *
		 * Ahora con manejo de errores.
		 * 
		 * @Author Daniel Lepe 2014
		 * @Version 1.2
		 */
		protected function create_connection ( ) {
			if ( isset ( $this -> connectionCfg ) && isset ( $this -> defaultConnection ) ) {
				try {
					parent::__construct ( $this -> connectionCfg [ $this -> defaultConnection ] [ 'host' ], $this -> connectionCfg [ $this -> defaultConnection ] [ 'user' ], $this -> connectionCfg [ $this -> defaultConnection ] [ 'password' ], $this -> connectionCfg [ $this -> defaultConnection ] [ 'name' ] );	
				} catch (Exception $e){
					 echo '<pre>Caught exception: ',  $e->getMessage(), "\n</pre>";
				}
			} else {
				die ( "Faltan parametros de conexión" );
			}
		}
		// }
        
        /**
         * reset_connection
         * 
         * Permite cambiar dinámicamente de modelo de datos
         *
         * Requiere que se le pase la configuración que se desea importar.
         *
         * @Author Daniel Lepe 2014
         * @Version 1.0
         * @Date 03/09/2015
         */
        public function reset_connection ( $connection = null ) {
            // VALIDATE
            if(!isset($this -> connectionCfg[$connection]))
                die('La conexión solicitada no existe');
            
            // CLOSE CURRENT CONNECTION
            $this -> close();
            
            // UPDATE CONNECITON
            $this -> defaultConnection = $connection;
            
            // CREATE NEW CONNECTION
            $this -> create_connection();
        }
        
		// ALIAS {
		/**
		 * Alias de cleanSql. Función que limpia el valor o valores para evitar inserción
		 * de sql y otros problemas
		 *
		 * @param Mixed $var Valor o Arreglo de valores a limpiar de posibles inserciones
		 * de sql
		 * @return Mixed Valor o Arreglo de valores limpio de posibles ataques
		 */
		public function no_injection ( $var ) {
			return $this -> cleanSql ( $var );
		}
		/**
		 * Alias de cleanSql. Función que limpia el valor o valores para evitar inserción
		 * de sql y otros problemas
		 *
		 * @param Mixed $var Valor o Arreglo de valores a limpiar de posibles inserciones
		 * de sql
		 * @return Mixed Valor o Arreglo de valores limpio de posibles ataques
		 */
		public function mysql_escape ( $var ) {
			return $this -> cleanSql ( $var );
		}
		/**
		 * Alias de bind. Función que inserta el o los valores dados en una consulta
		 *
		 * @param Mix $sql Consulta donde se van a insertar los valores dados
		 * @param Mix $valores Arreglo de valores a insertar en mi consulta
		 * @param Boolean $save Bandera que indica si se va a sustituir de forma segura o
		 * nó
		 * @return String La consulta ya con los valores insertados
		 */
		public function getQueryResult ( $sql, $valores, $save = true ) {
			return $this -> bind ( $sql, $valores, $save );
		}
		/**
		 * Alias de bind. Función que inserta el o los valores dados en una consulta
		 *
		 * @param Mix $sql Consulta donde se van a insertar los valores dados
		 * @param Mix $valores Arreglo de valores a insertar en mi consulta
		 * @param Boolean $save Bandera que indica si se va a sustituir de forma segura o
		 * nó
		 * @return String La consulta ya con los valores insertados
		 */
		public function mysql_bind ( $sql, $valores, $save = true ) {
			return $this -> bind ( $sql, $valores, $save );
		}
		/**
		 * Alias de insert. Inserta un registro en la base de datos.
		 *
		 * @param String $tabla Nombre de la tabla o tablas donde se va a insertar el
		 * registro.
		 * @param Array $valores Arreglo con los valor que se van a insertar.
		 * @param Boolean $safe Bandera que me indica si se van a limpiar los valores a
		 * insertar.
		 * @return Mixed Id autonumérico que se insertó (en caso de que se haya
		 * insertado), o false.
		 */
		public function db_insertar ( $tabla, $valores, $safe = true ) {
			return $this -> insert ( $tabla, $valores, $safe );
		}
		/**
		 * Alias de update. Actualiza/Edita uno o varios registros en la base de datos.
		 *
		 * @param String $tabla Nombre de la tabla o tablas donde se va a editar el(los)
		 * registro(s).
		 * @param Array $valores Arreglo con los valor que se van a actualizar.
		 * @param String $where Condición o filtro para la consulta.
		 * @param Boolean $safe Bandera que me indica si se van a limpiar los valores a
		 * actualizar.
		 * @return Boolean.
		 */
		public function db_modificar_campos ( $tabla, $valores, $where, $safe = true ) {
			return $this -> update ( $tabla, $valores, $where, $safe );
		}
		/**
		 * Alias de delete. Elimina uno o varios registros en la base de datos.
		 *
		 * @param String $tabla Nombre de la tabla o tablas de donde se van a eliminar
		 * el(los) registro(s).
		 * @param String $where Condición o filtro para la consulta.
		 * @return Boolean.
		 */
		function db_eliminar_por_restricciones ( $tabla, $where ) {
			return $this -> delete ( $tabla, $where );
		}
		/**
		 * Alias de getColumns. Entrega las columnas de la tabla pedida.
		 *
		 * @param String $tabla Nombre de la tabla
		 * @return Array con las columnas
		 */
		public function db_obtener_campos ( $tabla ) {
			return $this -> getColumns ( $tabla );
		}
		// }
        
		// VALIDATION {
			private function load_validation_kit () {
				$this -> ValidationKit = new ValidationKit();
				$this -> ValidationKit -> load_validation ($this -> validation, $this -> schemas);
				
			}
			public function validateAll ($multiTableData = array()) {
				$this -> ValidationKit -> id = $this -> id;
				$this -> ValidationKit -> validateAll ($multiTableData);
				$this -> _collect_validation_results();
				return ($this -> response['status'] === false)? false : true;
			}
			public function validateAs ($tableKey = null, $tablaData = array()) {
				$this -> ValidationKit -> id = $this -> id;
				$this -> ValidationKit -> validateAs ($tableKey, $tablaData);
				$this -> _collect_validation_results();
				return ($this -> response['status'] === false)? false : true;
			}
			public function validateFieldAs ($fieldKey = null, $data = array()) {
				$this -> ValidationKit -> id = $this -> id;
				$this -> ValidationKit -> validateFieldAs ($fieldKey, $data);
				$this -> _collect_validation_results();
				return ($this -> response['status'] === false)? false : true;
			}
			private function _collect_validation_results () {
				$details = $this -> ValidationKit -> get_results();
				$status = true;
				foreach ( $details as $k => $v) {
					if($v['status'])
						unset($details[$k]);
					if($status and !$v['status'])
						$status = false;
				}
				if(!$status){
					$this -> response['status'] = false;
					$this -> response['msg'] = 'Hay un error en la captura de datos.';
					$this -> response['class'] = 'warning';
					$this -> response['validation'] = $details;
				}
			}
		// }
        
		// MVC LITE ESPECIFICS {
			/**
			 * Load Behaviors
			 *
			 * Carga los comportamientos incluidos en un modelo en especìfico.
			 *
			 * Ahora carga directamente desde los behaviors locales.
			 *
			 * @Author Daniel Lepe
			 * @Version 1.1
			 */
			protected function loadBehaviors ( ) {
				if ( isset ( $this -> behaviors ) and is_array ( $this -> behaviors ) ) {
					foreach ( $this -> behaviors as $behaviors ) {
						$local = APP::path ( 'Models', array('Behaviors') ) . ucfirst ( strtolower ( $behaviors ) ) . 'Behavior.php';
						if ( file_exists ( $local ) ) {
							$this -> load ( $behaviors, 'Models', array ( 'Behaviors' ), 'Behavior' );
						} else {
							$this -> load ( $behaviors, 'Lib', array (
								'Model',
								'Behaviors'
							), 'Behavior' );
						}
					}
				}
			}
			/**
			 * Retrieve Model Schemas
			 *
			 * Obtiene los valores de las estàticas del modelo, como las descripciones de las
			 * tablas y sus respectivas columnas.
			 *
			 * @Author Daniel Lepe 2015
			 * @Version 1.0
			 */
			protected function retrieveModelSchemas ( ) {
					$returningData = array ( );
					foreach ( $this -> tables as $table ) {
						$returningData [ $table ] = array ( );
						$data = $this -> getAllRows ( array ( 'sql' => sprintf ( "DESC %s;", $table ) ) );
						foreach ( $data as $k ) {
							$returningData [ $table ] [ $k [ 'Field' ] ] = array (
								'Type' => $k [ 'Type' ],
								'Null' => ($k [ 'Null' ] == 'NO') ? true : false,
								'Default' => $k [ 'Default' ],
								'Extra' => $k [ 'Extra' ],
								'Field' => $k [ 'Field' ]
							);
						}
					}
					$this -> schemas = $returningData;
					return true;
				}
		// }
        
	}