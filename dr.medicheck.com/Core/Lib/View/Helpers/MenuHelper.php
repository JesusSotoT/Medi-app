<?php

	class MenuHelper extends Helper {

		protected $actionMenuUlClass = "actionList";
		protected $actionsStack = array ( );
		public $Security;
		// Required

		/**
		 * Add Modal Action
		 *
		 * Una vez que ha validado el objecto de JS, llena la pila de acciones con
		 * compatibilidad de Modal de JsHelper.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function addModalAction ( $title, $icon = NULL, $url, $options = array(), $buttons = array() ) {

			if ( !$this -> request_js_object ( ) )
				die ( 'La función addModalAction requiere que se llame el helper JsHelper' );

			if ( is_null ( $icon ) )
				$icon = 'entypo-cog';

			if ( preg_match ( '/^glyphicon\-/', $icon ) )
				$icon = 'glyphicon ' . $icon;

			if ( preg_match ( '/^fa\-/', $icon ) )
				$icon = 'fa ' . $icon;

			$options [ 'class' ] = false;

			$this -> actionsStack [ ] = array ( 'url' => $this -> Js -> modal_link ( '<i class="' . $icon . '"></i>' . $title, $url, $options, $buttons ) );
		}


		/**
		 * Add  Action
		 *
		 * Manda a la pila de acciones una liga con el contenido.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function addAction ( $title, $icon = NULL, $url ) {
			if ( is_null ( $icon ) )
				$icon = 'entypo-cog';

			if ( preg_match ( '/^glyphicon\-/', $icon ) )
				$icon = 'glyphicon ' . $icon;

			if ( preg_match ( '/^fa\-/', $icon ) )
				$icon = 'fa ' . $icon;

			if ( isset ( $url [ 'confirm' ] ) ) {
				$confirm = sprintf ( " onclick='return confirm(\"%s\")' ", $url [ 'confirm' ] );
				unset ( $url [ 'confirm' ] );
			} else {
				$confirm = NULL;
			}

			$this -> actionsStack [ ] = array ( 'url' => sprintf ( '<a %s href="%s" ><i class="%s"></i> %s </a>', $confirm, $this -> url ( $url ), $icon, $title ), );
		}


		/**
		 * Rquest JS Object
		 *
		 * Solicita el objecto Js, si ya existe devuelve verdadero, si no existe pero
		 * existe la clase lo crea y devuelve verdadero, de lo contrario, finaliza en
		 * error.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function request_js_object ( ) {
			if ( isset ( $this -> Js ) and is_object ( $this -> Js ) )
				return true;
			if ( class_exists ( 'JsHelper' ) ) {
				$this -> Js = new JsHelper ( );
				return true;
			}
			return false;
		}


		/**
		 * Check Actions
		 *
		 * Devuelve verdadero si existen acciones en la pila de Action Stacks.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public function check_actions ( ) {
			return( !empty ( $this -> actionsStack ) ) ? TRUE : FALSE ;
		}


		/**
		 * Action Menu
		 *
		 * Imprime el menú de acciones.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function actionMenu ( ) {
			if(!$this -> check_actions())
				return null;

			$return = sprintf ( '<ul id="%s">', $this -> actionMenuUlClass );

			foreach ( $this -> actionsStack as $a ) {
				$return .= sprintf ( '<li> %s </li>', $a [ 'url' ] );
			}

			$return .= sprintf ( '</ul>' );

			return $return;
		}


		public function make_menu_list ( $array, $attrs_first_element = array(), $afterOpenElelments = array() ) {
            
			if ( !is_array ( $array ) )
				return null;

			$attrs = '';

			if ( !empty ( $attrs_first_element ) ) {
				foreach ( $attrs_first_element as $attr => $value ) {
					if ( $attrs != '' )
						$attrs .= ' ';
					$attrs .= sprintf ( "%s='%s'", $attr, $value );
				}
			}

			$str = "<ul $attrs >";

			if ( !empty ( $afterOpenElelments ) ) {

				foreach ( $afterOpenElelments as $element => $values ) {
					ob_start ( );
					$this -> element ( 'admin/' . $element, $values );
					$str .= ob_get_clean ( );
				}

			}
            
			foreach ( $array as $element ) {

				if ( $this -> Security -> hasPermission ( $element [ 'permisos_clave' ] ) ) {
					if ( is_null ( $element [ 'glyphicon' ] ) )
						$element [ 'glyphicon' ] = 'entypo-dot';

					if ( is_null ( $element [ 'url' ] ) )
						$element [ 'url' ] = '';

					$str .= '<li>';
                    $lang = null;   // STORES THE POSSIBLE LANGUAGE PREFIX
                    
                    if(class_exists('Translate')){
                        $lang = '/' . Translate::get_prefix();
                        $element [ 'titulo' ] = i($element [ 'titulo' ]);
                    }
                    
					$str .= sprintf ( '<a href="%s"><i class="%s"></i><span>%s</span></a>', $this -> url ( Commons::$context . $lang . '/' . $element [ 'url' ] ), $element [ 'glyphicon' ], $element [ 'titulo' ] );

					if ( isset ( $element [ 'children' ] ) and !empty ( $element [ 'children' ] ) )
						$str .= $this -> make_menu_list ( $element [ 'children' ] );

					$str .= '</li>';
				}

			}

			$str .= '</ul>';

			return $str;
		}


	}
