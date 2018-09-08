<?php

/**
 * Mail Component
 *
 * Permite el envio de correos electrónicos.
 *
 * Requiere el PLUGIN de PHPMailer.
 *
 * @Author Daniel Lepe 2014
 * @Version 1.1
 * @Date 14/09/2015
 */

class MailComponent extends Component {
	public $models = null;
	public $components = array('Mailtemplate');
	public $plugins = array('Phpmailer');
	public $response = array('status' => null, 'msg' => null, 'error' => null, );
	public $subject = null;
	public $body = null;

	private $destinatarios = array();
	private $adjuntos = array();
	private $paraRespusta = array();

	/**
	 * agregaDestinatario
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	public function agregaDestinatario($destinatario) {
		$this -> destinatarios[] = $destinatario;
	}

	/**
	 * agregarParaRespuesta
	 *
	 * Recibe un correo electrónico y lo coloca en el HEADER para replay-to
	 *
	 * @Author Daniel Lepe
	 * @Version 1.0
	 */

	public function agregarParaRespuesta($correo) {
		$this -> paraRespusta[] = $correo;
	}

	/**
	 * agregaAdjunto
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */

	public function agregaAdjunto($ubicacion, $nombre = null) {
		if (!is_string($ubicacion))
			die('La ubicacion del archivo debe ser un STRING que apunte a su ubicación en el servidor');

		if (!is_null($nombre) and !is_string($nombre))
			die('El nombre debe ser un string, que incluya el prefijo del nombre o NULL');

		$this -> adjuntos[] = array('file' => $ubicacion, 'name' => $nombre);
	}

	/**
	 * send
	 *
	 * Envia un correo electrónico. Al finalizar, limpia el entorno.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.1
	 */

	public function send() {
		// VALIDA EL CONTENIDO DE MAILTEMPLATE BODY
		if(!is_null($this -> Mailtemplate -> body))
			$this -> body = $this -> Mailtemplate -> body;
		
		// RECTIFICA QUE SI EXISTAN DESTIANTARIOS
		if(empty($this -> destinatarios))
			die('No se puede enviar correos sin destinatarios.');		
		
		// PROCESA
		if (self::$smtpMailing) {
			$this -> sendSmtp();
		} else {
			$this -> sendmail();
		}
		
		// LIMPIEZA AUTOMÁTICA
		$this -> clear_plugin();
	}
	
	/**
	 * clear_plugin
	 *
	 * Limpia el plugin general para empezar de nuevo.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.1
	 */
	
	public function clear_plugin () {
		
		// AL FINALIZAR LIMPIA VARIABLES LOCALES
		$this -> destinatarios = array();
		$this -> adjuntos = array();
		$this -> paraRespusta = array();
		$this -> body = null;
		$this -> Mailtemplate -> body = null;
		
		// LIMPIA EL PLUGIN
		$this -> Phpmailer -> clearAddresses ();
		$this -> Phpmailer -> clearCCs ();
		$this -> Phpmailer -> clearBCCs ();
		$this -> Phpmailer -> clearReplyTos ();
		$this -> Phpmailer -> clearAllRecipients ();
		$this -> Phpmailer -> clearAttachments ();
		$this -> Phpmailer -> clearCustomHeaders ();
	}

	/**
	 * sendmail
	 *
	 * Envia un correo electrónico via php sendmail
	 *
	 * @Author Daniel Lepe
	 * @Version 1.0
	 */

	private function sendmail( ) {
		foreach($this -> destinatarios as $destinatario){
			$sendResult = mail($destinatario, $this -> subject, $this -> body, $this -> sendmailCabeceras());		
			if(!$sendResult){
				$this -> response['error'] = $sendResult;
				$this -> response['status'] = false;
				$this -> response['msg'] = 'Ha ocurrido un error enviando el correo electrónico a: ' . $destinatario . PHP_EOL;
				return false;
			}
		}
		$this -> response['status'] = true;
		$this -> response['msg'] = 'Enviado(s)';
		return true;
	}
	
