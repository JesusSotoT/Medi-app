<?php
/**
 * ValidationKit Class
 *
 * Kit de herramientas de validado para MVC Lite.
 *
 * @Author Daniel Lepe 2015
 * @Version 1.0
 */
class ValidationKit extends MySqlDb {
	public $id = null;
	private $validation = array();
	private $schemas = array();
	private $validationResultsStack = array();
	private $table = null;
	private $field = null;
	private $fieldName = null;
	private static $textuals = array(
		'msg_required' 		=> 'MSG_REQUIRED',
		'rule_undefined'	=> '[RULE_UNDEFINED] Existe eun error con la estructura de $validaciones del modelo. Cada campo a validar debe tener N numero de validaciones en el arreglo. '
	);
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
	public function __construct ( ) {
		// Crea conexiòn.
		$this -> create_connection ( );
	}
	
	// REGEX LIB {
	protected static $regExCollection = array(
		'email'		=> "/[a-zA-Z0-9]+(?:(\.|_)[A-Za-z0-9!#$%&'*+\/=?^`{|}~-]+)*@(?!([a-zA-Z0-9]*\.[a-zA-Z0-9]*\.[a-zA-Z0-9]*\.))(?:[A-Za-z0-9](?:[a-zA-Z0-9-]*[A-Za-z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?/",
	);
	// }
	// PUBLICS {
		/**
		 * get_results
		 *
		 * Devuelve los resultados de validación.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function get_results() {
			return $this -> validationResultsStack;
		}
		/**
		 * load_validation
		 *
		 * Importa las reglas de validado del modelo en específico y el esquema para cotejado de tipo de datos.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function load_validation ($validationRules = array(), $schemas = array()) {
			if( !is_array($validationRules) ) die ('MODEL::$validation debe ser un array.');
			$this -> validation = $validationRules;
			$this -> schemas  = $schemas;
			return true;
		}
		/**
		 * validateAll
		 *
		 * Valida un arreglo multinivel cotejando todo el recibo de un arreglo con diferentes tablas en sí.
		 *
		 * La función devuelve veradero|falso para efecto de interrumpir flujos de proceso. El resultado de la validación, 
		 * si lo hubiera, se almacenará en $this -> response['validation'];
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function validateAll ($multiTableData = array()) {
			$return = true;
			foreach($multiTableData as $table => $data){
				if( !$this -> validateAs($table, $data) and $return == true) $return = false;
			}
			return $return;
		}	
		/**
		 * validateAs
		 *
		 * Valida un array "mononivel" cotejandolo con la clave especificada.
		 * La estructura de la clave es:
		 * "tabla" para un arreglo: array('tabla' => array('campo' => array('rule' => 'RULE', 'msg' => 'MSG', 'required' => true)))
		 *
		 * La función devuelve veradero|falso para efecto de interrumpir flujos de proceso. El resultado de la validación, 
		 * si lo hubiera, se almacenará en $this -> response['validation'];
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function validateAs ($tableKey = null, $tablaData = array()) {
			$return = true;
			foreach($tablaData as $field => $data){
				if( !$this -> validateFieldAs($tableKey . "." . $field, $data) and $return == true ) 
					$return = false;
			}
			return $return;
		}		
		/**
		 * validateFieldAs
		 *
		 * Valida un string cotejandolo con la clave especificada.
		 * La estructura de la clave es:
		 * "tabla.campo" para un arreglo: array('tabla' => array('campo' => array('rule' => 'RULE', 'msg' => 'MSG', 'required' => true)))
		 *
		 * La función devuelve veradero|falso para efecto de interrumpir flujos de proceso. El resultado de la validación, 
		 * si lo hubiera, se almacenará en $this -> response['validation'];
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function validateFieldAs ($fieldKey = null, $data = array()) {
			$this -> fieldName = $this -> getFieldName($fieldKey);
			$this -> build_names($fieldKey);
			// Esta función realiza el procedimeinto de validado solo si existe una regla definida en el stack.
			if(isset($this -> validation[$this -> table][$this -> field])){
				$validateRule 	= $this -> validation[$this -> table][$this -> field];
				$schemasField 	= null;
				if(isset($this -> schemas[$this -> table][$this -> field]))
					$schemasField = $this -> schemas[$this -> table][$this -> field];
				return  $this -> validate($data, $validateRule, $schemasField);
			} else {
				return true;
			}
		}
		/**
		 * build_names 
		 *
		 * Construye nombres de tabla y campo desde $fieldKey.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		private function build_names ($fieldKey){
			$fieldKey = explode('.', $fieldKey);
			$this -> table = $fieldKey[0];
			$this -> field = $fieldKey[1];
		}
		/**
		 * getFieldName 
		 *
		 * Devuelve el nombre de campo desde un $fieldKey.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		private function getFieldName ( $fieldKey ) {
			$field = explode('.', $fieldKey);
			$fieldName = '';
			if(!isset($fieldKey[1])){
				$fieldName .= $fieldKey[0];
			} else {
				foreach($field as $key){
					if($fieldName == ''){
						$fieldName .= $key;
					} else {
						$fieldName .= "[$key]";
					}
				}
			}
			return $fieldName;
		}
		/**
		 * validate
		 *
		 * Valida un dato, con la regla pre-establecida. Admite en $rule un array con la clave obligatoria de 'rule'.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		private function validate ( $data, $rules, $schemas = null ){
			$return = true;
			$data = trim($data);
            
			foreach ($rules as $k => $rule)  {
                // VALIDATES RULE
                $this -> validate_multiple_rule($rule);
                
				// INIT
					$msg = (isset($rule['msg']))? $rule['msg'] : self::$textuals['msg_required'];
                
				// VALIDADO DE DATO
                if(is_array($rule['rule'])){
                    if(isset($rule['rule']['min']) and !$this -> _validate_min($data, $rule['rule']['min'], $msg, $schemas))
                        $return = false;
                    if(isset($rule['rule']['max']) and !$this -> _validate_max($data, $rule['rule']['max'], $msg, $schemas))
                        $return = false;
                } else if (is_string($rule['rule'])) {
                    if($rule['rule'] == 'unique'){
                        $return = $this -> unique($data, $msg);
                    } else {
                        switch ( $rule['rule'] ){
                            case 'email':
                                $rule['rule'] = self::$regExCollection['email'];
                                break;
                        }
                        $return = $this -> _validate_as_regex($data, $rule['rule'], $msg, $schemas);
                    }
                } else {
                    $return = true;
                }
                
			}
			return $return;
		}
        // validate_multiple_rule
        private function validate_multiple_rule($rule){
            if(!is_array($rule) or !isset($rule['rule'])) 
                die(self::$textuals['rule_undefined']);
        }
	// PUBICS }
	// VALIDATES {
		// VALIDATE MINS {
			private function validate_as_numeric_min ($data, $size) {
				if(!is_numeric(($data)*1))
					return false;
				if(($data * 1) < $size)
					return false;
				return true;
			}
			private function validate_as_string_min ($data, $size) {
				if(!is_string($data))
					return false;
				if(strlen($data) < $size)
					return false;
				return true;
			}
			/**
			 * _validate_min
			 *
			 * Valida en función de valores mínimos.
			 *
			 * @Author Daniel Lepe 2015
			 * @Version 1.0
			 */
			private function _validate_min ($data, $size, $msg, $schemas){
				$status = true;
				if(is_null($schemas)){
					if(is_numeric($data)){
						$status = $this -> validate_as_numeric_min ( $data, $size );
					} else {
						$status = $this -> validate_as_string_min ( $data, $size );
					}
				} else {
					if( $this -> field_type_is_string ($schemas['Type']) ){
						$status = $this -> validate_as_string_min ( $data, $size );
					} elseif ($this -> field_type_is_string ($schemas['Type'])){
						$status = $this -> validate_as_string_min ( $data, $size );
					}
				}
				$this -> _validate_write_to_stack($status, $msg);
			}
		// VALIDATE MINS }
		// VALIDATE MAXS {
			private function validate_as_numeric_max ($data, $size) {
				if(!is_numeric(($data)*1))
					return false;
				if(($data)*1 > $size)
					return false;
				return true;
			} 
			private function validate_as_string_max ($data, $size) {
				if(!is_string($data))
					return false;
				if(strlen($data) > $size)
					return false;
				return true;
			}
			/**
			 * _validate_max
			 *
			 * Valida en función de valores máximos.
			 *
			 * @Author Daniel Lepe 2015
			 * @Version 1.0
			 */
			private function _validate_max ($data, $size, $msg, $schemas){
				$status = true;
				if(is_null($schemas)){
					if(is_numeric($data)){
						$status = $this -> validate_as_numeric_max ( $data, $size );
					} else {
						$status = $this -> validate_as_string_max ( $data, $size );
					}
				} else {
					if( $this -> field_type_is_string ($schemas['Type']) ){
						$status = $this -> validate_as_string_max ( $data, $size );
					} elseif ($this -> field_type_is_string ($schemas['Type'])){
						$status = $this -> validate_as_string_max ( $data, $size );
					}
				}
				$this -> _validate_write_to_stack($status, $msg);
			}
		// VALIDATE MAXS }
		// VALIDATE REGEX {
			private function _validate_as_regex($data, $regex, $msg) {
				$status = (preg_match($regex, $data));
				$this -> _validate_write_to_stack($status, $msg);
			}
		// VALIDATE REGEX }
		// VALIDATE UNIQUE {
			private function unique($data, $msg){
				if(!isset($this -> table) or !isset($this -> field)) die('No se puede validar un registro en la base de datos sin la Tabla|Campo no están directametne definidos.');
				$lookUp = array(
					'from' => $this -> table,
					'values' => $this -> field,
					'where' => $this -> field . " = '" . $data . "'");
				if(!is_null($this -> id)){
					$lookUp['where'] = $lookUp['where'] . " AND id != " . $this -> id;
				}
				$read = $this -> getOneRow($lookUp);
				$status = (empty($read));
				$this -> _validate_write_to_stack($status, $msg);
			}
		// }
	// }
	// COMMONS {
	/**
	 * field_type_is_string($fieldType)
	 *
	 * Devuelve verdadero si el tipo de dato es un string
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	private function field_type_is_string ($fieldType) {
		return (preg_match("/(char)|(text)/", $fieldType)) ? true : false;
	}
	/**
	 * field_type_is_numeric($fieldType)
	 *
	 * Devuelve verdadero si el tipo de dato es numérico
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	private function field_type_is_numeric ($fieldType) {
		return (preg_match("/(decimal)|(float)|(dobule)|(int)/", $fieldType)) ? true : false;
	}
	/**
	 * _validate_write_to_stack
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	private function _validate_write_to_stack( $status = true, $msg = true){
		$form = array(
			'field' 	=> $this -> fieldName,
			'status' 	=> $status,
			'msg'		=> (!$status)? $msg : null,
		);
		if(!isset($this -> validationResultsStack[$this -> fieldName]) or ($this -> validationResultsStack[$this -> fieldName]['status']))
		$this -> validationResultsStack[$this -> fieldName] = $form;
	}
	// COMMONS }
}