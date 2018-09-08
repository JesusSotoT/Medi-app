<?php
	/**
	 * Clase de enrutamiento
	 *
	 * Routes permite especificar a que lugares se va a apuntar con distintas
	 * composiciónes de URL.
	 *
	 * La función primaria será add_redirect, y se deberá accesar de forma estática.
	 *
	 * La función que verifica la reescritura es rewrite, sólo se le debe de pasar
	 * un parámetro que es el request completo, enviado al archivo index.php por el
	 * HTACCESS. En caso de que exista la reescritura devuelve dicho array, en caso
	 * de que el request contenga más parámetros get, devuelve el array con los
	 * parámetros get arreglados en attrs.
	 *
	 * El controlador que hará uso de esta clase, será APP.inc.php, de igual forma
	 * estática.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.2
	 * @Date 10/08/2015
	 */

	class Routes {

		static $routes = array();
		static $contextsAvailable = array ( );
		static $context;
		static $controller;
		static $action;
		static $getAttrs;
		static $activeroutekey;
		static $language = null;

    public static $pagination = array(
        'page'  => null,
        'oa'    => null,
        'od'    => null
    );
    public static $paginationHASHING = array(
        'page'          => 'p_',    // Allows _ and #
        'order_asc'     => 'oa_',
        'order_desc'    => 'od_',
    );

		/**
		 * Add Redirect
		 *
		 *  Agrega una ruta para redirección, el primer parámetro debe ser un texto
		 * que incluya la URL del enrutamiento a redirigir, mientras que el segundo
		 * deberá ser el arreglo contenedor de la ruta tal cual la debe interpretar la
		 * aplicación.
		 *
		 * Siempre devolverá verdadero a menos que exista algún error, en ese caso morirá
		 * el proceso.
		 *
		 * @Author Daniel Lepe 2014
		 */
		public static function add_redirect ( $route, $destiny ) {
			// Validaciónes
			if ( !is_string ( $route ) or !is_array ( $destiny ) )
				die ( 'La reescritura de ruta es incorrecta' );
			if ( !isset ( $destiny [ 'controller' ] ) or !isset ( $destiny [ 'action' ] ) )
				die ( 'La reescritura de ruta no contiene en el destino controlador o acción válidos' );
			$route = self::clean_request ( $route );
			// Establecer ruta
			self::$routes [ $route ] = array (
				'controller' => $destiny [ 'controller' ],
				'action' => $destiny [ 'action' ],
			);
			// Si se le pasa un contexto, lo establece también
			if ( isset ( $destiny [ 'context' ] ) )
				self::$routes [ $route ] [ 'context' ] = $destiny [ 'context' ];
			return true;
		}

		/**
		 * Add Context
		 *
		 * Agrega un contexto al sistema.
		 *
		 * El contexto en curso, deberá sustitur el contexto en Sessión de Logueo, con la
		 * intención
		 * de que no se mezclen los sistemas de Login.
		 *
		 * @Author Daniel Lepe 2014
		 */
		public static function add_context ( $string, $desc = array() ) {
			if ( empty ( self::$contextsAvailable ) or !isset ( self::$contextsAvailable [ $string ] ) )
				self::$contextsAvailable [ $string ] = $desc;
			return true;
		}

		/**
		 * Clean Route
		 *
		 * Limpa una ruta de request.
		 *
		 * @Author Daniel Lepe 2014
		 */
		public static function clean_request ( $route ) {
			$route = preg_replace ( '/(^\/)|(\/$)/', '', $route );
			$route = preg_replace ( '/[\_]/', '', $route );
			return $route;
		}

		/**
		 * Rewrite
		 *
		 * Analiza el request de parámetros enviados desde un HTACCESS y devuelve el
		 * array formateado para su uso en APP.inc.php
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public static function rewrite ( $request ) {
			if ( !isset ( $request [ 'request' ] ) )
				return array ( 'request' => '/' );
			$request = $request [ 'request' ];
			$request = self::extract_context ( $request );
			$request = self::extract_language ( $request );
			$request = preg_replace ( '/(^\/)|(\/$)/', null, $request ); // LIMPIA INICIOS/FINALES SUCIOS
            $request = self::_extract_pagination_params($request);

			if ( self::is_rewriteable ( $request ) ) {
				self::do_rewrite ( $request );
			} else {
				self::build_rewrite ( $request );
			}

			return array (
				'controller' => self::$controller,
				'action' => self::$action,
				'request' => $request,
				'getAttrs' => self::$getAttrs
			);
		}

    /**
     *
     *
     * Extrae los datos paramétricos que deberán considerarse
     * concretamente a los modelos para paginado directo.
     *
     * @Author Daniel Lepe 2015
     * @Version 1.0
     */
    private static function _extract_pagination_params($request){
        // INIT
        $hash = array();
        $RegEx = str_replace('_', '\_', self::$paginationHASHING['page']);
        $RegEx = str_replace('#', '\#', $RegEx);

        // EXTRAE PÁGINA
        if(preg_match('/\/'. $RegEx .'\d+/', $request, $hash)){
            self::$pagination['page'] = preg_replace('/\/' . $RegEx . '/', null, $hash[0]);
            $request = preg_replace('/\/' . $RegEx . '\d+/', null, $request);
            $request = preg_replace('/\/$/', null, $request);
        }

        // breakpoint($request);

        // RETURN REQUEST
        return $request;
    }

		/**
		 * Get Defaults by Context
		 *
		 * Devuelve los valores por defecto de un contexto de uso de la aplicacion.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public static function get_defaults_by_context ( $context ) {
			if ( isset ( self::$contextsAvailable [ $context ] ) )
				return self::$contextsAvailable [ $context ];
			return null;
		}

		/**
		 * extract_language
		 *
		 * Extrae de un request de URL el lenguaje seleccionado por parte del de usuario
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public static function extract_language ( $request ) {

	    // VALIDA QUE EXISTA O ESTÉ OPERABLE EL MÉTODO DE TRADUCCIÓN DESDE LA CONFIGURACIÓN PARA QUE CONTINÚE CON NORMALIDAD
	    if(!isset(AppConfig::$languagesCFG) or !AppConfig::$languagesCFG['allow_translations'])
	        return $request;

	    // INIT
	    $array_hash = array();  // STORES REGEX MATCH RESULTS.
	    $def = array();         // STORES DEFAULT LANG.
	    $possibleLang = null;   // STORES POSSIBLE LANG.

			// OBTIENE EL POSIBLE CONTEXTO
			if(preg_match( '/^\/?([a-zA-Z|\_|]+)/', $request, $array_hash)
					and isset($array_hash[1]) and !is_null($array_hash[1]))
        $possibleLang = $array_hash[1];


      // VALIDA NO COTEJAR CON IMPOSIBILIDADES
      if(is_null($possibleLang))
          return $request;

      // TRATA DE LOCALIZAR UN LENGUAJE COMPATIBLE
      foreach(AppConfig::$languagesCFG['dictionaries'] as $lang){
        if(strtolower($lang['prefix']) == strtolower($possibleLang)){
          // CARGA DATOS DE LENGUAJE DEFINIDO
          self::$language = $lang;

          // CONVIERTE $possibleLang A REGEX
          $possibleLang = str_replace('_', '\_', $possibleLang);

          // LIMPIEZA DEL REQUEST DEL IDIOMA
          $request = preg_replace("/^\/?" . $possibleLang . "/", null, $request);

          // ELIMINADO DE BASURA
          $request = preg_replace('/\/+/', '/', $request);
        }
      }

      // LIMPIA SALIDA EN CASO DE QUE YA NO HAYA DATOS QUE BOTAR.
			if ( is_null ( $request ) or $request == '' ) $request = '/';

			return $request;
		}

		/**
		 * Extract Context
		 *
		 * Extrae de un request de URL el contexto de usuario
		 *
		 * @Author Daniel Lepe 2014
 		 * @Version 1.0
		 */
		public static function extract_context ( $request ) {
			// OBTIENE EL POSIBLE CONTEXTO
			$possibleContext = preg_replace ( '/(\/.*)/', NULL, $request );

			// SI ES TEXTO LO BUSCA EN LOS CONTEXTOS DISPONIBLES
			if ( !is_null ( $possibleContext ) ) {
				if ( isset ( self::$contextsAvailable [ $possibleContext ] ) ) {
					// Si lo encuentra, lo establece como contexto y limpia el requestdel contexto
					self::$context = $possibleContext;
					$preg = '/^' . self::$context . '/';
					$request = preg_replace ( $preg, NULL, $request );
				}
			}

			if ( is_null ( $request ) or $request == '' )
				$request = '/';

			return $request;
		}

		/**
		 * Do Rewrite
		 *
		 * Reescribe una URL en caso de existir la reescritura.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.3
		 */
		public static function do_rewrite ( $request ) {
			$request = self::clean_request ( $request );

			foreach ( self::$routes[self::$activeroutekey] as $key => $value ) {
				self::${$key} = $value;
			}

			if ( preg_match ( '/\/\*/', self::$activeroutekey ) ) {
				$removeStr = str_replace ( '*', '', self::$activeroutekey );
				$removeStr = str_replace ( '/', '\/', self::$activeroutekey );
				$RegEx = sprintf("/^(%s)\//", $removeStr);
				$attrs = preg_replace ($RegEx, null, $request );
				self::$getAttrs = explode ( '/', $attrs );
			}

		}

		/**
		 * Is Rewritable
		 *
		 * Devuelve verdadero si la request enviada a procesar se encuentra en la lista
		 * de rutas de redireccion.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.2
		 */
		public static function is_rewriteable ( $request ) {
			$request = self::clean_request ( $request );
			self::$activeroutekey = $request;

			if ( isset ( self::$routes [ $request ] ) )
				return true;

			foreach ( self::$routes as $route => $attrs ) {
				self::$activeroutekey = $route;

				$route = str_replace ( '/', '\/', $route );

				$RegEx = sprintf("/^(%s)\/(.*)$/", $route);

				if ( preg_match ( $RegEx, $request ) ){
					return true;
				}

			}

			self::$activeroutekey = NULL;
			return false;
		}

		/**
		 * Build Rewrite
		 *
		 * Construye la estructura de ejecución de la APP desde la URL.
		 *
		 * @Author Daniel Lepe 2014
		 */
		public static function build_rewrite ( $request ) {
			$aGet = explode ( '/', $request );
			foreach ( $aGet as $k => $v ) {
				switch($k) {
					case 0 :
						$v = preg_replace ( '/^(\_|[ ])+/', NULL, $v );
						strtolower ( $v );
						if ( !is_null ( $v ) )
							self::$controller = ucfirst ( $v );
						break;
					case 1 :
						$v = preg_replace ( '/^(\_|[ ])+/', NULL, $v );
						strtolower ( $v );
						if ( !is_null ( $v ) )
							self::$action = $v;
						break;
					default :
						if ( !is_null ( $v ) )
							self::$getAttrs [ ] = $v;
						break;
				}
			}
			return true;
		}

        /**
         * debug_routes
         */
		public static function debug_routes ( ) {
			debug ( self::$routes );
		}


		public static function is_ajax ( ) {
			if ( isset ( $_SERVER [ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower ( $_SERVER [ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) {
				return true;
			}
			return false;
		}


		public static function request_in_list($request, $list){
			foreach ($list as $allowedAction) {
				if (preg_match('/\/\*/', $allowedAction)) {
					$allowedAction = str_replace('/*', '', $allowedAction);
					$allowedAction = str_replace('_', '\_', $allowedAction);
					$allowedAction = str_replace('/', '\/', $allowedAction);
					$REGEX = sprintf('/^(%s)/', $allowedAction);
					if(preg_match($REGEX, $request)){
						return true;
					}
				}
				if($allowedAction == $request){
					return true;
				}
			}
			return false;
		}


	}
