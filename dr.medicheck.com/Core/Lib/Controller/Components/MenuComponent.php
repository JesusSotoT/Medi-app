<?php

	/**
	 * Menu Component
	 *
	 * Crea el menú principal para el Backend.
	 *
	 * Si deseas modificarlo, copia este archivo a /Core/Controllers/Components, de
	 * allí lo correrá automáticamente.
	 *
	 * @Author Daniel Lepe 2014
	 */

	class MenuComponent extends Component {

		public $models = array ( 'Backend' );
		private $menu = array ( );

		/**
		 * Build Main Menu
		 *
		 * Devuelve el menù general de la aplicaciòn para que el MenuHelper pueda
		 * renderizar la barra lateral de la izquierda.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 * */

		public function buildMainMenu ( ) {

			$menu = $this -> Backend -> retrieveMenu ( );
            
			foreach ( $menu as $k => $m ) {

				// Nivel 1
				if ( $m [ 'padre_id' ] == 0 ) {
					$this -> menu [ $m [ 'id' ] ] = $m;
					unset ( $menu [ $k ] );

					// Nivel 2
					foreach ( $menu as $j => $s ) {
						if ( $s [ 'padre_id' ] == $m [ 'id' ] ) {
							$this -> menu [ $m [ 'id' ] ] [ 'children' ] [ $s [ 'id' ] ] = $s;
							unset ( $menu [ $j ] );

							// Nivel 3
							foreach ( $menu as $l => $t ) {
								if ( $t [ 'padre_id' ] == $s [ 'id' ] ) {
									$this -> menu [ $m [ 'id' ] ] [ 'children' ] [ $s [ 'id' ] ] [ 'children' ] [ $t [ 'id' ] ] = $t;
									unset ( $menu [ $l ] );

									// Nivel 4
									foreach ( $menu as $k1 => $m1 ) {
										if ( $m1 [ 'padre_id' ] == $t [ 'id' ] ) {
											$this -> menu [ $m [ 'id' ] ] [ 'children' ] [ $s [ 'id' ] ] [ 'children' ] [ $t [ 'id' ] ] [ 'children' ] [ $m1 [ 'id' ] ] = $m1;
											unset ( $menu [ $k1 ] );
										}
									}
								}
							}
						}
					}
				}

			}
            
			$this -> set ( 'menu_data', $this -> menu );

		}


	}