	/**
	 * sendmailCabeceras
	 * 
	 * Genera las cabeceras para el protocolo de php SENDMAIL
	 * 
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	
	private function sendmailCabeceras(){
		// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
		$cabeceras = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=' . self::$smtpConfig['charset'] . "\r\n";
		$cabeceras .= 'From: ' . self::$smtpConfig['remitenteEmail'] . "\r\n";
		return $cabeceras;
	}

	/**
	 * sendSmtp
	 *
	 * Envia via STMP el correo electrónico construido.
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */

	private function sendSmtp() {
		// Configura Conexión.
		$this -> configureSmtp();

		// Configura Destiantarios, Adjuntos y Remitentes.
		$this -> smtpConstruyeDestinatarios();
		$this -> smtpConstruyeAdjuntos();
		$this -> smtpAgregaCorreosParaRespuesta();

		// Construye el Correo.
		$this -> smtpConstruyeCorreo();

		// Envia el Correo.
		return $this -> smtpProcesaEnvio();
	}

	/**
	 * smtpProcesaEnvio
	 *
	 * @Author Daniel Lepe 2014
	 * @version 1.0
	 */

	private function smtpProcesaEnvio() {
		$this -> response['status'] = $this -> Phpmailer -> send();
		if (!$this -> response['status']) {
			$this -> response['msg'] = 'Ha ocurrido un error';
			$this -> response['error'] = $this -> Phpmailer -> ErrorInfo;
		} else {
			$this -> response['msg'] = 'Correo enviado';
		}
		return $this -> response['status'];
	}

	/**
	 * smtpConstruyeCorreo
	 *
	 * @Author Daniel Lepe 2014
	 * @version 1.0
	 */
	 
	private function smtpConstruyeCorreo() {
		$this -> Phpmailer -> isHTML(true);
		$this -> Phpmailer -> Subject = $this -> subject;
		$this -> Phpmailer -> Body = $this -> body;
		$this -> Phpmailer -> AltBody = strip_tags($this -> body);
	}

	/**
	 * smtpConstruyeAdjuntos
	 *
	 * Agrega los adjuntos al correo.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.0
	 */
	private function smtpConstruyeAdjuntos() {
		if (!empty($this -> adjuntos)) {
			foreach ($this -> adjuntos as $adj) {
				$this -> Phpmailer -> addAttachment($adj['file'], $adj['name']);
			}
		}
	}

	/**
	 * smtpConstruyeDestiantarios
	 *
	 * Confiugra los destinatarios para la clase SMTP.
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	private function smtpConstruyeDestinatarios() {
		if (empty($this -> destinatarios))
			die('No se puede enviar correos sin destinatarios, usa la funcion $this -> Mail -> agregaDestinatario en tu controlador.');

		foreach ($this -> destinatarios as $key => $destinatario) {
			$this -> Phpmailer -> addAddress($destinatario);
		}
	}

	/**
	 * smtpAgregaCorreosParaRespuesta
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */

	private function smtpAgregaCorreosParaRespuesta() {
		if (empty($this -> paraRespusta))
			return true;
		foreach ($this -> paraRespusta as $replayTo) {
			$this -> Phpmailer -> addReplyTo($replayTo);
		}
		return true;
	}

	/**
	 * configureSmtp
	 *
	 * Configura las cabeceras para el envio.
	 *
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	private function configureSmtp() {
		$this -> Phpmailer -> isSMTP();

		// Set mailer to use SMTP
		$this -> Phpmailer -> Host = self::$smtpConfig['host'];

		// Specify main and backup SMTP servers
		$this -> Phpmailer -> SMTPAuth = true;

		// Enable SMTP authentication
		$this -> Phpmailer -> Username = self::$smtpConfig['username'];

		// SMTP username
		$this -> Phpmailer -> Password = self::$smtpConfig['password'];

		// Enable TLS encryption, `ssl` also accepted
		$this -> Phpmailer -> Port = self::$smtpConfig['port'];
		$this -> Phpmailer -> CharSet = self::$smtpConfig['charset'];

		// TCP port to connect to
		$this -> Phpmailer -> From = self::$smtpConfig['username'];
		$this -> Phpmailer -> FromName = self::$appName;

		$this -> Phpmailer -> WordWrap = 50;
	}

	// AGREGA ATTACHMENTS
	public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment'){
		$this -> Phpmailer -> addAttachment($path, $name, $encoding, $type, $disposition);
	}
	
}
