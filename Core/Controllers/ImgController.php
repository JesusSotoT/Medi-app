<?php

	class Img extends AppController {

		var $models = array ( 'Backend' );

		public function render ( $img, $w=null, $h=null ) {

			if ( !is_numeric ( $img ) )
				$this -> set_404 ( );

			$img = $this -> Backend -> getOneRow ( array (
				'from' => 'imagenes',
				'where' => "id = $img"
			) );

			$this -> pop_image ( $img, $this -> Backend -> base64, $w, $h );
		}


		/**
		 * Pop Image
		 *
		 * Renderiza una imágen con los datos pasados.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.1
		 */

		protected function pop_image ( $img, $base64, $w, $h) {
			if(is_numeric($w) and is_numeric($h))
				$this -> pop_image_resized($img[ 'imagen' ], $base64, $w, $h, $img['mime']);

			if ( $base64 ) {
				header ( 'Content-Length: ' . strlen ( base64_decode ( $img [ 'imagen' ] ) ) );
				header ( "Content-type: " . $img [ 'mime' ] );
				echo base64_decode ( $img [ 'imagen' ] );
			} else {
				header ( 'Content-Length: ' . strlen ( $img [ 'imagen' ] ) );
				header ( "Content-type: " . $img [ 'mime' ] );
				echo $img [ 'imagen' ];
			}
			die ( );
		}
		
		/**
		 * pop_image_resized
		 *
		 * Renderiza una imágen con dimensiones distintas a la de origen.
		 *
		 * @Author Daniel Lepe
		 * @DAte 17/11/2015
		 * @Version 1.1
		 */

		protected function pop_image_resized ( $img, $base64, $w, $h, $mime) {
			if ( $base64 ) {
				$image = base64_decode ( $img );
			} else {
				$image = $img;
			}
			
			$this -> Backend -> Imagenes -> get_resized($image, $w, $h, false, 90, $mime);

			// header ( 'Content-Length: ' . $image['length'] );
			// header ( "Content-type: " . $image['mime'] );
			// echo $image['data'];
			// die ( );
		}


	}
