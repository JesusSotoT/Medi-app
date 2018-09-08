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
	 * # Importante:
	 *
	 * No puede ser llamado directamente, ya que el controllador app, està
	 * restringido.
	 *
	 * Asì mismo, las funciones beforeAction y beforeTemplate están restringidas para
	 * acceso pùblico.
	 *
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 *
	 */

	class AppController extends Controller {

		public $components = array();

		public $helpers = array('Form', 'Js');

		public function beforeAction ( ) {
			//
		}

		public function beforeTemplate ( ) {
			$this -> _check_template();
		}

		private function _check_template() {
			if (Routes::is_ajax())
				$this -> template = 'ajax';
		}
	}
