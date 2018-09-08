<?php

	/**
	 * Provee conexión a MySql
	 *
	 * @author Arturo Ruvalcaba, <arturo@sustam.com>
	 * @author Daniel Lepe 2014
	 * @version 1.2
	 */

	class MySqlDb extends ModelConfig {

		/**
		 * Variable que almacena la dirección de la base de datos.
		 *
		 *
		 * @var String
		 * @access private
		 */
		private $host = '';
		/**
		 * Variable que almacena el nombre de usuario de la base de datos.
		 *
		 * @var String
		 * @access private
		 */
		private $user = '';
		/**
		 * Variable que almacena el password de la base de datos.
		 *
		 * @var String
		 * @access private
		 */
		private $password = '';
		/**
		 * Variable que almacena el nombre de la base de datos.
		 *
		 * @var String
		 * @access private
		 */
		private $database = '';
		/**
		 * Variable que almacena información sobre el resultado de una consulta de
		 * selección.
		 *
		 * @var Array
		 * @access private
		 */
		private $result_info = array ( );
		/**
		 * Variable que almacena información sobre los tipos de datos que soporta una
		 * columna.
		 *
		 * @var Array
		 * @access private
		 */
		private $column_type = array (
			0 => 'DECIMAL',
			1 => 'TINYINT',
			2 => 'SMALLINT',
			3 => 'INTEGER',
			4 => 'FLOAT',
			5 => 'DOUBLE',
			7 => 'TIMESTAMP',
			8 => 'BIGINT',
			9 => 'MEDIUMINT',
			10 => 'DATE',
			11 => 'TIME',
			12 => 'DATETIME',
			13 => 'YEAR',
			14 => 'DATE',
			16 => 'BIT',
			246 => 'DECIMAL',
			247 => 'ENUM',
			248 => 'SET',
			249 => 'TINYBLOB',
			250 => 'MEDIUMBLOB',
			251 => 'LONGBLOB',
			252 => 'BLOB',
			253 => 'VARCHAR',
			254 => 'CHAR',
			255 => 'GEOMETRY'
		);
		/**
		 * Variable que contiene el último id manipulado.
		 *
		 * @var Int
		 * @access private
		 */
		private $insert_id = null;
		/**
		 * Variable que almacena el índice
		 * @var Mixed
		 * @access private
		 */
		private $indice = false;

		/**
		 * Variable que almacena la conexión a la base de datos.
		 *
		 * @var Mixed
		 * @access public
		 */
		public $connection = false;
		/**
		 * Variable bandera que indica si se pudo encontrar información sobre el
		 * resultado de una consulta de selección.
		 *
		 * @var Boolean
		 * @access public
		 */
		public $loaded_result_info = false;
		/**
		 * Variable bandera que indica el tipo de conección que se estableció con la base
		 * de datos (mysqlI o normal).
		 *
		 * @var Boolean
		 * @access public
		 */
		public $mySqlI;
		/**
		 * Variable bandera que indica si se logró establecer la conexión
		 *
		 * @var Boolean
		 * @access public
		 */
		public $connected = false;
		/**
		 * Variable bandera que indica si se va a bloquear el autocommit
		 *
		 * @var Boolean
		 * @access public
		 */
		public $autocommit = true;

		/**
		 * Variable que almacena las consultas por su nombre y con índice numérico.
		 *
		 * @var Array
		 * @access public
		 */
		public $querys = array ( );
		/**
		 * Variable que almacena las palabras a limpiar para los valores a insertar en
		 * una consulta
		 *
		 * @var Array
		 * @access public
		 */
		public $basura = array (
			"'",
			'"',
			'SELECT',
			'INSERT',
			'UPDATE',
			'DELETE',
			'ALTER',
			'DROP',
			'TRUNCATE'
		);

		/**
		 * Función constructor de la clase
		 *
		 * @param String $host Dirección donde está la base de datos
		 * @param String $user Nombre de usuario de la base de datos
		 * @param String $password Password de la base de datos
		 * @param String $database Nombre de la base de datos
		 */
		public function __construct ( $host, $user, $password, $database, $querys = false ) {
			if ( function_exists ( 'mysqli_init' ) && is_callable ( 'mysqli_init' ) && (@$this -> connection = mysqli_init ( )) ) {

				$this -> mySqlI = true;

				$this -> connection -> options ( MYSQLI_OPT_CONNECT_TIMEOUT, 5 );
				if ( !@$this -> connection -> real_connect ( $host, $user, $password, $database ) ) {
					$this -> response['msg'] = $this -> getErrorMsg ( ) ;
					$this -> response['status'] = FALSE; 
					$this -> response['class'] = 'error';
					throw new Exception( $this -> getErrorMsg ( ) );
					@$this -> connection -> close ( );
					$this -> connection = false;
					throw new Exception("Error en la conexi&oacute;n de base de datos MYSQLI" . PHP_EOL, 1);
				} else {
					$sql = "SET CHARACTER SET 'utf8'";
					$this -> executeQuery ( $sql );
					$sql = "SET NAMES 'utf8'";
					$this -> executeQuery ( $sql );
					$this -> connected = true;
				}
			} else {

				$this -> mySqlI = false;

				if ( !($this -> connection = mysql_connect ( $host, $user, $password )) ) {
					$this -> response['msg'] = $this -> getErrorMsg ( ) ;
					$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
					throw new Exception("Error en la conexi&oacute;n de base de datos MYSQL" . PHP_EOL, 1);
				} elseif ( !mysql_select_db ( $database, $this -> connection ) ) {
					$this -> response['msg'] = $this -> getErrorMsg ( ) ;
					$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
					mysql_close ( $this -> connection );
					throw new Exception("Error en la conexi&oacute;n de base de datos MYSQL" . PHP_EOL, 1);
					$this -> connection = false;
				} else {
					$sql = "SET CHARACTER SET 'utf8'";
					$this -> executeQuery ( $sql );
					$sql = "SET NAMES 'utf8'";
					$this -> executeQuery ( $sql );
					$this -> connected = true;
				}
			}

			$this -> host = $host;
			$this -> user = $user;
			$this -> password = $password;
			$this -> database = $database;

			//si $querys es array
			if ( is_array ( $querys ) ) {
				//asigno las consultas a mi variable local
				$this -> querys = $querys;
				//-----------------------------------------

				//genero las consultas con índice numérico
				$x = 0;
				foreach ( $this->querys as $qry ) {
					$this -> querys{$x} = $qry;
					$x++;
				}
				//-----------------------------------------
			}
			//-----------------------------------------
		}


		/**
		 * Función destructor de la clase
		 */
		/*public function __destruct() {
		 if ( $this->mySqlI ) {
		 @$this->connection->close();
		 }
		 else {
		 mysql_close($this->connection);
		 }

		 $this->connection = false;
		 }*/

		/**
		 * Función que limpia el valor o valores para evitar inserción de sql y otros
		 * problemas
		 *
		 * @param Mixed $var Valor o Arreglo de valores a limpiar de posibles inserciones
		 * de sql
		 * @return Mixed Valor o Arreglo de valores limpio de posibles ataques
		 */
		public function cleanSql ( $var ) {
			if ( is_array ( $var ) ) {
				foreach ( $var as $key => $value ) {
					$var{$key} = $this -> cleanSql ( $value );
				}
			} elseif ( $var === NULL || $var == 'NULL' ) {
				$var = 'NULL';
			} else {
				if ( get_magic_quotes_gpc ( ) ) {
					$var = stripslashes ( $var );
				}

				if ( !is_numeric ( $var ) ) {
					if ( $this -> mySqlI ) {
						$var = "'" . $this -> connection -> real_escape_string ( $var ) . "'";
					} else {
						$var = "'" . mysql_real_escape_string ( $var, $this -> connection ) . "'";
					}
				} else {
					$var = "'" . $var . "'";
				}
			}

			return $var;
		}


		/**
		 * Función que devuelve la consulta pedida de mi listado de consultas, o el valor
		 * de $sql.
		 *
		 * @param Mix $sql Consulta a buscar en mi listado de consultas
		 * @return Mix La consulta real
		 */
		public function getQuery ( $sql ) {
			if ( isset ( $this -> querys{$sql} ) ) {
				$this -> indice = $sql;
				$sql = $this -> querys{$sql};
			}

			return $sql;
		}


		/**
		 * Función que inserta el o los valores dados en una consulta
		 *
		 * @param Mix $sql Consulta donde se van a insertar los valores dados
		 * @param Mix $valores Arreglo de valores a insertar en mi consulta
		 * @param Boolean $save Bandera que indica si se va a sustituir de forma segura o
		 * nó
		 * @return String La consulta ya con los valores insertados
		 */
		public function bind ( $sql, $valores, $save = true ) {
			$sql = $this -> getQuery ( $sql );
			$valores = ($save) ? $this -> cleanSql ( $valores ) : $valores;

			foreach ( $valores as $name => $val ) {
				$sql = str_replace ( ":$name", $val, $sql );
			}

			return $sql;
		}


		/**
		 * Función que inicia una transacción.
		 *
		 * @return Boolean.
		 */
		public function begin ( ) {
			//mySQLI
			if ( $this -> mySqlI ) {
				if ( !$this -> connection -> autocommit ( false ) ) {
					return false;
				}
				$this -> autocommit = false;
				return true;
			}

			//normal
			else {
				if ( !@mysql_query ( "BEGIN;", $this -> connection ) ) {
					return false;
				}
				$this -> autocommit = false;
				return true;
			}
		}


		/**
		 * Función que finaliza una transacción.
		 *
		 * @return Boolean.
		 */
		public function commit ( ) {
			//mySQLI
			if ( $this -> mySqlI ) {
				if ( !@$this -> connection -> commit ( ) ) {
					return false;
				}
				if ( @$this -> connection -> autocommit ( true ) ) {
					$this -> autocommit = true;
				}
				return true;
			}

			//normal
			else {
				if ( !@mysql_query ( "COMMIT;", $this -> connection ) ) {
					return false;
				}
				$this -> autocommit = true;
				return true;
			}
		}


		/**
		 * Función que regresa al estado anterior.
		 *
		 * @return Boolean.
		 */
		public function rollback ( ) {
			//mySQLI
			if ( $this -> mySqlI ) {
				if ( !@$this -> connection -> rollback ( ) ) {
					return false;
				}
				if ( @$this -> connection -> autocommit ( true ) ) {
					$this -> autocommit = true;
				}
				return true;
			}

			//normal
			else {
				if ( !@mysql_query ( "ROLLBACK;", $this -> connection ) ) {
					return false;
				}
				$this -> autocommit = true;
				return true;
			}
		}


		/**
		 * Solo ejecuta una consulta.
		 *
		 * @param String $sql
		 * @return Boolean.
		 **/
		public function executeQuery ( $sql ) {
			//mySQLI
			if ( $this -> mySqlI ) {
				//activado el autocommit
				if ( $this -> autocommit == true ) {
					$this -> connection -> autocommit ( false );
					//bloqueo el autocommit

					if ( ($result = $this -> connection -> query ( $sql )) ) {
						$this -> insert_id = $this -> connection -> insert_id;
						$this -> connection -> commit ( );
						//commit
						$this -> connection -> autocommit ( true );
						//desbloqueo el autocommit
						return $result;
					} else {
						$this -> connection -> rollback ( );
						//regreso al estado anterior
						$this -> connection -> autocommit ( true );
						//desbloqueo el autocommit

						$this -> response['msg'] = $this -> getErrorMsg ( ) ;
						$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
						$this -> response['sql'] = $sql;

						return false;
					}
				}

				//desactivado el autocommit
				else {
					if ( ($result = @$this -> connection -> query ( $sql )) ) {
						$this -> insert_id = $this -> connection -> insert_id;
						return $result;
					} else {
						$this -> response['msg'] = $this -> getErrorMsg ( ) ;
						$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
						$this -> response['sql'] = $sql;
						return false;
					}
				}
			}

			//normal
			else {
				//activado el autocommit
				if ( $this -> autocommit == true ) {
					@mysql_query ( "BEGIN;", $this -> connection );

					if ( ($result = @mysql_query ( $sql, $this -> connection )) ) {
						$this -> insert_id = mysql_insert_id ( $this -> connection );
						@mysql_query ( "COMMIT;", $this -> connection );
						//commit
						return $result;
					} else {
						@mysql_query ( "ROLLBACK;", $this -> connection );
						//regreso al estado anterior

						$this -> response['msg'] = $this -> getErrorMsg ( ) ;
						$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
						$this -> response['sql'] = $sql;

						return false;
					}
				}

				//desactivado el autocommit
				else {
					if ( ($result = @mysql_query ( $sql, $this -> connection )) ) {
						$this -> insert_id = mysql_insert_id ( $this -> connection );
						return $result;
					} else {
						
						$this -> response['msg'] = $this -> getErrorMsg ( ) ;
						$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
						$this -> response['sql'] = $sql;

						return false;
					}
				}
			}
		}
        
		/**
		 * Obtiene todas las filas de una consulta. Usarlo con cuidado con tablas de
		 * resultados largas.
		 *
		 * Los parámetros que puede recibir son:
		 * 1). from: Nombre de la tabla o tablas.
		 * 2). where: Parámetros o filtro de la consulta.
		 * 3). values: Valores a Buscar.
		 * 4). group_by: Columna por la cual se va a agrupar la info.
		 * 5). order_by: Forma en la que se va a ordenar la info.
		 * 6). limit: Limite que tendrá la consulta.
		 * 7). get_query: Bandera que indica si quieres ejecutar la consultar u obtener
		 * la consulta que se ejecutaría.
		 * 8). sql: Consulta a ejecutar.
		 * 9). valores: Arreglo de valores para la consulta.
		 * 10). not_safe: Bandera que indica si quieres o no limpiar los parámetros de la
		 * consulta.
		 *
		 * @param String $sql La consulta a ejecutar
		 * @return Array una matriz multidimencional asociativa
		 */
		public function getAllRows ( $sql ) {
			$parametros = array (
				'from',
				'where',
				'values',
				'group_by',
				'order_by',
				'limit',
				'get_query',
				'sql',
				'valores',
				'not_safe'
			);
			$_V = $this -> getParams ( func_get_args ( ), $parametros );
			//Me pasan la consulta
			if ( isset ( $_V{'sql'} ) ) {
				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $_V{'sql'}, $_V{'valores'}, $safe );
				} else {
					$sql = $this -> getQuery ( $_V{'sql'} );
				}
			}
			//FIN Me pasan la consulta

			//Genero la consulta
			else {
				$values = ( isset ( $_V{'values'} )) ? $_V{'values'} : '*';
				$sql = "SELECT $values FROM {$_V{'from'}} ";
				$sql .= ( isset ( $_V{'where'} )) ? " WHERE {$_V{'where'}} " : '';
				$sql .= ( isset ( $_V{'group_by'} )) ? " GROUP BY {$_V{'group_by'}} " : '';
				$sql .= ( isset ( $_V{'order_by'} )) ? " ORDER BY {$_V{'order_by'}} " : '';
				$sql .= ( isset ( $_V{'limit'} )) ? " LIMIT {$_V{'limit'}} " : '';

				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $sql, $_V{'valores'}, $safe );
				}
			}
			//FIN Genero la consulta

			//Regreso el sql formado
			if ( isset ( $_V{'get_query'} ) ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			//mySQLI
			if ( $this -> mySqlI ) {
				if ( $results = @$this -> connection -> query ( $sql ) ) {
					$this -> setResultInfo ( $results );
					$rows = array ( );
					while ( $fields = $results -> fetch_assoc ( ) ) {
						$rows [ ] = $fields;
					}
					$results -> close ( );
					return $rows;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}

			//normal
			else {
				if ( $results = @mysql_query ( $sql, $this -> connection ) ) {
					$this -> setResultInfo ( $results );
					$rows = array ( );
					while ( $fields = mysql_fetch_array ( $results, MYSQL_ASSOC ) ) {
						$rows [ ] = $fields;
					}
					mysql_free_result ( $results );
					return $rows;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}
		}


		/**
		 * Obtiene la primera fila de una consulta ($rows[0]),
		 * esto significa que regresa una fila única: 'LIMIT 1'.
		 *
		 * Los parámetros que puede recibir son:
		 * 1). from: Nombre de la tabla o tablas.
		 * 2). where: Parámetros o filtro de la consulta.
		 * 3). values: Valores a Buscar.
		 * 4). group_by: Columna por la cual se va a agrupar la info.
		 * 5). order_by: Forma en la que se va a ordenar la info.
		 * 6). get_query: Bandera que indica si quieres ejecutar la consultar u obtener
		 * la consulta que se ejecutaría.
		 * 7). sql: Consulta a ejecutar.
		 * 8). valores: Arreglo de valores para la consulta.
		 * 9). not_safe: Bandera que indica si quieres o no limpiar los parámetros de la
		 * consulta.
		 *
		 * @param String $sql La consulta a ejecutar con 'LIMIT 1'
		 * @return Array La primera fila de la consulta
		 **/
		public function getOneRow ( $sql ) {
			$parametros = array (
				'from',
				'where',
				'values',
				'group_by',
				'order_by',
				'get_query',
				'sql',
				'valores',
				'not_safe'
			);
			$_V = $this -> getParams ( func_get_args ( ), $parametros );
			//Me pasan la consulta
			if ( isset ( $_V{'sql'} ) ) {
				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $_V{'sql'}, $_V{'valores'}, $safe );
				} else {
					$sql = $this -> getQuery ( $_V{'sql'} );
				}

				$sql .= (strpos ( $sql, ' LIMIT ' ) === false && strpos ( $sql, ' limit ' ) === false) ? ' LIMIT 1 ' : '';
			}
			//FIN Me pasan la consulta

			//Genero la consulta
			else {
				$values = ( isset ( $_V{'values'} )) ? $_V{'values'} : '*';
				$sql = "SELECT $values FROM {$_V{'from'}} ";
				$sql .= ( isset ( $_V{'where'} )) ? " WHERE {$_V{'where'}} " : '';
				$sql .= ( isset ( $_V{'group_by'} )) ? " GROUP BY {$_V{'group_by'}} " : '';
				$sql .= ( isset ( $_V{'order_by'} )) ? " ORDER BY {$_V{'order_by'}} " : '';
				$sql .= " LIMIT 1 ";

				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $sql, $_V{'valores'}, $safe );
				}
			}
			//FIN Genero la consulta

			//Regreso el sql formado
			if ( isset ( $_V{'get_query'} ) ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			//mySQLI
			if ( $this -> mySqlI ) {
				if ( $results = @$this -> connection -> query ( $sql ) ) {
					$this -> setResultInfo ( $results );
					if ( $fields = $results -> fetch_assoc ( ) ) {
						$results -> close ( );
						return $fields;
					}
					return false;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}

			//normal
			else {
				if ( $results = @mysql_query ( $sql, $this -> connection ) ) {
					$this -> setResultInfo ( $results );
					if ( $fields = mysql_fetch_array ( $results, MYSQL_ASSOC ) ) {
						mysql_free_result ( $results );
						return $fields;
					}
					return false;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}
		}


		/**
		 * Obtiene el primer valor de la primera fila de una consulta ($rows[0][0]),
		 * esto significa que regresa un valor único de una consulta con 'LIMIT 1' que
		 * solo
		 * tiene una fila y una columna en la tabla de resultados.
		 *
		 * Los parámetros que puede recibir son:
		 * 1). value: Valor a Buscar.
		 * 2). from: Nombre de la tabla o tablas.
		 * 3). where: Parámetros o filtro de la consulta.
		 * 4). group_by: Columna por la cual se va a agrupar la info.
		 * 5). order_by: Forma en la que se va a ordenar la info.
		 * 6). get_query: Bandera que indica si quieres ejecutar la consultar u obtener
		 * la consulta que se ejecutaría.
		 * 7). sql: Consulta a ejecutar.
		 * 8). valores: Arreglo de valores para la consulta.
		 * 9). not_safe: Bandera que indica si quieres o no limpiar los parámetros de la
		 * consulta.
		 *
		 * @param String $sql La consulta a ejecutar con 'LIMIT 1' y solo un campo
		 * @return Mixed El primer valor de la primera fila de la consulta
		 **/
		public function getOneValue ( $sql ) {
			$parametros = array (
				'value',
				'from',
				'where',
				'group_by',
				'order_by',
				'get_query',
				'sql',
				'valores',
				'not_safe'
			);
			$_V = $this -> getParams ( func_get_args ( ), $parametros );
			//Me pasan la consulta
			if ( isset ( $_V{'sql'} ) ) {
				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $_V{'sql'}, $_V{'valores'}, $safe );
				} else {
					$sql = $this -> getQuery ( $_V{'sql'} );
				}

				$sql .= (strpos ( $sql, ' LIMIT ' ) === false && strpos ( $sql, ' limit ' ) === false) ? ' LIMIT 1 ' : '';
			}
			//FIN Me pasan la consulta

			//Genero la consulta
			else {
				$sql = "SELECT {$_V{'value'}} FROM {$_V{'from'}} ";
				$sql .= ( isset ( $_V{'where'} )) ? " WHERE {$_V{'where'}} " : '';
				$sql .= ( isset ( $_V{'group_by'} )) ? " GROUP BY {$_V{'group_by'}} " : '';
				$sql .= ( isset ( $_V{'order_by'} )) ? " ORDER BY {$_V{'order_by'}} " : '';
				$sql .= " LIMIT 1 ";

				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $sql, $_V{'valores'}, $safe );
				}
			}
			//FIN Genero la consulta

			//Regreso el sql formado
			if ( isset ( $_V{'get_query'} ) ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			//mySQLI
			if ( $this -> mySqlI ) {
				if ( $results = @$this -> connection -> query ( $sql ) ) {
					$this -> setResultInfo ( $results, 1 );
					if ( $fields = $results -> fetch_assoc ( ) ) {
						$results -> close ( );
						return current ( $fields );
					}
					return false;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}

			//normal
			else {
				if ( $results = @mysql_query ( $sql, $this -> connection ) ) {
					$this -> setResultInfo ( $results, 1 );
					if ( $fields = mysql_fetch_array ( $results, MYSQL_ASSOC ) ) {
						mysql_free_result ( $results );
						return current ( $fields );
					}
					return false;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}
		}


		/**
		 * Obtiene la primera columna que pediste,
		 * esto significa que regresa un array con los valores de esta columna.
		 *
		 * Los parámetros que puede recibir son:
		 * 1). value: Valor a Buscar.
		 * 2). from: Nombre de la tabla o tablas.
		 * 3). where: Parámetros o filtro de la consulta.
		 * 4). group_by: Columna por la cual se va a agrupar la info.
		 * 5). order_by: Forma en la que se va a ordenar la info.
		 * 6). limit: Limite que tendrá la consulta.
		 * 7). get_query: Bandera que indica si quieres ejecutar la consultar u obtener
		 * la consulta que se ejecutaría.
		 * 8). sql: Consulta a ejecutar.
		 * 9). valores: Arreglo de valores para la consulta.
		 * 10). not_safe: Bandera que indica si quieres o no limpiar los parámetros de la
		 * consulta.
		 *
		 * @param String $sql Con y solo una columna
		 * @return Array Los valores de la primera columna
		 */
		public function getOneColumn ( $sql ) {
			$parametros = array (
				'value',
				'from',
				'where',
				'group_by',
				'order_by',
				'limit',
				'get_query',
				'sql',
				'valores',
				'not_safe'
			);
			$_V = $this -> getParams ( func_get_args ( ), $parametros );
			//Me pasan la consulta
			if ( isset ( $_V{'sql'} ) ) {
				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $_V{'sql'}, $_V{'valores'}, $safe );
				} else {
					$sql = $this -> getQuery ( $_V{'sql'} );
				}
			}
			//FIN Me pasan la consulta

			//Genero la consulta
			else {
				$sql = "SELECT {$_V{'value'}} FROM {$_V{'from'}} ";
				$sql .= ( isset ( $_V{'where'} )) ? " WHERE {$_V{'where'}} " : '';
				$sql .= ( isset ( $_V{'group_by'} )) ? " GROUP BY {$_V{'group_by'}} " : '';
				$sql .= ( isset ( $_V{'order_by'} )) ? " ORDER BY {$_V{'order_by'}} " : '';
				$sql .= ( isset ( $_V{'limit'} )) ? " LIMIT {$_V{'limit'}} " : '';

				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $sql, $_V{'valores'}, $safe );
				}
			}
			//FIN Genero la consulta

			//Regreso el sql formado
			if ( isset ( $_V{'get_query'} ) ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			//mySQLI
			if ( $this -> mySqlI ) {
				if ( $results = @$this -> connection -> query ( $sql ) ) {
					$this -> setResultInfo ( $results, 1 );
					$rows = array ( );
					while ( $fields = $results -> fetch_assoc ( ) ) {
						$rows [ ] = current ( $fields );
					}
					$results -> close ( );
					return $rows;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}

			//normal
			else {
				if ( $results = @mysql_query ( $sql, $this -> connection ) ) {
					$this -> setResultInfo ( $results, 1 );
					$rows = array ( );
					while ( $fields = mysql_fetch_array ( $results, MYSQL_ASSOC ) ) {
						$rows [ ] = current ( $fields );
					}
					mysql_free_result ( $results );
					return $rows;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}
		}


		/**
		 * Obtiene un arreglo en la forma de pareja 'llave-valor' compuesto por el primer
		 * y segundo campo de una consulta.
		 *
		 * Los parámetros que puede recibir son:
		 * 1). from: Nombre de la tabla o tablas.
		 * 2). where: Parámetros o filtro de la consulta.
		 * 3). values: Valores a Buscar.
		 * 4). group_by: Columna por la cual se va a agrupar la info.
		 * 5). order_by: Forma en la que se va a ordenar la info.
		 * 6). limit: Limite que tendrá la consulta.
		 * 7). get_query: Bandera que indica si quieres ejecutar la consultar u obtener
		 * la consulta que se ejecutaría.
		 * 8). sql: Consulta a ejecutar.
		 * 9). valores: Arreglo de valores para la consulta.
		 * 10). not_safe: Bandera que indica si quieres o no limpiar los parámetros de la
		 * consulta.
		 *
		 * @param String $sql Con solo dos campos a regresar en la tabla de resultados
		 * (más seran ignorados)
		 * @return Array El primer campo sera la llave, el segundo el valor
		 **/
		public function getArrayPair ( $sql ) {
			$parametros = array (
				'from',
				'where',
				'values',
				'group_by',
				'order_by',
				'limit',
				'get_query',
				'sql',
				'valores',
				'not_safe'
			);
			$_V = $this -> getParams ( func_get_args ( ), $parametros );
			//Me pasan la consulta
			if ( isset ( $_V{'sql'} ) ) {
				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $_V{'sql'}, $_V{'valores'}, $safe );
				} else {
					$sql = $this -> getQuery ( $_V{'sql'} );
				}
			}
			//FIN Me pasan la consulta

			//Genero la consulta
			else {
				$values = ( isset ( $_V{'values'} )) ? $_V{'values'} : '*';
				$sql = "SELECT $values FROM {$_V{'from'}} ";
				$sql .= ( isset ( $_V{'where'} )) ? " WHERE {$_V{'where'}} " : '';
				$sql .= ( isset ( $_V{'group_by'} )) ? " GROUP BY {$_V{'group_by'}} " : '';
				$sql .= ( isset ( $_V{'order_by'} )) ? " ORDER BY {$_V{'order_by'}} " : '';
				$sql .= ( isset ( $_V{'limit'} )) ? " LIMIT {$_V{'limit'}} " : '';

				if ( isset ( $_V{'valores'} ) ) {
					$safe = ( isset ( $_V{'not_safe'} )) ? false : true;
					//Bandera que indica si quieres o no limpiar los parámetros de la consulta
					$sql = $this -> bind ( $sql, $_V{'valores'}, $safe );
				}
			}
			//FIN Genero la consulta

			//Regreso el sql formado
			if ( isset ( $_V{'get_query'} ) ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			//mySQLI
			if ( $this -> mySqlI ) {
				if ( $results = @$this -> connection -> query ( $sql ) ) {
					$this -> setResultInfo ( $results, 2 );
					$pairs = array ( );
					while ( $fields = $results -> fetch_assoc ( ) ) {
						$key = current ( $fields );
						$value = next ( $fields );
						$pairs{$key} = $value;
					}
					$results -> close ( );
					return $pairs;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}

			//normal
			else {
				if ( $results = @mysql_query ( $sql, $this -> connection ) ) {
					$this -> setResultInfo ( $results, 2 );
					$pairs = array ( );
					while ( $fields = mysql_fetch_array ( $results, MYSQL_ASSOC ) ) {
						$key = current ( $fields );
						$value = next ( $fields );
						$pairs{$key} = $value;
					}
					mysql_free_result ( $results );
					return $pairs;
				}

				$this -> response['msg'] = $this -> getErrorMsg ( ) ;
				$this -> response['status'] = FALSE; $this -> response['class'] = 'error';
				$this -> response['sql'] = $sql;

				return false;
			}
		}


		/**
		 * Inserta un registro en la base de datos.
		 *
		 * @param String $tabla Nombre de la tabla o tablas donde se va a insertar el
		 * registro.
		 * @param Array $valores Arreglo con los valor que se van a insertar.
		 * @param Boolean $get_query Bandera que me indica si se va a regresar el sql
		 * formado.
		 * @param Boolean $safe Bandera que me indica si se van a limpiar los valores a
		 * insertar.
		 * @return Mixed Id autonumérico que se insertó (en caso de que se haya
		 * insertado), o false.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.2
		 */

		public function insert ( $tabla, $valores, $safe = true,  $get_query = false ) {
			$llaves = array_keys ( $valores );

			foreach ( $valores as $k => $v ) {
				if ( is_array ( $v ) )
					$valores [ $k ] = implode ( ',', $v );
			}

			$valores = ($safe) ? $this -> cleanSql ( $valores ) : $valores;

			$sql = "INSERT INTO $tabla (";
			$sql .= implode ( ",", $llaves );
			$sql .= ") VALUES (";
			$sql .= implode ( ",", $valores );
			$sql .= ")";

			//Regreso el sql formado
			if ( $get_query ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			if ( $this -> executeQuery ( $sql ) ) {
				$this -> id = $this -> insert_id;
				$this -> response['status'] = true; $this -> response['class'] = 'success';
				return ($this -> insert_id) ? $this -> insert_id : true;
			} else {
				return false;
			}
		}


		/**
		 * Actualiza/Edita uno o varios registros en la base de datos.
		 *
		 * @param String $tabla Nombre de la tabla o tablas donde se va a editar el(los)
		 * registro(s).
		 * @param Array $valores Arreglo con los valor que se van a actualizar.
		 * @param String $where Condición o filtro para la consulta.
		 * @param Boolean $get_query Bandera que me indica si se va a regresar el sql
		 * formado.
		 * @param Boolean $safe Bandera que me indica si se van a limpiar los valores a
		 * actualizar.
		 * @return Boolean.
		 *
		 */
		public function update ( $tabla, $valores, $where, $safe = true,  $get_query = false) {
			foreach ( $valores as $k => $v ) {
				if ( is_array ( $v ) )
					$valores [ $k ] = implode ( ',', $v );
			}

			$valores = ($safe) ? $this -> cleanSql ( $valores ) : $valores;

			$sql = "UPDATE $tabla SET ";

			foreach ( $valores as $llave => $valor ) {
				$sql .= "$llave = $valor,";
			}

			$sql = substr ( $sql, 0, -1 );

			$sql .= " WHERE $where";

			//Regreso el sql formado
			if ( $get_query ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			if(!$this -> executeQuery ( $sql )){
				return false;
			} else {
				$this -> response['status'] = true; $this -> response['class'] = 'success';
				return true;
			}
		}


		/**
		 * Elimina uno o varios registros en la base de datos.
		 *
		 * @param String $tabla Nombre de la tabla o tablas de donde se van a eliminar
		 * el(los) registro(s).
		 * @param String $where Condición o filtro para la consulta.
		 * @param Boolean $get_query Bandera que me indica si se va a regresar el sql
		 * formado.
		 * @return Boolean.
		 */
		function delete ( $tabla, $where, $get_query = false ) {
			$sql = "DELETE FROM $tabla WHERE $where";

			//Regreso el sql formado
			if ( $get_query ) {
				return $sql;
			}
			//FIN Regreso el sql formado

			$this -> response['status'] = (!$this -> executeQuery ( $sql )) ? false : true;
            
            return $this -> response['status'];
		}


		/**
		 * Regresa el último ID autonumérico insertado.
		 *
		 * @return Mixed Id
		 */
		public function getLastId ( ) {
			return $this -> insert_id;
		}


		/**
		 * Obtiene el mensaje de error de la última consulta.
		 *
		 * @return string Mensaje de error
		 */
		public function getErrorMsg ( ) {
			//mySQLI
			if ( $this -> mySqlI ) {
				return $this -> connection -> error;
			}

			//normal
			else {
				return mysql_error ( $this -> connection );
			}
		}


		/**
		 * Cierra la conección con la Base de Datos.
		 *
		 * @return TRUE en éxito, false otro caso
		 */
		public function close ( ) {
			//mySQLI
			if ( $this -> mySqlI ) {
				if ( !@$this -> connection -> close ( ) ) {
					return false;
				}
			}

			//normal
			else {
				if ( !mysql_close ( $this -> connection ) ) {
					return false;
				}
			}

			$this -> connection = false;
			return true;
		}


		/**
		 * Carga los datos de las columnas de resultados.
		 *
		 * @param Mixed $results El resultado de la consulta de selección
		 * @param Int $limit La cantidad de columnas a las que se les va a extraer la
		 * información
		 */
		private function setResultInfo ( $results, $limit = 0 ) {
			//mySQLI
			if ( $this -> mySqlI ) {
				$this -> result_info = array ( );

				$info = $results -> fetch_fields ( );
				$limit = ($limit == 0) ? count ( $info ) : $limit;

				for ( $x = 0; $x < $limit; $x++ ) {
					foreach ( $info{$x} as $key => $value ) {
						$this -> result_info{$x}{$key} = ($key == 'type') ? $this -> column_type{$value} : $value;
					}
				}

				$this -> loaded_result_info = ($x > 0) ? true : false;
			}

			//normal
			else {
				$this -> result_info = array ( );

				$limit = ($limit == 0) ? mysql_num_fields ( $results ) : $limit;

				for ( $x = 0; $x < $limit; $x++ ) {
					$info = mysql_fetch_field ( $results, $x );
					foreach ( $info as $key => $value ) {
						$this -> result_info{$x}{$key} = ($key == 'type') ? $this -> column_type{$value} : $value;
					}
				}

				$this -> loaded_result_info = ($x > 0) ? true : false;
			}
		}


		/**
		 * Entrega los datos de las columnas de resultados.
		 *
		 * @return Array los datos de las columnas de resultados
		 */
		public function getResultInfo ( ) {
			return $this -> result_info;
		}


		/**
		 * Entrega las opciones de la columna pedida.
		 *
		 * @param String $tabla Nombre de la tabla
		 * @param String $columna Nombre de la columna
		 * @return Array con las opciones
		 */
		public function getOptions ( $tabla, $columna ) {
			//$sql = "SELECT COLUMN_TYPE FROM information_schema.columns WHERE
			// table_schema='$this->database' AND table_name='$tabla' AND
			// column_name='$columna' AND (DATA_TYPE='enum' OR DATA_TYPE='set')";
			$where = "table_schema='{$this->database}' AND table_name='$tabla' AND column_name='$columna' AND (DATA_TYPE='enum' OR DATA_TYPE='set')";

			if ( !($valor = $this -> getOneValue ( 'COLUMN_TYPE', 'information_schema.columns', $where )) ) {
				$opciones = array ( );
			} else {
				$opciones = explode ( "','", str_replace ( array (
					"enum('",
					"set('",
					"')"
				), '', $valor ) );
			}
			return $opciones;
		}


		/**
		 * Entrega las columnas de la tabla pedida.
		 *
		 * @param String $tabla Nombre de la tabla
		 * @return Array con las columnas
		 */
		public function getColumns ( $tabla ) {
			$sql = "SHOW COLUMNS FROM $tabla;";
			return $this -> getOneColumn ( "sql: " . $sql );
		}


		/**
		 * Entrega las columnas de la tabla pedida.
		 *
		 * @param String $tabla Nombre de la tabla
		 * @return Array con las columnas
		 */
		public function getColumnsDetails ( $tabla ) {
			$sql = "DESC $tabla;";
			return $this -> getAllRows ( "sql: " . $sql );
		}


		/**
		 * Entrega los metadatos de una columna.
		 *
		 * @param String $tabla Nombre de la tabla
		 * @param String $columna Nombre de la columna
		 * @return Array los metadatos de la columna
		 */
		public function getColumnInfo ( $tabla, $columna ) {
			//$sql = "SELECT * FROM information_schema.columns WHERE
			// table_schema='$this->database' AND table_name='$tabla' AND
			// column_name='$columna'";
			$metadata = $this -> getOneRow ( 'information_schema.columns', "table_schema='{$this->database}' AND table_name='$tabla' AND column_name='$columna'" );
			if ( $metadata{'DATA_TYPE'} == 'enum' || $metadata{'DATA_TYPE'} == 'set' ) {
				$metadata{'OPTIONS'} = explode ( "','", str_replace ( array (
					"enum('",
					"set('",
					"')"
				), '', $metadata{'COLUMN_TYPE'} ) );
			}
			return $metadata;
		}


		/**
		 * Entrega los metadatos de varias columnas.
		 *
		 * @param String $tabla nombre de la tabla
		 * @param Array $columnas nombres de las columnas
		 * @return Array los metadatos de las columnas
		 */
		public function getColumnsInfo ( $tabla, $columnas ) {
			$where = "table_schema='{$this->database}' AND table_name='$tabla' AND ( column_name='";
			$where .= implode ( "' || column_name='", $columnas );
			$where .= "' ) ";
			$metadata = $this -> getAllRows ( 'information_schema.columns', $where );
			foreach ( $metadata as $key => $row ) {
				if ( $row{'DATA_TYPE'} == 'enum' || $row{'DATA_TYPE'} == 'set' ) {
					$metadata{$key}{'OPTIONS'} = explode ( "','", str_replace ( array (
						"enum('",
						"set('",
						"')"
					), '', $row{'COLUMN_TYPE'} ) );
				}
			}
			return $metadata;
		}


		/**
		 * Entrega los metadatos de una tabla.
		 *
		 * @param String $tabla nombre de la tabla
		 * @return Array los metadatos de la tabla
		 */
		public function getTableInfo ( $tabla ) {
			//$sql = "SELECT * FROM information_schema.tables WHERE
			// table_schema='$this->database' AND table_name='$tabla'";
			return $this -> getOneRow ( 'information_schema.tables', "table_schema='{$this->database}' AND table_name='$tabla'" );
			//$this->getOneRow($sql);
		}


		/**
		 * Entrega los metadatos de varias tablas.
		 *
		 * @param Array $tablas nombres de las tablas
		 * @return Array los metadatos de las tablas
		 */
		public function getTablesInfo ( $tablas ) {
			$where = "table_schema='{$this->database}' AND ( table_name='";
			$where .= implode ( "' || table_name='", $tablas );
			$where .= "' ) ";
			return $this -> getAllRows ( 'information_schema.tables', $where );
			//$this->getAllRows($sql);
		}


		/**
		 * Entrega los metadatos de una tabla y sus columnas.
		 *
		 * @param $tabla nombre de la tabla
		 * @return Array los metadatos de la tabla
		 */
		public function getAllTableInfo ( $tabla ) {
			$metadata = $this -> getTableInfo ( $tabla );
			//$sql = "SELECT * FROM information_schema.columns WHERE
			// table_schema='$this->database' AND table_name='$tabla'";
			$metadata{'COLUMN_INFO'} = $this -> getAllRows ( 'information_schema.columns', "table_schema='{$this->database}' AND table_name='$tabla'" );
			//$this->getAllRows($sql);
			foreach ( $metadata{'COLUMN_INFO'} as $key => $row ) {
				if ( $row{'DATA_TYPE'} == 'enum' || $row{'DATA_TYPE'} == 'set' ) {
					$metadata{'COLUMN_INFO'}{$key}{'OPTIONS'} = explode ( "','", str_replace ( array (
						"enum('",
						"set('",
						"')"
					), '', $row{'COLUMN_TYPE'} ) );
				}
			}
			return $metadata;
		}


		/**
		 * Entrega los metadatos de varias tablas y sus columnas.
		 *
		 * @param Array $tablas nombres de las tablas
		 * @return Array los metadatos de la tabla
		 */
		public function getAllTablesInfo ( $tablas ) {
			$metadata = $this -> getTablesInfo ( $tablas );
			foreach ( $metadata as $key => $tabla ) {
				//$sql = "SELECT * FROM information_schema.columns WHERE
				// table_schema='$this->database' AND table_name='{$tabla{'TABLE_NAME'}}'";
				$metadata{$key}{'COLUMN_INFO'} = $this -> getAllRows ( 'information_schema.columns', "table_schema='{$this->database}' AND table_name='{$tabla{'TABLE_NAME'}}'" );
				//$this->getAllRows($sql);
				foreach ( $metadata{$key}{'COLUMN_INFO'} as $key_2 => $row ) {
					if ( $row{'DATA_TYPE'} == 'enum' || $row{'DATA_TYPE'} == 'set' ) {
						$metadata{$key}{'COLUMN_INFO'}{$key_2}{'OPTIONS'} = explode ( "','", str_replace ( array (
							"enum('",
							"set('",
							"')"
						), '', $row{'COLUMN_TYPE'} ) );
					}
				}
			}
			return $metadata;
		}


		/**
		 * Entrega los metadatos de la base de datos y sus tablas.
		 *
		 * @return Array los metadatos de la base de datos
		 */
		public function getDbTablesInfo ( ) {
			//$sql = "SELECT * FROM information_schema.tables WHERE
			// table_schema='$this->database'";
			return $this -> getAllRows ( 'information_schema.tables', "table_schema='{$this->database}'" );
			//$this->getAllRows($sql);
		}


		/**
		 * Entrega los metadatos de la base de datos, sus tablas y sus columnas.
		 *
		 * @return Array los metadatos de la base de datos
		 */
		public function getAllDbTablesInfo ( ) {
			$metadata = $this -> getDbTablesInfo ( );
			foreach ( $metadata as $key => $tabla ) {
				//$sql = "SELECT * FROM information_schema.columns WHERE
				// table_schema='$this->database' AND table_name='{$tabla{'TABLE_NAME'}}'";
				$metadata{$key}{'COLUMN_INFO'} = $this -> getAllRows ( 'information_schema.columns', "table_schema='{$this->database}' AND table_name='{$tabla{'TABLE_NAME'}}'" );
				//$this->getAllRows($sql);
				foreach ( $metadata{$key}{'COLUMN_INFO'} as $key_2 => $row ) {
					if ( $row{'DATA_TYPE'} == 'enum' || $row{'DATA_TYPE'} == 'set' ) {
						$metadata{$key}{'COLUMN_INFO'}{$key_2}{'OPTIONS'} = explode ( "','", str_replace ( array (
							"enum('",
							"set('",
							"')"
						), '', $row{'COLUMN_TYPE'} ) );
					}
				}
			}
			return $metadata;
		}

		// COMPONENTES {
            /**
             * Función que obtiene los parámetros para las funciones
             *
             * @param Array $vars Arreglo de valores a revisar
             * @return Array Arreglo resultante con los parámetros de la función
             */
            public function get_params ( $vars ) {
                //1 parámetro
                if ( count ( $vars ) == 1 ) {
                    $var = current ( $vars );
                    $result = array ( );
                    //arreglo
                    if ( is_array ( $var ) ) {
                        foreach ( $var as $key => $value ) {
                            if ( !is_string ( $key ) && is_string ( $value ) ) {
                                $match = explode ( ': ', $value, 2 );
                                if ( isset ( $match{1} ) ) {
                                    $result{$match{0}} = $match{1};
                                } else {
                                    $result [ ] = $value;
                                }
                            } elseif ( is_string ( $key ) ) {
                                $result{$key} = $value;
                            } else {
                                $result [ ] = $value;
                            }
                        }
                    }
                    //FIN arreglo
                    //cadena
                    elseif ( is_string ( $var ) ) {
                        $match = explode ( ': ', $var, 2 );
                        if ( isset ( $match{1} ) ) {
                            $result{$match{0}} = $match{1};
                        } else {
                            $result = $vars;
                        }
                    }
                    //FIN cadena
                    //normal
                    else {
                        $result = $vars;
                    }
                    //FIN normal
                    return $result;
                }
                //FIN 1 parámetro
                //varios parámetros
                elseif ( count ( $vars ) > 1 ) {
                    $result = array ( );
                    foreach ( $vars as $key => $var ) {
                        if ( is_string ( $key ) && is_array ( $var ) ) {
                            $result{$key} = $var;
                        } else {
                            $result = array_merge ( $result, $this -> get_params ( array ( $key => $var ) ) );
                        }
                    }
                    return $result;
                }
                //FIN varios parámetros
                //ningún parámetro
                else {
                    return false;
                }
                //FIN ningún parámetro
            }
            /**
             * Función que renombra los parámetros de las funciones
             *
             * @param Array $vars Arreglo de valores a revisar
             * @param Array $names Arreglo de nombres a utilizar
             * @return Array Arreglo resultante con los parámetros de la función
             */
            public function nameParams ( $vars, $names ) {
                $result = array ( );
                foreach ( $vars as $key => $val ) {
                    if ( !is_string ( $key ) ) {
                        foreach ( $names as $name ) {
                            if ( !isset ( $result{$name} ) ) {
                                $result{$name} = $val;
                                break;
                            }
                        }
                    } else {
                        $result{$key} = $val;
                    }
                }
                return $result;
            }
            /**
             * Función que obtiene y renombra los parámetros de las funciones
             *
             * @param Array $vars Arreglo de valores a revisar
             * @param Array $names Arreglo de nombres a utilizar
             * @return Array Arreglo resultante con los parámetros de la función
             */
            public function getParams ( $vars, $names = false ) {
			$vars = $this -> get_params ( $vars );
			if ( $names ) {
				$vars = $this -> nameParams ( $vars, $names );
			}
			return $vars;
		}
		// }
        
        // PAGINATOR {
            private $paginated = false;         // FLAG QUE DICE SI SE HA EFECTUADO PAGINACIÓN EN EL MODELO.
            public $paginationCFG = array(
                'limit'             => 30,      // LIMITE (Actualizado dinámicamente)
                'skip'              => 0,       // REGISTROS A IGNORAR
                'items'             => null,    // ALMACENA LOS REGISTROS TOTALES
                'pages'             => null,    // ALMACENA LAS PÁGINAS
                'current_page'      => 1,       // PÁGINA ACTUAL
                'basic_query'       => null,    // ALMACENA EL QUERY BASE
                'complex_query'     => null,    // ALMACENA EL QUERY COMPLEJO
                'query_wrapper'     => "SELECT * FROM ([basic_query]) A LIMIT [skip], [limit]", // FORMA DE PAGINADO
                'count_query'       => "SELECT count(*) as ITEMS FROM ([basic_query]) A ", // FORMA DE PAGINADO
            );
        
            public function isPaginated(){
                return $this -> paginated;
            }
        
            public function getPaginationHeaders(){
                $data = $this -> paginationCFG;
                foreach(array('basic_query', 'complex_query', 'query_wrapper', 'count_query') as $unnecesaryLabel){
                    unset($data[$unnecesaryLabel]);
                }
                return $data;
            }
        
            /**
             * Obtiene todas las filas de una consulta con métodos de paginado. Especial para uso tablas de 
             * resultados largas.
             *
             * Los parámetros que puede recibir son:
             * 1). from: Nombre de la tabla o tablas.
             * 2). where: Parámetros o filtro de la consulta.
             * 3). values: Valores a Buscar.
             * 4). group_by: Columna por la cual se va a agrupar la info.
             * 5). order_by: Forma en la que se va a ordenar la info.
             * 6). limit: Limite que tendrá la consulta.
             * 7). get_query: Bandera que indica si quieres ejecutar la consultar u obtener
             * la consulta que se ejecutaría.
             * 8). sql: Consulta a ejecutar.
             * 9). valores: Arreglo de valores para la consulta.
             * 10). not_safe: Bandera que indica si quieres o no limpiar los parámetros de la
             * consulta.
             *
             * @param String $sql La consulta a ejecutar
             * @return Array una matriz multidimencional asociativa
             * @author Daniel Lepe 2015
             * @version 1.0
             */
            public function paginateAllRows ($sql) {
                $request = $sql;    // USADA PARA MANDAR EL REQUEST A getAllRows
                $results = array(); // ALMACENA LOS RESULTADOS DE LA CONSULTA
                
                // VALIDATES
                if($this -> isPaginated())
                    die('[ERROR_DE_PAGINADO::MODEL_REVIEW] Sólo debe haber un paginado por request, no puedes usar en 2 modelos distintos un paginado.');
                
                if(!is_numeric($this -> paginationCFG['current_page']) or $this -> paginationCFG['current_page'] < 1)
                    die('[MySqlDb::paginateAllRows] Error de query');
                
                // CALCULA EL SKIPING
                $this -> paginationCFG['skip'] = ($this -> paginationCFG['limit'] * ($this -> paginationCFG['current_page'] - 1));
                

                
                // PRIMERO CONFORMARÉMOS LA CONSULTA BASE HACIENDO USO DE getAllRows.
                $request['get_query'] = true;
                
                // BUILDS BASIC QUERY
                $this -> paginationCFG['basic_query'] = preg_replace('/^[ ]|\;|[ ]$/', null, $this -> getAllRows($request));
                
                // BUILDS HEADERS
                $this -> _pagination_build_headers();
                
                // BUILDS COMPLEX
                $this -> _pagination_build_complex_query();
                
                // EFECTÚA LA CONSULTA DE RESULTADOS
                $results = $this -> getAllRows(array('sql' => $this -> paginationCFG['complex_query']));
                
                // SETS MODEL PAGINATED
                $this -> paginated = true;
                
                // RETURN
                if(isset($sql['get_query']) and $sql['get_query']){
                    return $this -> paginationCFG['complex_query'];
                } else {
                    return $results;
                }
            }
        
            private function _pagination_build_complex_query() {
                // PRE-BUILD
                $this -> paginationCFG['complex_query'] = $this -> paginationCFG['query_wrapper'];
                
                // BUILD COMPLEX
                foreach(array('limit', 'skip', 'basic_query') as $string){
                    $this -> paginationCFG['complex_query'] = preg_replace("/\[$string\]/", 
                                                           $this -> paginationCFG[$string], 
                                                           $this -> paginationCFG['complex_query']);
                }
            }
        
            private function _pagination_build_headers() {
                $count = $this -> getOneRow(array('sql' => preg_replace("/\[basic_query\]/", 
                                               $this -> paginationCFG['basic_query'], 
                                               $this -> paginationCFG['count_query'])));
                
                $this -> paginationCFG['items'] = $count['ITEMS'];
                $this -> paginationCFG['pages'] = ceil($count['ITEMS'] / $this -> paginationCFG['limit']);
            }
        
        // }
		
	}
