<?php

	$demo_mode = array (
		0 => array (
			'glyphicon' => 'entypo-gauge',
			'title' => 'Dashboard',
			'url' => 'dashboard',
			'children' => array (
				0 => array (
					'glyphicon' => NULL,
					'title' => 'Sin Glyphicon, con Hijos',
					'url' => 'dashboard',
					'children' => array (
						0 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 1',
							'url' => 'dashboard',
						),
						1 => array (
							'glyphicon' => 'entypo-layout',
							'title' => 'Hijo 2',
							'url' => 'dashboard',
						),
						2 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 3',
							'url' => 'dashboard',
						),
					)
				),
				1 => array (
					'glyphicon' => 'entypo-star',
					'title' => 'entypo-star',
					'url' => 'dashboard',
					'children' => array (
						0 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 1',
							'url' => 'dashboard',
						),
						1 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 2',
							'url' => 'dashboard',
						),
						2 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 3',
							'url' => 'dashboard',
						),
					)
				),
			)
		), 1 => array (
			'glyphicon' => 'entypo-gauge',
			'title' => 'Dashboard',
			'url' => 'dashboard',
			'children' => array (
				0 => array (
					'glyphicon' => NULL,
					'title' => 'Sin Glyphicon, con Hijos',
					'url' => 'dashboard',
					'children' => array (
						0 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 1',
							'url' => 'dashboard',
						),
						1 => array (
							'glyphicon' => 'entypo-layout',
							'title' => 'Hijo 2',
							'url' => 'dashboard',
						),
						2 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 3',
							'url' => 'dashboard',
						),
					)
				),
				1 => array (
					'glyphicon' => 'entypo-star',
					'title' => 'entypo-star',
					'url' => 'dashboard',
					'children' => array (
						0 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 1',
							'url' => 'dashboard',
						),
						1 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 2',
							'url' => 'dashboard',
						),
						2 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 3',
							'url' => 'dashboard',
						),
					)
				),
			)
		), 2 => array (
			'glyphicon' => 'entypo-gauge',
			'title' => 'Dashboard',
			'url' => 'dashboard',
			'children' => array (
				0 => array (
					'glyphicon' => NULL,
					'title' => 'Sin Glyphicon, con Hijos',
					'url' => 'dashboard',
					'children' => array (
						0 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 1',
							'url' => 'dashboard',
						),
						1 => array (
							'glyphicon' => 'entypo-layout',
							'title' => 'Hijo 2',
							'url' => 'dashboard',
						),
						2 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 3',
							'url' => 'dashboard',
						),
					)
				),
				1 => array (
					'glyphicon' => 'entypo-star',
					'title' => 'entypo-star',
					'url' => 'dashboard',
					'children' => array (
						0 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 1',
							'url' => 'dashboard',
						),
						1 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 2',
							'url' => 'dashboard',
						),
						2 => array (
							'glyphicon' => NULL,
							'title' => 'Hijo 3',
							'url' => 'dashboard',
						),
					)
				),
			)
		)
	);

	$this -> Menu -> Security = $this -> Security;

	echo $this -> Menu -> make_menu_list ( $this -> Components -> Menu -> menu_data, $options = array (
		'id' => 'main-menu',
		'class' => ''
	), $afterElements = array ( 'search' => array ( ) ) );
