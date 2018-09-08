<?php
/**
 * Mail Component
 *
 * Permite el envio de correos electrónicos.
 *
 * Requiere el PLUGIN de PHPMailer.
 *
 * Ahora se puede usar cuantas veces se necesite en el mismo request.
 *
 * @Author Daniel Lepe
 * @Version 1.2
 * @Date 07/09/2015
 */

class MailtemplateComponent extends Component {
	public $models = null;
	public $template = null;
	public $view = null;
	private $vars = array();
	public $body = null;
	private $contents = null;

	/**
	 * setMailTemplate
	 *
	 * Establece el template a rendetizar
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 *
	 */

	public function setMailTemplate($template) {
		if (is_string($template)) {
			$this -> template = $template;
		} else {
			die("Para configurar el template debespasar un array.");
		}
	}

	/**
	 * setView
	 *
	 * Establece la vista que el pluguin va a usar.
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 *
	 */

	public function setView($view) {
		if (is_string($view)) {
			$this -> view = $view;
		} else {
			die("Para configurar el template debespasar un array.");
		}
	}

	/**
	 * mailSet
	 *
	 * Manda una variable al template de vista.
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 *
	 */

	public function mailSet($varName, $data) {
		if (is_string($varName)) {
			$this -> vars[$varName] = $data;
		} else {
			die("Para hacer una variable debes pasar como titulo un string.");
		}
	}

	/**
	 * render
	 *
	 * Llama a que se cargue la vista y los datos del
	 * template seleccionado.
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 *
	 */

	public function render() {
		$this -> validateTemplate();
		$this -> buildView();
		$this -> buildTemplate();
	}

	/**
	 * validateTemplate
	 *
	 * Rectifica que no hagan falta datos neresarios para la
	 * operación del plugin en general
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 *
	 */

	private function validateTemplate() {
		if (!is_string($this ->template)) 
			die("No se puede proceder sin un template");
		if (!is_string($this -> view)) 
			die("No se puede proceder sin una Vista");
	}

	/**
	 * buildView
	 *
	 * Construye la vista con los datos obtenidos. Elimina el BUG que evita
	 * se haga uso de la librería dos veces en el mismo request.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.2
	 * @Date 07/09/2015
	 */

	private function buildView() {
		extract($this -> vars);
		ob_start();
		require ( $this -> path("Views", array('Mailing', "views")) . $this -> view . '.ctp');
		$this -> contents = ob_get_clean();
	}
	
	/**
	 * buildTemplate
	 *
	 * Construye el template con los datos obtenidos. Elimina el BUG que evita
	 * se haga uso de la librería dos veces en el mismo request.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.2
	 * @Date 07/09/2015
	 *
	 */

	private function buildTemplate() {
		ob_start();
		require ( $this -> path("Views", array('Mailing')) . $this -> template . '.ctp');
		$this -> body = ob_get_clean();
	}
	
	/**
	 * __construct
	 *
	 * Permite a mailtemplate hacer uso del helper HTML
	 *
	 * @Author Daniel Lepe 2015
	 * @Version 1.0
	 *
	 */

	public function __construct() {
		parent::__construct();
		
		if(class_exists("HtmlHelper"))
			$this -> Html = new HtmlHelper();
	}
}
