<?php

/**
 *
 * AppController
 *
 * Este controlador puede extender a todos los Controladores, como tal, es
 * opcional, pero los controladores que no lo incluyan deben extender la
 * clase Controller.
 *
 * Sirve como portador de acciones en comùn entre todos los controladores.
 *
 * No puede ser llamado directamente, ya que el controllador app, está
 * restringido.
 *
 * Asì mismo, las funciones beforeAction y beforeTemplate están restringidas para
 * acceso público.
 *
 * @Author Daniel Lepe 2015
 * @Version 2.0
 *
 *
 * # NOTAS:
 * La aplicación actual, hace uso de una copia modificada del componente Security y Backend,
 * con el propósito de que funcione a con el componente Multidepartment.
 */

class AppController extends Controller {
	public $components = array('Menu',
								'Security',
								'Multidepartment',
								'Log');

	public $helpers = array('Menu', 'Form', 'Security', 'Js');

	private static $loginAction = array(
        'context'       => 'admin',
        'controller'    => 'administradores',
        'action'        => 'login');

	public $afterLoginAction = array(
        'controller' => 'administradores',
        'action' => 'miperfil');

	public static $homeAction = array('controller' => 'home');

	public $afterLogoutAction = array(
        'context'       => 'admin',
        'controller'    => 'administradores',
        'action'        => 'login');

	private static $allowedUnloggedActions = array(
        'administradores/login',
        'administradores/forgottenpassword',
        'administradores/passwordbackemailsent',
        'administradores/resetpassword/*');

	public function beforeAction() {
        // VALIDA EL DEPARTAMENTO DESDE SELECCION, SI FUERA ACEQUIBLE.
        $this -> Multidepartment -> user_id = $this -> Session -> user('id');
        $this -> Multidepartment -> check();

        // CONSTRUYE EL ÁRBOL DE PERMISOS.
		$this -> Security -> build_permissions();

		// HAZ ALGO ANTES LA EJECUCIÓN DE CUALQUIER ACCIÓN, DE CUALQUEIR CONTROLADOR.
		$this -> request_login();

		// CONFIGURA EL TEMPLATE A CARGAR
		$this -> _check_template();

		// MANDA LA VERSIÓN DEL CORE AL TEMPLATE PRINCIPAL
		$this -> setTemplate('version', Controller::$coreVersion);
	}

	private function _build_dashboard() {
		// CONSTRUYE EL MENÚ PRINCIPAL VIA COMPONENTE.
		$this -> Menu -> buildMainMenu();
	}

	private function _check_template() {
		if (Routes::is_ajax()) {
			$this -> template = 'admin_ajax';
		} else {
			$this -> _build_dashboard();
		}
	}

	public function beforeTemplate() {
		// HAZ ALGO ANTES DEL LLENADO DE CUALQUIER VISTA, Y CUALQUIER TEMPLATE
	}

	/**
	 * request_login
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */

	public function request_login() {
		// Retifica el estatus de Login.
		if (!$this -> Session -> is_logged()) {
			// Si la acción actual no está permitida en $allowedUnloggedActions, redirecciona
			// a $loginAction
			if (!Routes::request_in_list($this -> request, self::$allowedUnloggedActions)) {
				$this -> go_to(self::$loginAction);
			}
		}
	}

}
