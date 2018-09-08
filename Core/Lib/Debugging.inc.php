<?php

	/* ================================================================== *
	 *
	 * Nombre del archivo: debugging_flashing.inc.php
	 * Descripcion: Recive desde controlador, modelos y vistas llamadas para
	 * depurado de datos y otras noticaciones para el cliente.
	 *
	 * Requiere bootstrap.min.css bootstrap.min.js y Jquery cargados en Frontend.
	 * @Author Daniel Lepe
	 * @
	 */

	/*
	 * Recibe una variable y la imprime INSITE.
	 *
	 * Ademas indica nombre de archivo y linea donde ha sido llamado.
	 * V 1.2: Para que funcione, ahora debe estar la variable:
	 *		[APP::$debug = true]
	 *
	 * @Author Daniel Lepe
	 * @Version 1.2
	 * */
	function debug ( $var, $html = true, $backtrace = null ) {
		if(class_exists('APP') and APP::$debug == false)
			return true;

		$id = uniqid ( );

		if ( is_null ( $backtrace ) )
			$backtrace = debug_backtrace ( );

		$debug = "<div id='$id'>" . "<code class=''>" . "<strong>FILE: " . $backtrace [ 0 ] [ 'file' ] . "</strong>" . "<BR />" . PHP_EOL . "<strong>LINE: " . $backtrace [ 0 ] [ 'line' ] . "</strong>" . "<BR />" . PHP_EOL . "<pre>";

		ob_start ( );
		print_r ( $var );
		$dump = ob_get_clean ( );
		$debug .= htmlentities ( $dump );
		$debug .= "</pre>" . "</code>" . "</div>";

		if ( !$html )
			$debug = strip_tags ( $debug );

		echo $debug;
	}


	/**
	 * BreakPoint
	 *
	 * Depura el contenido de una variable y depura el valor llamado.
	 *
	 * Ahora se puede seleccionar si se desea que se muestre el código fuente de una funcion o si no.
	 * V 1.2: Para que funcione, ahora debe estar la variable:
	 *		[APP::$debug = true]
	 *
	 * @Author Daniel Lepe
	 * @Version 1.2
	 * @Date 18/09/2015
	 */
	function breakpoint ( $var, $show_source = false ) {
		if(class_exists('APP') and APP::$debug == false)
			return true;
		$break = debug_backtrace ( );
		debug ( $var, true, $break );
		if ( isset ( $this ) )
			unset ( $this );
		if($show_source)
			show_source ( $break [ 0 ] [ 'file' ] );
		die ( 'Fin del Brakepoint: ' . date('Y-m-d H:i:s'));

	}
