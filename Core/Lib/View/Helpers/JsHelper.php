<?php

	class JsHelper extends Helper {

		public static $cache = array ( );

		public function __construct ( ) {
			$this -> Html = new HtmlHelper ( );
		}


		public function _replace_parallelActions ( $script, $options ) {
			foreach ( array('after', 'before', 'success', 'error') as $replacment_option ) {
				$replacement = "";
				if ( isset ( $options [ $replacment_option ] ) ) {
					$replacement = $options [ $replacment_option ];
				}
				$lookup_string = sprintf ( "/*[%s_ACTIONS]*/", strtoupper ( $replacment_option ) );
				$script = str_replace ( $lookup_string, $replacement, $script );
			}
			return $script;
		}


		public function _ajax_link_bootstrap ( $url, $options, $modal_id, $msg ) {
			$id = uniqid ( );
			ob_start ( );
			readfile ( LIBRARY_PATH . DS . 'ajax_snippets' . DS . 'modal_link_script.js' );
			$script = ob_get_clean ( );
			$script = str_replace ( '[link_id]', $id, $script );
			$script = str_replace ( '[modal_id]', $modal_id, $script );
			$script = str_replace ( '[msg]', $msg, $script );
			$script = $this -> _replace_parallelActions ( $script, $options );
			self::$cache [ 'scripts' ] [ ] = $script;
			return $id;
		}


		public function request ( $options, $cache = false ) {

			if ( !is_array ( $options ) or !isset ( $options [ 'url' ] ) )
				throw new Exception ( 'Debe ser un array que contenga el [url] => "URL", y [update] => "#element"', 1 );

			if ( is_array ( $options [ 'url' ] ) )
				$options [ 'url' ] = $this -> url ( $options [ 'url' ] );

			$options [ 'url' ] = preg_replace ( '/\//', '\/', $options [ 'url' ] );

			$request = $this -> _get_request_body ( $options );

			if ( $cache ) {
				$cache [ 'scripts' ] [ ] = $request;
				return true;
			} else {
				$return = "<script>" . $request . "</script>";
				return $return;
			}
		}


		public function _get_request_body ( $options = array() ) {
			if ( !isset ( $options [ 'url' ] ) or !isset ( $options [ 'update' ] ) )
				throw new Exception ( 'No están definidos los parámetros solicitados: $options [ "url" ] | $options [ "update" ]!', 1 );

			$script = "$.ajax({
						url: '[url]',
						cache: [cache],
						success: function(data) {
							$('[update]').html(data);
							/*[SUCCESS_ACTIONS]*/
						},
						beforeSend: function() {
							/*[BEFORE_ACTIONS]*/
						},
						complete: function() {
							/*[AFTER_ACTIONS]*/
						},
						error: function() {
							/*[ERROR_ACTIONS]*/
						}
					});";

			$functional_wrappers = array (
				'[url]' => 'url',
				'[update]' => 'update',
				'[cache]' => 'cache',
				'/*[BEFORE_ACTIONS]*/' => 'before',
				'/*[SUCCESS_ACTIONS]*/' => 'success',
				'/*[AFTER_ACTIONS]*/' => 'after',
				'/*[ERROR_ACTIONS]*/' => 'error'
			);

			foreach ( $functional_wrappers as $key_search => $possible_option ) {
				if ( !isset ( $options [ $possible_option ] ) ) {
					$options [ $possible_option ] = "";

					if ( $possible_option == 'cache' )
						$options [ $possible_option ] = "false";
				}

				$options [ $possible_option ] = preg_replace ( '/<\/{0,1}script>/mi', '', $options [ $possible_option ] );

				$script = str_replace ( $key_search, $options [ $possible_option ], $script );
			}
			$script = preg_replace ( '/\n|[ ]{1,5}|\t/mi', ' ', $script );
			$script = preg_replace ( '/\n|[ ]{1,5}|\t/mi', ' ', $script );

			return $script;
		}


		public function link ( $title, $url, $options = array(), $msg = null ) {
			$options [ 'id' ] = $this -> _ajax_link ( $url, $options, $msg );
			return $this -> Html -> link ( $title, $url, $options );
		}


		private $modal_default = array (
			'options' => array (
				'class' => 'btn btn-default btn-sm', // Aplica en el link activador
				'link_id' => NULL, // Aplica en el link activador
				'id' => NULL, // Aplica en el Diálogo
				'title' => NULL, // Aplica en el Diálogo
				'size' => NULL, // NULL, sm, lg
				'callback' => NULL	// NULL, STR, ARRAY('url' => '', 'target' => '')
			),
			'buttons' => array (
				'close' => 'Cerrar',
				'ok' => array (
					'class' => 'btn-primary',
					'title' => 'Aplicar',
					'id' => false
				)
			)
		);

		/**
		 * build_buttons_for_modal_dialog
		 *
		 * Devuelve el Dialog modal en un HTML;
		 * Ahora permite bloquear algún botón de los 2 predefinidos estableciendolo como nulo.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.1
		 */

		private function build_buttons_for_modal_dialog ( ) {
			$return = NULL;
			$dismissStr = 'data-dismiss="modal"';
			$btnStr = '<button type="button" class="btn %s" id="%s" %s>%s</button>';
			foreach ( $this -> modal['buttons'] as $k => $b ) {
				if ( is_string ( $b ) ) {
					$title = $b;
					$class = 'btn-default';
					$id = str_replace ( ' ', '_', $this -> modal [ 'options' ] [ 'id' ] . $k );
				} elseif ( is_array ( $b ) ) {
					$title = (isset ( $b [ 'title' ] )) ? $b [ 'title' ] : str_replace ( '_', ' ', ucfirst ( $k ) );
					$class = (isset ( $b [ 'class' ] )) ? $b [ 'class' ] : 'btn-default';
					$id = (isset ( $b [ 'id' ] ) and $b [ 'id' ]) ? $b [ 'id' ] : str_replace ( ' ', '_', $this -> modal [ 'options' ] [ 'id' ] . $k );
				}
				if(!is_null($b)){
					$dismiss = ($k == 'close') ? $dismissStr : NULL;
					$return .= sprintf ( $btnStr, $class, $id, $dismiss, $title );	
				}
			}
			return $return;
		}


		/**
		 * Build Modal Body
		 *
		 * Devuelve un String (HTML) con el body del Modal.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		private function build_modal_body ( $buttons ) {
			// Configuración dinámica del tamaño
			$modal_size = (is_null ( $this -> modal [ 'options' ] [ 'size' ] )) ? NULL : 'modal-' . $this -> modal [ 'options' ] [ 'size' ];
			// Configuración del Wrapper
			$wrapperStr = '<div class="modal fade" id="[modal:id]" tabindex="-1" role="dialog" aria-labelledby="[modal:id]Label" aria-hidden="true" data-backdrop="static">
							  <div class="modal-dialog [modal:size]">
							    <div class="modal-content">
									[modal:header]
									[modal:body]
							      	[modal:footer]
							    </div>
							  </div>
							</div>';
			// Configuración del Header
			$headerStr = '<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">
									<span aria-hidden="true">&times;</span><span class="sr-only">[close]</span>
								</button>
								<h4 class="modal-title" id="[modal:id]Label">[modal:title]</h4>
							</div>';
			// Configuración del Cuerpo
			$bodyStr = '<div class="modal-body">[modal:content]</div>';
			// Configuración del Footer
			$footerStr = '<div class="modal-footer">[modal:buttons]</div>';
			// Consutrccion general.
			$return = str_replace ( '[modal:header]', $headerStr, str_replace ( '[modal:body]', $bodyStr, str_replace ( '[modal:footer]', $footerStr, $wrapperStr ) ) );
			// Reemplazos
			$return = str_replace ( '[modal:size]', $modal_size, $return );
			$return = str_replace ( '[modal:id]', $this -> modal [ 'options' ] [ 'id' ], $return );
			$return = str_replace ( '[modal:buttons]', $buttons, $return );
			$return = str_replace ( '[modal:title]', $this -> modal [ 'options' ] [ 'title' ], $return );
			// Finaliza
			return $return;
		}


		/**
		 * Build Modal Callback
		 *
		 * Construye el Callback del modal
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		private function build_modal_callback ( ) {
			if ( is_string ( $this -> modal [ 'options' ] [ 'callback' ] ) )
				// Procesa el callback como funcion de Javascript
				$this -> modal [ 'options' ] [ 'callback' ] .= '();';

			if ( is_array ( $this -> modal [ 'options' ] [ 'callback' ] ) ) {
				$c = $this -> modal [ 'options' ] [ 'callback' ];
				if ( !isset ( $c [ 'target' ] ) )
					die ( 'Un callback de actualización de AJAX requiere un target en el callback.' );
				if ( !isset ( $c [ 'url' ] ) )
					die ( 'Un callback de actualización de AJAX requiere un url en el callback.' );
				$c [ 'url' ] = preg_replace ( '/\//', '\/', $this -> url ( $c [ 'url' ] ) );
				$callback = "$.ajax({
					url:'[url]',
					success: function (callback){
						$('[target]').html(callback);
					},error: function(){
						$('[target]').html('¡Error cargando el Callback ([url])!');
					}});";
				$callback = str_replace ( '[url]', $c [ 'url' ], $callback );
				$callback = str_replace ( '[target]', $c [ 'target' ], $callback );
				$this -> modal [ 'options' ] [ 'callback' ] = $callback;
			}
		}


		/**
		 * Build Modal Dialog
		 *
		 * Devuelve el Dialog modal en un HTML;
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		private function build_modal_dialog ( ) {
			$buttons = $this -> build_buttons_for_modal_dialog ( );
			self::$cache [ 'bodys' ] [ ] = $this -> build_modal_body ( $buttons );
		}


		/**
		 * build_modal_link
		 *
		 * Devuelve el HTML del link que activará al modal en curso.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		private function build_modal_link ( $title, $url ) {
			$linkStr = '<a href="[link:url]" class="[link:class]" data-target="#[modal:id]" id="[link:id]">
						  [link:title]
						</a>';
			$linkStr = str_replace ( '[modal:id]', $this -> modal [ 'options' ] [ 'id' ], $linkStr );
			$linkStr = str_replace ( '[link:class]', $this -> modal [ 'options' ] [ 'class' ], $linkStr );
			$linkStr = str_replace ( '[link:id]', $this -> modal [ 'options' ] [ 'link_id' ], $linkStr );
			$linkStr = str_replace ( '[link:title]', $title, $linkStr );
			$linkStr = str_replace ( '[link:url]', $url, $linkStr );
			return $linkStr;
		}
		
		/**
		 * build_modal_link_anidado
		 *
		 * Devuelve el HTML del link que activará al modal en curso.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		private function build_modal_link_anidado ( $title, $url ) {
			$linkStr = '<a href="javascript:[link:id](\'[link:url]\');" class="[link:class]" data-target="#[modal:id]" id="[link:id]">
						  [link:title]
						</a>';
			$linkStr = str_replace ( '[modal:id]', $this -> modal [ 'options' ] [ 'id' ], $linkStr );
			$linkStr = str_replace ( '[link:class]', $this -> modal [ 'options' ] [ 'class' ], $linkStr );
			$linkStr = str_replace ( '[link:id]', $this -> modal [ 'options' ] [ 'link_id' ], $linkStr );
			$linkStr = str_replace ( '[link:title]', $title, $linkStr );
			$linkStr = str_replace ( '[link:url]', $url, $linkStr );
			return $linkStr;
		}

		/**
		 * build_modal_script
		 *
		 * Devuelve el Script específico para un link de modal, este script, incluye un
		 * fragmento de Jquery que automatiza el Submit con el botòn de OK por defecto
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		private function build_modal_script ( ) {
			$script = "
				$('#[link:id]').on('click', function (e) {
					e.preventDefault();
					$.ajax({
						url: $(this).attr('href'),
						success: function (data){
							$('#[modal:id] .modal-body').html(data);
							$('#[modal:id] .submit').addClass('hidden');
							$('#[modal:id]').modal('show');
							$('#[modal:id]ok').text($('#[modal:id] BUTTON[type=submit]').text());
							$('#[modal:id]ok').off('click');
							$('#[modal:id]ok').on('click', function () {
								$('#[modal:id] BUTTON[type=submit]').click();
							});
						}
					});
					$('#[modal:id]').on('hidden.bs.modal', function (e) {
						alert('hide');
						$('#[modal:id] .modal-body').html('');
					  	[modal:callback]
					});
				});
			";
			$script = str_replace ( '[link:id]', $this -> modal [ 'options' ] [ 'link_id' ], $script );
			$script = str_replace ( '[modal:id]', $this -> modal [ 'options' ] [ 'id' ], $script );
			$script = str_replace ( '[modal:callback]', $this -> modal [ 'options' ] [ 'callback' ], $script );
			self::$cache [ 'scripts' ] [ ] = $script;
		}
		
		/**
		 * build_modal_script_anidado
		 *
		 * Devuelve el Script específico para un link de modal, este script, incluye un
		 * fragmento de Jquery que automatiza el Submit con el botòn de OK por defecto
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		private function build_modal_script_anidado ( ) {
			$script = "
				 function [link:id] ( link ) {
					$.ajax({
						url: link,
						success: function (data){
							$('#[modal:id] .modal-body').html(data);
							$('#[modal:id] .submit').addClass('hidden');
							$('#[modal:id]').modal('show');
							$('#[modal:id]ok').text($('#[modal:id] BUTTON[type=submit]').text());
							$('#[modal:id]ok').off('click');
							$('#[modal:id]ok').on('click', function () {
								$('#[modal:id] BUTTON[type=submit]').click();
							});
						}
					});
					$('#[modal:id]').on('hide.bs.modal', function (e) {
						$('#[modal:id] .modal-body').html('');
					  	[modal:callback]
					});
				}
			";
			$script = str_replace ( '[link:id]', $this -> modal [ 'options' ] [ 'link_id' ], $script );
			$script = str_replace ( '[modal:id]', $this -> modal [ 'options' ] [ 'id' ], $script );
			$script = str_replace ( '[modal:callback]', $this -> modal [ 'options' ] [ 'callback' ], $script );
			self::$cache [ 'scripts' ] [ ] = $script;
		}

		/**
		 * Modal Link
		 *
		 * Imprime el código necesario para exportar un Modal Link de Bootstrap.
		 *
		 * El primer parámtro que acepta es el del titulo de la liga, que puede ser
		 * personalizable via $options['class'] u $options['id].
		 *
		 * El segundo parámetro es $url, donde se debe de pasar un array, para que sea
		 * formateado através de $this -> url(), o un string.
		 *
		 * El Tercer parámetro debe ser un array opcional con los siguientes posibles
		 * modificadores:
		 * 	class:STR 						Cadena de texto que irá directamente a formatear el link
		 * 									generado.
		 *  id:STR							Cadena de texto que recibirá el Diálogo modal.
		 *  size:STR(short|medium|long)		Tamaño del Diálogo modal a cargar
		 * 	title:STR						Título del Diálogo Modal, si está vacío, tomará el primer H1
		 * 									que localice.
		 *  callback:STR:ARRAY				Parámetros llamados cuando el modal sea cerrado, si se
		 * 									le pasa un str, asumirá que es una función de javascript,
		 * 									si es un array, buscará las claves target y url para actualizar un
		 * 									elemento via ajax
		 *
		 * El Cuarto y último parámetro, también opcional, configura los botónes, cada
		 * clave es un botón, se puede agregar ya sea un String o un array con un
		 * arreglo personalizado para el botón.
		 *
		 *  buttons:[						Organizador de botones
		 * 		'close' 	=> 'Cerrar'		Botón y título de cerrado, colocar FALSE para no
		 * agregar botón de cerrado al diálogo
		 * 		'aceptar'	=> [
		 * 			'class' => STR			Cadena de texto que agrega clases a este boton.
		 * 			'id'	=> STR			Cadena de texto que agrega ids a este boton.
		 * 			'title'	=> STR			Cadena de texto que agrega un título a este botón.
		 * 	]
		 *
		 * Version 1.1
         * Ahora agreaga un refresher a cada request.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.1
		 */

		public function modal_link ( $title, $url, $options = array(), $buttons = array() ) {
			$this -> modal = $this -> modal_default;
			// Validations
			$this -> validate_modal_params ( $title, $options, $buttons );
			// Customizations
			$this -> customize_modal ( $title, $options, $buttons );
			// Procesa los Callbacks
			$this -> build_modal_callback ( );
			// Obtiene el Cuerpo del Modal:
			$this -> build_modal_dialog ( );
			// Obtiene el Script:
			$this -> build_modal_script_anidado ( );
			// Obtiene el Link activador del Modal
			return $this -> build_modal_link_anidado( $title, $this -> url ( $url, true, true ) );
		}


		/**
		 * Modal Link (ALIAS)
		 *
		 * Alias de modal_link();
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function modalLink ( $title, $url, $options = array(), $buttons = array() ) {
			return $this -> modal_link ( $title, $url, $options, $buttons );
		}


		/**
		 * Customize Modal
		 *
		 * Personaliza los parámetros del modal con la informaciòn contenida en $options
		 * y $buttons desde la funcion modal_link.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		private function customize_modal ( $title, $options, $buttons ) {
			foreach ( $this -> modal['options'] as $k => $v ) {
				if ( isset ( $options [ $k ] ) )
					$this -> modal [ 'options' ] [ $k ] = $options [ $k ];
			}
			if ( !empty ( $buttons ) ) {
				$this -> modal [ 'buttons' ] = $buttons;
			}
			if ( is_null ( $this -> modal [ 'options' ] [ 'id' ] ) )
				$this -> modal [ 'options' ] [ 'id' ] = uniqid ( 'modal' );
			if ( is_null ( $this -> modal [ 'options' ] [ 'link_id' ] ) )
				$this -> modal [ 'options' ] [ 'link_id' ] = uniqid ( 'link' );
			if ( is_null ( $this -> modal [ 'options' ] [ 'title' ] ) )
				$this -> modal [ 'options' ] [ 'title' ] = $title;
			if ( isset ( $options [ 'class' ] ) and !$options [ 'class' ] )
				$this -> modal [ 'options' ] [ 'class' ] = NULL;
		}


		/**
		 * Validate Modal Params
		 *
		 * Recitifica los parámetros que se le han pasado a modal.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		private function validate_modal_params ( $title, $options, $buttons ) {
			if ( !is_string ( $title ) )
				die ( '$title debe ser string.' );
			if ( !is_array ( $options ) )
				die ( '$options debe ser array.' );
			if ( !is_array ( $buttons ) )
				die ( '$buttons debe ser array.' );
			return true;
		}


		public function btnLink ( $title, $url, $class = null, $icon = 'entypo-right-dir', $options = array() ) {

			if ( isset ( $url [ 'update' ] ) ) {
				$options [ 'update' ] = $url [ 'update' ];
				unset ( $url [ 'update' ] );
			} else {
				die ( 'Debes pasar el parámetro update en el Array de URL para rellenar un nodo con el resultado del request, o puedes referenciar a una funcion de javascript por su nombre. ' );
			}

			$options [ 'id' ] = $this -> _ajax_link ( $url, $options );

			return $this -> Html -> btnLink ( $title, $url, $class, $icon, $options );
		}


		public function release_cached ( ) {

			if ( isset ( self::$cache [ 'bodys' ] ) ) {
				if ( $this -> is_ajax ( ) ) {
					echo "<script>$(function(){";
					foreach ( self::$cache['bodys'] as $modal_bodys ) {
						echo sprintf ( "$('body').append('%s');", str_replace ( "'", "\'", preg_replace ( '/(\s{2,9}|\t|\n)/', ' ', $modal_bodys ) ) );
					}
					echo "});</script>";
				} else {
					foreach ( self::$cache['bodys'] as $modal_bodys ) {
						echo $modal_bodys;
					}
				}

			}

			if ( isset ( self::$cache [ 'scripts' ] ) ) {
				echo "<script>";
				foreach ( self::$cache['scripts'] as $script ) {
					echo $script;
				}
				echo "</script>";
			}
		}


		public function _ajax_link ( $url, $options, $msg = null ) {

			$id = uniqid ( );
			$update_target = NULL;
			if(!is_null($msg)){
				$msg = "
					if(!confirm('$msg')){ return false; }
				";
			}

			if ( isset ( $options [ 'update' ] ) ) {
				$update_target = $options [ 'update' ];
				if ( preg_match ( '/^(\#|\.)/', $update_target ) ) {
					// Si es Nodo, el Script de ajax mandará al ID o clase especificados el HTML.
					$target_is_node = 'true';
					$action = '$(update_target).html(data);';
				} else {
					// De otra manera, tratará de mandarle el resultado como si fuera una funciòn
					// declarada.
					$target_is_node = 'false';
					$action = $update_target . '(data);';
				}
			} else {
				die ( 'Se debe pasar el parámetro Update' );
			}

			$script = "$('#[link_id]').bind('click', function(event) {
							event.preventDefault();

							$msg

							target_isNode = [target_is_node];
							update_target = '[update_target]';

							if(target_isNode){
								$(update_target).html('');
							}

							var target_url = $(this).attr('href');

							$.ajax({
								url: target_url,
								cache: false,
								data: {update_target_id: '[update_target_id]'},
								success: function(data) {

									[action]

									/*[SUCCESS_ACTIONS]*/
								},
								beforeSend: function() {
									/*[BEFORE_ACTIONS]*/
								},
								after: function() {
									/*[AFTER_ACTIONS]*/
								},
								error: function(data) {
									console.log(data);
									/*[ERROR_ACTIONS]*/
								}
							});
						});";

			$script = str_replace ( '[link_id]', $id, $script );

			$script = str_replace ( '[update_target]', $update_target, $script );

			$script = str_replace ( '[target_is_node]', $target_is_node, $script );

			$script = str_replace ( '[action]', $action, $script );

			self::$cache [ 'scripts' ] [ ] = $script;

			return $id;
		}


	}
