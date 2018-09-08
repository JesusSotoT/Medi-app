<?php

	/**
	 * Archivo de configuración de rutas. Ejemplos:
	 *
	 * Add Context [contexto:str], [defaults:arr]
	 * Routes::add_context('admin', array (
	 *		'controller' 	=> 'proyectos',
	 *		'action' 		=> 'index',
	 *		'template' 		=> 'dashboard',
	 *	));
	 *
	 * Add Context [ruta:str], [routeCfg:arr]
	 * Routes::add_redirect('paginas/*', array(
	 * 		'admin' 		=> false,
	 * 		'controller' 	=> 'pages',
	 * 		'action' 		=> 'index'));
	 *
	 * @Author Daniel Lepe 2014
	 */
	

	// Contexto administrativo
	Routes::add_context ( 'admin', array (
		'controller' 	=> 'administradores',
		'action' 		=> 'index',
		'template' 		=> 'dashboard',
	) );