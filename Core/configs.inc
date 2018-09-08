<?php

	/**
	 * Configs
	 *
	 * Sólo configuraciones de sistema, base de datos, URL pública etc.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.0
	 * @Date 20/08/2015
	 */

	abstract class AppConfig extends Commons {
		static $coreVersion = '3.5.5.1';

        // GENERAL APP CONFIG
		// URL Base debe iniciar y terminar con '/'
		static $urlBase = '/';

		static $appName = 'Medicheck';

		static $appVersion = '0.0.1';

		static $loadResourcesFrom = 'media'; // String or NULL

		static $live_site = '/';

    // DEBUG LINES
    static $debug = false;
		static $debug_disablecss = false;

		// Si es verdadera, junto con debug, deshabilita todos los css.
		static $debug_disablejs = false;

		// Si es verdadera, junto con debug, deshabilita todos los js.
		static $google_maps_api_key = "AIzaSyASS9b8PvWs8LrWBAYkqiDG5DCH7Ba_2HA";


		// NULL si no tiene Front!
		// Mailing setup

		static $smtpMailing = true;

    // CONFIGURAME PARA CADA PROYECTO DE CADA UNO DE LOS PROYECTOS
		static $smtpConfig = array(
			'port' 				=> 2525,
			'host' 				=> 'mail.medicheckapp.com',
			'username' 			=> 'no-reply@medicheckapp.com',
			'password' 			=> '$%#fgUfd8/&',
			'charset' 			=> 'UTF-8',
			// DETALLES DEL LOS ENCABEZADOS
			'remitenteEmail' 	=> 'no-reply@medicheckapp.com',
			'remitente'			=> 'MVC Lite Mailer'
		);

		static $defaults = array (
			'controller' => 'home',
			'action'     => 'index',
			'template'   => 'medicheck',
		);

    static $languagesCFG = array(
        // CONFIGURACIONES DE TRANSLATIONS.inc
        'allow_translations'    => false,
        'default_prefix'        => 'es',
        'debug_translation'     => false,
        'dump_untranslated'     => false,  // AL FINALIZAR LA EJECUCIÓN DE LA APP, IMPRIME TRADUCCIONES FALTANTES
                                          // => USAR CON DISCRECIÓN, PUEDE ROMPER APIS SI ESTÁ EN VERDADERO  FALTA ALGÚN ELEMENTO.
        'dictionaries'          => array(
            array(
                'name'          => 'Español (México)',
                'file'          => 'es_mx',  // MUST BE AN INI FILE
                'prefix'        => 'es'
            ),
            array(
                'name'          => 'English (U. S. A.)',
                'file'          => 'en_us',  // MUST BE AN INI FILE
                'prefix'        => 'en'
            ),
        )
    );

		// FINE SETTINGS
		static $allow_pretty_url 	= true;				// URLS bonitas, o feas...
		static $time_limit				= 5;
		static $memory_limit			= '150M';
		static $timezone					= "America/Mexico_City";
	}

	abstract class ModelConfig extends Commons {
		public $defaultConnection = 'local';
		public $connectionCfg = array (
			'local' => array (
				'host' => 'localhost',
				'user' => 'root',
				'name' => 'mvclite2',
				'password' => ''
			),
			'production' => array (
				'host' => '',
				'user' => '',
				'name' => '',
				'password' => ''
			)
		);
	}
