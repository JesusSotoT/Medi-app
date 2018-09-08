<?php

	/**
	 * Form Helpers,
	 *
	 * Ayuda a vincular de manera casi instantÃ¡nea los modelos de datos con los
	 * formularios
	 * de forma general.
	 *
	 * El formato es una clase, y estÃ¡ pensado para ser implementado con bootstrap,
	 * mas detalles en
	 * http://getbootstrap.com/css/#forms
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 2.0 11-Sep-2014
	 * @Author Daniel Lepe 2015
	 * @Revision 2.2 21-Abr-2015
	 * Permte generar campos con hasta 4 subniveles de nombramiento...
	 * @Revision 2.3 14-Ago-2015
	 * Ahora se le puede pasar el parámetro simple a los selects o multiselects para evitar componente de js.
	 * @Revision 2.4 08-Oct-2015
	 * Ahora se le puede pasar el parámetro de opciones a scaffold
	 */

	class FormHelper extends Helper {

		private $model;
		private $table;
		private $schema;

		public $options;

		public $removers = array (
			'elim',
			'created',
			'modified',
			'post_id'
		);

		public $id;

		// Usada especialmente para la clase JS en submit();

		public $default_class = array (
			'wrapper' 	=> 'form-group',
			'label' 	=> "col-sm-5 control-label",
			'div' 		=> "col-sm-7",
			'input' 	=> "form-control",
			'form' 		=> 'form-horizontal form-groups-bordered'
		);

		public $default_class_nonhorizontal = array (
			'wrapper' => 'form-group',
			'label' => "",
			'div' => false,
			'input' => "form-control"
		);

		public $class_binding = array ( 'checkbox' => array (
				'form-control',
				'col-sm-5',
				'col-sm-3'
			) );

		public static $field_defaults;
		public static $field_types;
		public static $field_requirements;
		public static $parallels;

		public function scaffold ( $modelTable, $print_code = false, $options = array()) {

			echo $this -> create ( $modelTable, $options );

			if ( $print_code )
				echo htmlentities ( sprintf ( 'echo $this -> Form -> create("%s");', $modelTable ) ) . "<br/>";

			foreach ( $this -> _bridge -> {$this -> model}[$this -> table] as $field => $details ) {

				if ( $print_code ) {

					echo htmlentities ( 'echo $this -> Form -> input("' . $field . '", array());' ) . "<br/>";

				} else {

					if ( $field == $this -> primary_key ) {

						$primary_keyOptions = $this -> options;
						$primary_keyOptions [ 'type' ] = 'hidden';

						echo $this -> input ( $field, $primary_keyOptions );

					} else {

						echo $this -> input ( $field, $this -> options );

					}

				}

			}

			if ( $print_code ) {

				echo htmlentities ( 'echo $this -> Form -> end("Enviar", array());' ) . "<br/>";

			} else {

				echo $this -> end ( 'Enviar', $this -> options );

			}

		}


		public function create ( $modelTable, $options = array() ) {
			if(!empty($options)){
				foreach($options as $k => $o){
					$this -> options[$k] = $o;
				}
			}

			$this -> Form ( $modelTable );

			if ( isset ( $this -> options [ 'class' ] ) ) {
				$class = $this -> options [ 'class' ];
			} else {
				$class = $this -> default_class [ 'form' ];
			}

			if ( isset ( $this -> options [ 'method' ] ) ) {
				$method = $this -> options [ 'method' ];
			} else {
				$method = 'post';
			}

			if ( isset ( $this -> options [ 'id' ] ) )
				$this -> id = $this -> options [ 'id' ];

			if ( !isset ( $this -> options [ 'action' ] ) ) {
				$data = $this -> get_request();
				$this -> options [ 'action' ] = $this -> url ( $data );

			} elseif ( is_array ( $this -> options [ 'action' ] ) ) {
				$this -> options [ 'action' ] = $this -> url (  $this -> options [ 'action' ] );
			}

			return sprintf ( '<form role="form" class="%s" action="%s" method="%s" id="%s" enctype="%s">', $class, $this -> options [ 'action' ], $method, $this -> id, $this -> _get_enctype_method ( $this -> options ) );

		}


		public function input ( $field, $options = array() ) {
			if(isset($options['pure']) and $options['pure']){
				$options['div'] = false;
				$options['label'] = false;
				$options['wrapper'] = false;
			}
			if ( preg_match ( '/^\w*\.\w*/', $field ) ) {
				// Detecciòn de inputs desde otro modelo!
				$new_field = explode ( '.', $field );
				$table = $new_field [ 0 ];
				$field = $new_field [ 1 ];
				if(isset($new_field [2]))
					$ext = $new_field [ 2 ];
				if(isset($new_field [3]))
					$ext2 = $new_field [ 3 ];
				// Bootstrap para alternar de forma paralela con otro modelo
				$options = $this -> _retrieve_parallel_model_defaults ( $table, $field, $options);
			} else {
				$table = $this -> table;
			}

			$name = sprintf ( "%s[%s]", $table, $field );

			if(isset($ext)) $name .= sprintf("[%s]", $ext);
			if(isset($ext2)) $name .= sprintf("[%s]", $ext2);

			if ( isset ( $options [ 'id' ] ) ) {
				$id = $options [ 'id' ];
			} else {
				$id = $name;
			}

			$label = $this -> _build_label ( $field, $options );

			if ( isset ( $options [ 'default' ] ) ) {
				$default = $options [ 'default' ];
			} else {
				$default = $this -> _find_default_value ( $field );
			}

			// Es imperante que reescriba el valor por value, despuès del default y despuÃ©s
			// del valor previo.
			if ( isset ( $this -> data [ $table ] [ $field ] ) )
				$default = $this -> data [ $table ] [ $field ];

			// Value es imperativo
			if ( isset ( $options [ 'value' ] ) )
				$default = $options [ 'value' ];

			// Si el valor por defeto estÃ¡ vacío, y el cempo es el ID de representación,
			// no devuelve input,
			// para obtener un empty id, en caso de que no estÃ© vacío pero sea la clave
			// primaria,
			// preestablee el typo como hidden a menos de que la solicitud pida lo contrario.
			if ( $field == $this -> primary_key && empty ( $default ) ) {

				return NULL;

			} elseif ( $field == $this -> primary_key && !empty ( $default ) ) {

				if ( !isset ( $options [ 'type' ] ) )

					$options [ 'type' ] = 'hidden';
			}

			if ( isset ( $options [ 'input' ] [ 'class' ] ) ) {

				$input_class = $options [ 'input' ] [ 'class' ];

			} else {

				$input_class = $this -> _get_default_class ( $field, 'input' );

			}

			$input = $this -> _retrieve_input_string ( $field, $default, $name, $id, $input_class, $options );

			return $this -> _fields_warp ( $field, $label, $input, $id, $options );
		}


		public function end ( $text, $options = array(), $warp = true ) {

			$class = 'btn btn-default';

			if ( isset ( $options [ 'class' ] ) )
				$class = $options [ 'class' ];

			$input = "<button type='submit' class='$class'>$text</button>";

			if ( !isset ( $options [ 'wrapper' ] [ 'class' ] ) ) {
				$this -> options [ 'wrapper' ] [ 'class' ] = $this -> default_class [ 'wrapper' ] . ' submit';
			}
			if($warp){
				return $this -> _fields_warp ( null, '&nbsp;', $input, uniqid ( ), $this -> options ) . $this -> endTag();
			} else {
				return $input;
			}
		}

		public function endTag(){
			return "</form>";
		}


		// Establece la clave primaria de cada tabla.
		public function set_primarykey ( ) {
			$this -> primary_key = 'id';
			if ( isset ( $this -> options [ 'primary_key' ] ) ) {
				$this -> primary_key = $this -> options [ 'primary_key' ];
			}
			unset ( $this -> options [ 'primary_key' ] );
		}


		private function _extract_offset_bootstrap_class ( $class_str ) {

			preg_match ( '/col-\w+-\d+/', $class_str, $a );

			if ( !empty ( $a ) ) {
				preg_match ( '/^\w{3}-\w{2}-/', $a [ 0 ], $c );
				preg_match ( '/\d+/', $a [ 0 ], $b );
				return sprintf ( '%soffset-%s', $c [ 0 ], $b [ 0 ] );
			} else {
				return null;
			}
		}


		private function _derivate_checkbox_classes_from_defaults ( $key = null ) {

			$return = array (
				'wrapper' => '',
				'label' => "",
				'div' => "",
				'input' => "",
			);

			if ( preg_match ( '/form\-horizontal/', $this -> default_class [ 'form' ] ) ) {
				$return [ 'label' ] = '';
				$return [ 'div' ] = sprintf ( "%s %s", $this -> _extract_offset_bootstrap_class ( $this -> default_class [ 'label' ] ), $this -> default_class [ 'div' ] );
				$return [ 'input' ] = $this -> _remove_unapprobatedInputClass ( 'checkbox', $this -> default_class [ 'input' ] );
				$return [ 'wrapper' ] = 'form-group';
			}

			if ( $key != null ) {
				return $return [ $key ];
			}

			return $return;
		}


		private function _clean_names ( ) {

			$this -> model = preg_replace ( '/\..*/', NULL, $this -> modelTable );

			$this -> table = preg_replace ( '/.*\./', NULL, $this -> modelTable );

		}


		private function Form ( $modelTable ) {

			// Establce el modelo y tabla a usar.
			$this -> modelTable = $modelTable;

			// Obtiene el esquema.
			$this -> _clean_names ( );

			$this -> set_primarykey ( );

			$this -> id = uniqid ( );

		}


		public $form_types = array (
			'selected' => 'normal',
			'options' => array (
				'upload',
				'normal'
			)
		);

		private function _get_enctype_method ( ) {

			switch ( $this -> form_types['selected']) {

				case 'upload' :
					return 'multipart/form-data';
					break;

				default :
					return $this -> form_types [ 'selected' ];
					break;
			}

		}


		private function _build_schematics ( $schema ) {
			$return = array ( );
			if ( is_array ( $schema ) ) {
				foreach ( $schema as $k => $field ) {
					$return [ 'schema' ] [ $k ] = $field;
					$return [ 'defaults' ] [ $field [ 'Field' ] ] = $field [ 'Default' ];
					$return [ 'types' ] [ $field [ 'Field' ] ] = $field [ 'Type' ];
					if ( $field [ 'Null' ] == 'NO' ) {
						$return [ 'requires' ] [ $field [ 'Field' ] ] = 1;
					} else {
						$return [ 'requires' ] [ $field [ 'Field' ] ] = 0;
					}
				}
			}
			return $return;
		}


		private function _retrieve_parallel_model_defaults ( $table, $field, $options = null ) {
			// Verifica que no sea un campo de transacciÃ³n de datos, determinado con
			// el nombre de modelo: '_', de ser asÃ­, devuelve las opciones como tal.
			if ( $table == '_' )
				return $options;

			// Revisa si ya existe una descripciÃ³n de este modelo desde $this -> parallels
			if ( !isset ( self::$parallels [ $table ] ) ) {
				// Obtiene la descripciÃ³n del Modelo.
				$schema = db_query ( 'DESC ' . $table );
				self::$parallels [ $table ] = $this -> _build_schematics ( $schema );
			}

			if ( !isset ( $this -> options [ 'default' ] ) ) {
				// Obtiene el valor por defecto de este campo en este modelo
				$this -> options [ 'default' ] = self::$parallels [ $table ] [ 'defaults' ] [ $field ];
			}

			if ( !isset ( $this -> options [ 'required' ] ) ) {
				// Obtiene el valor de requerimento de este campo en este modelo
				$this -> options [ 'required' ] = self::$parallels [ $table ] [ 'requires' ] [ $field ];
			}

			if ( preg_match ( '/(enum|ENUM)/', self::$parallels [ $table ] [ 'types' ] [ $field ] ) ) {
				// En este caso el tipo se determina por el argumento options
				if ( !isset ( $this -> options [ 'options' ] ) ) {
					// Obtiene el valor de opciones del modelo para campos ENUM
					$this -> options [ 'options' ] = $this -> _retrieve_enum_set_options ( $field, self::$parallels [ $table ] [ 'schema' ] );
				}
			}

			if ( preg_match ( '/(tinyint|TINYINT|bool|BOOL)/', self::$parallels [ $table ] [ 'types' ] [ $field ] ) ) {
				if ( !isset ( $this -> options [ 'type' ] ) )
					$this -> options [ 'type' ] = 'tinyint';
			}

			if ( preg_match ( '/(text|TEXT)/', self::$parallels [ $table ] [ 'types' ] [ $field ] ) ) {
				if ( !isset ( $this -> options [ 'type' ] ) )
					$this -> options [ 'type' ] = 'textarea';
			}

			return array_merge($this -> options, $options);
		}


		private function _get_default_class ( $field, $element = null ) {
			$type = $this -> _find_field_type ( $field );

			$boolean_types = array (
				'checkbox',
				'tinyint',
				'bool'
			);

			if ( in_array ( $type, $boolean_types ) or (isset ( $this -> options [ 'type' ] ) and in_array ( $this -> options [ 'type' ], $boolean_types )) ) {
				$input_class = $this -> _derivate_checkbox_classes_from_defaults ( );
			} else {
				if ( $this -> default_class [ 'form' ] == 'form' ) {
					$input_class = $this -> default_class_nonhorizontal;
				} else {
					$input_class = $this -> default_class;
				}
			}

			if ( $element == null ) {
				return $input_class;
			} else {
				return $input_class [ $element ];
			}

		}


		private function _prepare_for_select ( ) {

			$result = array ( );

			foreach ( $this -> options as $legend ) {

				$value = str_replace ( '\'', '', trim ( $legend ) );
				$legend = str_replace ( '_', ' ', trim ( $value ) );
				$result [ $value ] = $legend;

			}

			$this -> options = $result;

			return $result;
		}


		private function _retrieve_enum_set_options ( $field ) {
			if ( !isset ( $this -> _bridge -> {$this -> model} [ $this -> table ] [ $field ] ) ) {
				return array ( );
			}

			$optionsList = $this -> _bridge -> {$this -> model} [ $this -> table ] [ $field ];

			$optionsList = preg_replace ( '/^(enum\(|set\()|\)$/', NULL, $optionsList [ 'Type' ] );
			$optionsList = preg_replace ( "/^\'|(\')|\'$/", NULL, $optionsList );
			$optionsList = preg_replace ( "/[\,(^\\\,)]/", '|', $optionsList );
			$optionsList = explode ( '|', $optionsList );

			$return = array ( );

			foreach ( $optionsList as $l ) {
				$return [ $l ] = $l;
			}

			return $return;
		}


		private function _remove_unapprobatedInputClass ( $type, $class ) {
			if ( !empty ( $this -> class_binding [ $type ] ) ) {
				foreach ( $this->class_binding[$type] as $class_element ) {
					$class = str_replace ( $class_element, '', $class );
				}
			}
			return $class;
		}


		private function _clean_id_string ( $id ) {
			$id = preg_replace ( '/[\]]$/', '', $id );
			$id = preg_replace ( '/[\[|\]]/', '_', $id );
			$id = preg_replace ( '/\_{2,9}/', '_', $id );
			return $id;
		}


		private function _extract_attrs ( $options ) {
			$attrs = '';
			if ( isset ( $options [ 'attr' ] ) and is_array ( $options [ 'attr' ] ) ) {
				foreach ($options['attr'] as $a => $k ) {
					$attrs .= sprintf ( ' %s = "%s" ', $a, $k );
				}
			}
			return $attrs;
		}


		private function _retrieve_input_string ( $field, $default, $name, $id, $class, $options ) {
			// ID
			$id = $this -> _clean_id_string ( $id );

			// Determinaciòn de tipo
			if ( isset ( $options [ 'type' ] ) ) {
				$field_type = $options [ 'type' ];
			} else {
				$field_type = $this -> _find_field_type ( $field );
			}

			// Determinaciòn de tipo por pase de variable con arreglo de opciones.
			if ( isset ( $options [ 'options' ] ) && is_array ( $options [ 'options' ] ) ) {
				$field_type = 'select';
				$select_options = $options [ 'options' ];
			}

			// Determinación del estatus de requerido
			$required_str = NULL;

			if ( isset ( $options [ 'required' ] ) AND $options [ 'required' ] === true) {
				$required_str = "required='required'";
			} elseif (!key_exists('required', $options) and $this -> _find_if_required ( $field ) ) {
				$required_str = "required='required'";
			}

			//Disabled
			if ( in_array ( 'disabled', $options, true ) ) {
				$disabled = "disabled = 'disabled'";
			} else {
				$disabled = NULL;
			}

			$attr = $this -> _extract_attrs ( $options );

			switch ($field_type) {
				case 'hidden' :
					$return = "<input type='hidden' name='$name' id='$id' class='$field_type $class' value='$default' $attr/>";
					break;
				case 'enum' :
				case 'set' :
				case 'select' :
				case 'multiple' :
					$fieldsWithArrayInDefaults = array (
						'set',
						'multiple'
					);

					if ( in_array ( $field_type, $fieldsWithArrayInDefaults ) ) {
						if ( is_string ( $default ) )
							$default = explode ( ',', $default );
					}

					// OPTIONS SETUP OVERRIDE
					if ( !isset ( $select_options ) )
						$select_options = $this -> _retrieve_enum_set_options ( $field, $this -> schema );

					// MULTIPLE PRECONFIG
					$multiple = "";

					if ( (isset ( $options [ 'multiple' ] ) && $options [ 'multiple' ]) or $field_type == 'set' ) {
						$multiple = "multiple = 'multiple'";
						$name .= '[]';
					}

					$return = "<select class='form-control $class' name='$name' id='$id' class='$class' $multiple $required_str $disabled $attr title='Nada seleccionado...' data-live-search='true'>";
					if ( isset ( $options [ 'allowEmpty' ] ) and $options [ 'allowEmpty' ] )
						$return .= sprintf ( "<option value='%s'>%s</option>", NULL, NULL );

					foreach ( $select_options as $value => $option ) {

						if ( is_array ( $option ) ) {

							$return .= sprintf ( "<optgroup label='%s'>", $value );

							foreach ( $option as $sub_value => $sub_option ) {
								if ( ($sub_value == $default) or (is_array ( $default ) and in_array ( $sub_value, $default )) ) {
									$return .= sprintf ( "<option value='%s' selected>%s</option>", $sub_value, $sub_option );
								} else {
									$return .= sprintf ( "<option value='%s'>%s</option>", $sub_value, $sub_option );
								}
							}

							$return .= sprintf ( "</optgroup>" );

						} else {

							if ( ($value == $default) or (is_array ( $default ) and in_array ( $value, $default )) ) {

								$return .= sprintf ( "<option value='%s' selected>%s</option>", $value, $option );

							} else {

								$return .= sprintf ( "<option value='%s'>%s</option>", $value, $option );

							}

						}

					}
					$return .= "</select>";
                    if(!isset($options [ 'simple' ]) or !$options [ 'simple' ])
                        $return .= $this -> _script_for_select_inputs($id);
					break;
				case 'tinyint' :
				case 'checkbox' :
				case 'bool' :
					$class = $this -> _remove_unapprobatedInputClass ( 'checkbox', $class );
					if ( $default ) {
						$checked = "checked";
					} else {
						$checked = "";
					}
					$return = "<input type='hidden' value='0' name='$name' /><input type='checkbox' value='1' name='$name' id='$id' class='$class' $checked $disabled $attr/>";
					break;
				case 'textarea' :
				case 'text' :
				case 'mediumtext' :
				case 'longtext' :
					if ( !isset ( $type ) )
						$type = 'text';
					if ( isset ( $options[ 'rows' ] ) ) {
						$rows = $options[ 'rows' ];
					} else {
						$rows = 10;
					}
					$placeholder = '';
					if ( isset ( $options[ 'placeholder' ] ) )
						$placeholder = $options[ 'placeholder' ];
					$return = "<textarea name='$name' rows='$rows' id='$id' class='$field_type $class' $required_str  placeholder='$placeholder'  $disabled $attr >$default</textarea>";
					break;
				default :
					if ( isset ( $options[ 'type' ] ) ) {
						$type = $options[ 'type' ];
					} elseif ( !isset ( $type ) ) {
						$type = $field_type;
					}
					$placeholder = '';
					if ( isset ( $options [ 'placeholder' ] ) )
						$placeholder = $options [ 'placeholder' ];
					switch ($type) {
						case 'int' :
						case 'float' :
						case 'decimal' :
						case 'mediumint' :
						case 'smallint' :
							$type = 'number';
							break;
						case 'varchar' :
						case 'char' :
							$type = 'text';
							break;
						case 'tinytext' :
							$type = 'url';
							break;
					}
					$return = "<input type='$type' name='$name' id='$id' class='$field_type $class' value='$default' $required_str $disabled $attr />";
					break;
			}

			return $return;
		}

		/**
		 * _fields_warp
		 *
		 * Envuelve un Input entre el Wrapper, y Div HOlder, además agrega el $label
		 *
		 * @Version 1.1
		 * Si alugno de los componentes se ha establecido como false, se envolvéra el input.
		 *
		 * @Version 1.0
		 * @Author Daniel Lepe 2015
		 */

		private function _fields_warp ( $field, $label, $input, $id, $options ) {

			$type = $this -> _find_field_type ( $field );

			switch ($type) {
				case 'tinyint' :
				case 'bool' :
					$type = 'checkbox';
					break;
			}

			$field_levels = array (
				'wrapper',
				'label',
				'div',
				'input'
			);

			foreach ( $field_levels as $lev ) {

				if ( isset ( $options [ $lev ] [ 'class' ] ) and is_array ( $options [ $lev ] ) ) {

					${$lev . "_class"} = $options [ $lev ] [ 'class' ];

				} elseif ( isset ( $options [ $lev ] ) and !$options[$lev] ){
					// Elimina los contenedores :)
					${$lev . "_class"} = null;
				} else {

					${$lev . "_class"} = $this -> _get_default_class ( $field, $lev );
				}

			}

			// Type Override
			if ( isset ( $options [ 'type' ] ) )
				$type = $options [ 'type' ];

			switch ($type) {
				case 'tinyint' :
				case 'checkbox' :
				case 'bool' :
					$return = $this -> _wrap_checkbox ( $wrapper_class, $id, $label_class, $label, $div_class, $input );
					break;
				case 'hidden' :
					$return = $this -> _wrap_hidden ( $wrapper_class, $id, $label_class, $label, $div_class, $input );
					break;
				default :
					$return = $this -> _wrap_basic ( $wrapper_class, $id, $label_class, $label, $div_class, $input );
					break;
			}
			return $return;
		}


		private function _wrap_hidden ( $wrapper_class, $id, $label_class, $label, $div_class, $input ) {
			return $input;
		}


		private function _wrap_basic ( $wrapper_class, $id, $label_class, $label, $div_class, $input ) {
			$return = "";
			if ( $wrapper_class != false)
				$return .= sprintf ( "<div class='%s'>", $wrapper_class );

			if ( $label  and $label_class != false)
				$return .= sprintf ( "<label for='%s' class='%s'>%s</label>", $this -> _clean_id_string ( $id ), $label_class, $label );

			if($div_class != false) {
				$return .= sprintf ( "<div class='%s'>%s</div>", $div_class, $input, $label );
			} else {
				$return .= sprintf ( "%s", $input  );
			}

			if ( $wrapper_class != false )
				$return .= "</div>";

			return $return;
		}


		private function _wrap_checkbox ( $wrapper_class, $id, $label_class, $label, $div_class, $input ) {
			$return = "";
			$id = $this -> _clean_id_string ( $id );

			if ( @$this -> options [ 'wrapper' ] !== FALSE )
				$return = sprintf ( "<div class='%s'>", $wrapper_class );

			if ( $label ) {
				$label = sprintf ( "<label for='%s' class='%s'>%s</label>", $id, $label_class, $label );
			} else {
				$label = NULL;
			}

			if ( @$this -> options [ 'div' ] !== FALSE ) {
				$return .= sprintf ( "<div class='%s'>%s %s</div>", $div_class, $input, $label );
			} else {
				$return .= sprintf ( "%s %s", $input, $label );
			}

			if ( @$this -> options [ 'wrapper' ] !== FALSE )
				$return .= "</div>";

			return $return;
		}


		private function _humanize ( $str ) {
			return str_replace ( '_', ' ', trim ( $str ) );
		}


		private function _build_label ( $field, $options ) {
			if(isset($options['pure']) and $options['pure']){
				return null;
			}
			$required = NULL;
			if ( isset ( $options [ 'required' ] ) and $options [ 'required' ] ) {
				$required = " * ";
			} elseif (!key_exists('required', $options) and $this -> _find_if_required ( $field ) ) {
				$required = " * ";
			}
			if ( isset ( $options [ 'label' ] ) ) {
				if ( !$options [ 'label' ] ) {
					return NULL . $required;
				} elseif ( is_string ( $options [ 'label' ] ) ) {
					return ucfirst ( $options [ 'label' ] ) . $required;
				} elseif ( is_array ( $options [ 'label' ] ) ) {
					if ( isset ( $options [ 'label' ] [ 'legend' ] ) ) {
						return ucfirst ( $options [ 'label' ] [ 'legend' ] ) . $required;
					} else {
						return ucfirst ( $this -> _humanize ( $field ) ) . $required;
					}
				}
			} else {
				return ucfirst ( $this -> _humanize ( $field ) ) . $required;
			}
		}


		private function _find_default_value ( $field ) {
			if ( empty ( self::$field_defaults ) ) {
				self::$field_defaults = array ( );
				foreach ( $this->_bridge->{$this -> model}[$this -> table] as $f_item ) {
					self::$field_defaults [ $f_item [ 'Field' ] ] = preg_replace ( '/\(.*\)/', '', $f_item [ 'Default' ] );
				}
			}
			if ( isset ( self::$field_defaults [ $field ] ) ) {
				return self::$field_defaults [ $field ];
			} else {
				return NULL;
			}
		}


		private function _find_field_type ( $field ) {
			if ( empty ( self::$field_types ) ) {
				self::$field_types = array ( );
				foreach ( $this->_bridge->{$this -> model}[$this -> table] as $f_item ) {
					self::$field_types [ $f_item [ 'Field' ] ] = preg_replace ( '/\(.*\)/', '', $f_item [ 'Type' ] );
				}
			}
			if ( isset ( self::$field_types [ $field ] ) ) {
				return self::$field_types [ $field ];
			} else {
				return NULL;
			}
		}


		private function _find_if_required ( $field ) {
			if ( empty ( self::$field_requirements ) ) {
				self::$field_requirements = array ( );
				foreach ( $this->_bridge->{$this -> model}[$this -> table] as $f_item ) {
					self::$field_requirements [ $f_item [ 'Field' ] ] = preg_replace ( '/\(.*\)/', '', $f_item [ 'Null' ] );
				}
			}
			if ( isset ( self::$field_requirements [ $field ] ) ) {
				if ( self::$field_requirements [ $field ] ) {
					return true;
				} else {
					return false;
				}
			} else {
				return NULL;
			}
		}

        private function _script_for_select_inputs($id){
            // INIT
            $script = "if(typeof jQuery == 'function' ){ $(function(){(function ($) { $('#$id').selectpicker();}(jQuery));}); }";
            $wrapper = "<script>[cnt]</script>";
            $return = null;

            // PROCESS
            $return = str_replace("[cnt]", $script, $wrapper);

            // RETURN
            return $return;
        }


	}
