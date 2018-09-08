<?php
/**
 * Clase de Sesion
 *
 * Administra el inicio, edición y baja de variables
 * de la superglobal de Sesión.
 *
 * Además incluye funciones para el Autenticado de usuario
 * y administración de mensajes de sesión de Flash.
 *
 * @Author Daniel Lepe 2014
 * @Version 1.0
 */
class Session {

	public $default_context = 'workspace';
	public $auth_context = 'auth';
	public $flash_context = 'flash';

	function __construct ( ) {
		if ( session_id ( ) === "" ) {
			session_name ( md5 ( realpath ( dirname ( dirname ( __FILE__ ) ) ) ) );

			session_start ( array('cookie_lifetime' => 86400 ));
		}

		// Reescribe el contexto de las sesiones en función del contexto general.
		$this -> check_global_context ( );
	}


	/**
	 * Check Global Context
	 *
	 * Verifica que el logueo sea único para cada contexto de la aplicación, por lo
	 * tanto, si no se especifica un contexto se queda como se ha declarado en public
	 * $auth_context.
	 *
	 * Además, promueve el contexto de Auth, y Flash, con el propósito de que no se
	 * mezclen las salidas de estas funciones a lo largo de los diferentes cambios de
	 * contexto.
	 *
	 * @Author Daniel Lepe 2014
	 *
	 */

	protected function check_global_context ( ) {
		if ( !is_null ( Commons::$context ) ) {
			$this -> default_context = Commons::$context;
			$this -> auth_context = Commons::$context . '_' . $this -> auth_context;
			$this -> flash_context = Commons::$context . '_' . $this -> flash_context;
		}
	}


	public function read ( $nombre, $context = null ) {
		$context = $this -> check_context ( $context );
		if ( isset ( $_SESSION [ $context ] [ $nombre ] ) )
			return $_SESSION [ $context ] [ $nombre ];
		return false;
	}


	public function check ( $nombre, $context = null ) {
		$context = $this -> check_context ( $context );
		return (isset ( $_SESSION [ $context ] [ $nombre ] ));
	}


	public function write ( $nombre, $data, $context = null ) {
		$context = $this -> check_context ( $context );
		if ( !is_string ( $nombre ) )
			die ( 'Error de acceso a sessión, el nombre debe ser cadena de texto.' );
		$_SESSION [ $context ] [ $nombre ] = $data;
		return true;
	}


	public function delete ( $nombre, $context = null ) {
		$context = $this -> check_context ( $context );
		if ( $this -> check ( $nombre, $context ) )
			unset ( $_SESSION [ $context ] [ $nombre ] );
		return true;
	}


	private function check_context ( $context ) {
		if ( $context == null ) {
			return $this -> default_context;
		} else {
			return $context;
		}

	}


	public function clear ( $context = null ) {
		$context = $this -> check_context ( $context );
		if ( isset ( $_SESSION [ $context ] ) )
			unset ( $_SESSION [ $context ] );
		return true;
	}


	public function Auth ( $user_data ) {
		return $this -> write ( 'user', $user_data, $this -> auth_context );
	}


	public function unAuth ( ) {
		return $this -> delete ( 'user', $this -> auth_context );
	}


	public function is_logged ( ) {
		$user = $this -> read ( 'user', $this -> auth_context );

		return ($user) ? true : false;
	}


	public function user ( $field = null ) {
		if ( !$this -> is_logged ( ) )
			return false;

		$user = $this -> read ( 'user', $this -> auth_context );

		if ( is_null ( $field ) )
			return $user;

		if ( !isset ( $user [ $field ] ) )
			return null;

		return $user [ $field ];
	}

	/**
	 * Update
	 *
	 * Actualiza los datos de sesiòn del usuario.
	 *
	 * Se le debe pasar el array completo del usuario para que funcione.
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */

	public function update($data){
		if ( !$this -> is_logged ( ) )
			return false;

		return $this -> write ( 'user', $data, $this -> auth_context );
	}


	/**
	 * Set Flash
	 *
	 * Guarda un mensaje Flash para ser enviado al usuario desde cualquier template
	 * que llame
	 * la funcion flash() de este objeto de Sesion.
	 *
	 * Requiere que se llame la función Session::flash() desde cualquier vista
	 * Para redireccionar requiere que se llame a un Die debido a la posibilidad de
	 * pérdida de mensajes.
	 * 	(uso recomendado Commons::go_to());
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */

	public function set_flash ( $msg, $cnf = NULL ) {
		if ( !empty ( $msg ) ) {

			$msgCollection = array ( );

			if ( $this -> check ( 'msg', $this -> flash_context ) )
				$msgCollection = $this -> read ( 'msg', $this -> flash_context );

			$msgCollection [ ] = array (
				'msg' => $msg,
				'class' => $cnf
			);

			$this -> write ( 'msg', $msgCollection, $this -> flash_context );

			return true;

		} else {

			return false;

		}
	}


	/**
	 * Set Flash Alias
	 *
	 * @Author Daniel Lepe 2014
	 */

	public function setFlash ( $msg, $cnf = NULL ) {
		$this -> set_flash ( $msg, $cnf );
	}


	function has_flash ( ) {
		return $this -> check ( 'msg', $this -> flash_context );
	}


	function flash ( ) {

		if ( $this -> has_flash ( ) ) {
			$return = $this -> read ( 'msg', $this -> flash_context );
			$this -> delete ( 'msg', $this -> flash_context );
			return $return;
		}

		return array();
	}

	function close_session(){
		session_write_close();
	}

	function __destruct ()
	{
		$this -> close_session();
	}

}