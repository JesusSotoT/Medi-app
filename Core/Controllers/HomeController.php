<?php
/**
 * Clase HOME
 *
 * Muestra las actividades principales del Home.
 *
 * @Author Daniel Lepe 2015
 * @Version 1.0
 */
class Home extends AppController {
	public $components =array('Mail');

	public function index() {
        
	}

	public function contact_send ( ) {
		$this -> title = 'contact_send';

		if($this->is_ajax()){
				$response = array();
				$data = $this->data['contacto'];
				if(isset($data['email']) && strlen($data['email']) > 0 && filter_var($data['email'],FILTER_VALIDATE_EMAIL)){
					
					// Construye el correo electrónico.
					
					$this -> Mail -> Mailtemplate -> mailSet('nombre', $data['nombre']);
					$this -> Mail -> Mailtemplate -> mailSet('email', $data['email']);
					$this -> Mail -> Mailtemplate -> mailSet('mensaje', $data['mensaje']);
					
					// Agrega los datos y requisitos para el envío.
					
					$this -> Mail -> Mailtemplate -> setView('contactoMail');
					$this -> Mail -> Mailtemplate -> setMailTemplate('generic');
					$this -> Mail -> Mailtemplate -> render();
					
					$this -> Mail -> subject = 'El formulario de contacto ha sido utilizado';
					$this -> Mail -> agregaDestinatario('info@medicheckapp.com');

					
					// Procesamiento final
					$this -> Mail -> send();

					// Salida JSON
					//$this -> json($this -> Mail -> response);
					
					
					/*$response['class'] = 'success';
					$response['msg'] = 'Su correo a sido enviado';
					$response['url']= $this->url(array('controller'=>'home'));*/
					
				}else{
					$response['class'] = 'warning';
					$response['msg'] = 'Agregue su correo';
				}
			
		}
			$this->json($response);	
	}

}
