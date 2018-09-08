<?php
	if (!function_exists('getimagesizefromstring')) {
	     function getimagesizefromstring($data)
	     {
	         $uri = 'data://application/octet-stream;base64,' . base64_encode($data);
	         return getimagesize($uri);
	     }
	}

	/**
	 * Imagenes Behavior
	 *
	 * Update 1.2
	 * Ahora la función upload_and_attach se le puede pasar el parámetro 'auto'
	 * para $foraign_field, con lo que requiere que se pase el nombre de la llave local
	 * del arhcivo a la superglobal $_FILES, por ejemplo:
	 * 		$_FILES['tabla']['imagenes_id'] = array();
	 *		Buscará en la tabla referenciada el campo 'imagenes_id' para el enlace.
	 *
	 * @Author Daniel Lepe
	 * @Version 1.2
	 * @Date 06/11/2015
	 */
	class ImagenesBehavior extends Model {

		var $targets = null;
		var $out_table;
		var $name = 'imagenes';
		var $base64 = true;

		public function imagenesModel ( $out_table = null ) {
			if ( !$out_table == null ) {
				$this -> out_table = $out_table;
			}
		}

		/**
		 * Upload
		 *
		 * Primero rectifica si se puede subir una imágen a la base de datos, si es asì
		 * la procesa para que se ajueste al tamaño indicado via Opciones:
		 *  $width => Ancho en pixeles de la imágen.
		 *  $height => Alto en pixeles de la imágen.
		 *  $q => Calidad en porcentaje de la imágen, ya que todo se guadará en Jpeg.
		 *
		 * Una vez que ha procesado la imágen la guara en la tabla de imagnes, si se le
		 * ha
		 * pasado un id de la tabla de imágenes, actualiza tal registro, si no existe lo
		 * ingresa
		 * y siempre devolverá el id del registro de la imágen subida o falso si ocurrió
		 * un error en algo.
		 *
		 * Esta función adminte JPEG y PNG únicamente.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function upload ( $imagen_id = null, $width = 100, $height = 100, $q = 80 ) {
			$this -> build_target_images ( );
			$result = array ( );
			// Resultant IDs
			foreach ( $this->targets as $target ) {
				$result [ ] = $this -> save_to_database ( $target );
			}
			return $result;
		}

		/**
		 * Upload and Attach
		 *
		 * Sube una imágen y guarda su ID en el registro del modelo especificado con
		 * $foreign_id, $foreign_table.
		 *
		 * Posteriormente borra la imágen de la tabla que correspondía al id anterior.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function upload_and_attach ( $foraign_id, $foreign_table, $width = 100, $height = 100, $q = 85, $foraign_field = 'imagenes_id', $avoid_build = false) {

			if(!$avoid_build)
				$this -> build_target_images ( );

			if($foraign_field == 'auto'){
				reset($this -> targets);
				$recursive_foraign_key = key($this -> targets);
				$this -> upload_and_attach($foraign_id,
											$foreign_table,
											$width,
											$height,
											$q, $recursive_foraign_key, true);
				// PREPARA EL FIELD
				unset($this -> targets[$recursive_foraign_key]);
				reset($this -> targets);
				$foraign_field = key ( $this -> targets );
			}


			$this -> out_table = $foreign_table;
			reset ( $this -> targets );
			$key = key ( $this -> targets );

			if ( empty ( $this -> targets ) )
				return true;

			$this -> redimension ( $width, $height, $q );

			$imagen_id = $this -> save_to_database ( $this -> targets [ $key ] );

			// Funciona con la primera coincidencia de imágen sana.

			if ( $imagen_id ) {

				return $this -> single_model_attach ( $imagen_id, $foraign_id, $foreign_table , $foraign_field);

			} else {

				return false;

			}
		}

		/**
		 * Upload Many and Attach
		 *
		 *  Igual que Upload Many pero para la relación usa una tabla HABTM
		 *
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */

		public function upload_many_and_attach ( $foraign_id, $foreign_table, $width = 100, $height = 100, $q = 85 ) {
			$this -> build_target_images ( );
			$this -> out_table = $foreign_table;

			if ( empty ( $this -> targets ) )
				return true;

			if ( is_null ( $this -> out_table ) )
				$this -> out_table = $foreign_table;

			// Valida si existe la tabla HABTM
			$habtm = $foreign_table . "_has_" . $this -> name;

			$habt_des = $this -> executeQuery ( "DESC " . $habtm );

			if ( empty ( $habt_des ) )
				die ( 'Tabla ' . $habtm . " inexistente." );

			$this -> redimension ( $width, $height, $q );

			foreach ( $this->targets as $img ) {

				$imagen_id = $this -> save_to_database ( $img );
				// Funciona con todas las coincidencias de imágenes sanas.

				if ( $imagen_id ) {
					if ( !$this -> habtm_model_attach ( $imagen_id, $foraign_id, $foreign_table ) )
						die ( "Ha ocurrido un error vinculando los modelos HABTM" );
				}
			}
			return true;
		}

		/*
		 * Redimension
		 *
		 *  Redimensiona las imágenes subidas y las ajusta a JPG
		 *
		 * Daniel Lepe 2014
		 */

		private function redimension ( $width, $height, $q ) {
			foreach ( $this->targets as $img ) {
				$this -> smart_resize_image ( $img [ 'tmp_name' ], $img [ 'tmp_name' ], $width, $height, $q );
			}
		}

		/**
		 * easy image resize function
		 * @param  $file - file name to resize
		 * @param  $output - name of the new file (include path if needed)
		 * @param  $width - new image width
		 * @param  $height - new image height
		 * @param  $quality - enter 1-100 (100 is best quality) default is 80
		 * @param  $proportional - keep image proportional, default is no
		 * @param  $delete_original - if true the original image will be deleted
		 * @param  $use_linux_commands - if set to true will use "rm" to delete the
		 * image, if false will use PHP unlink
		 * @param  $string - The image data, as a string
		 * @return boolean|resource
		 */
		private function smart_resize_image ( $file, $output = 'file', $width = 0, $height = 0, $quality = 100, $proportional = false, $delete_original = true, $use_linux_commands = false, $string = null ) {

			if ( $height <= 0 && $width <= 0 )
				return false;
			if ( $file === null && $string === null )
				return false;

			# Setting defaults and meta
			$info = $file !== null ? getimagesize ( $file ) : getimagesizefromstring ( $string );

			$image = '';
			$final_width = 0;
			$final_height = 0;
			list ( $width_old, $height_old ) = $info;
			$cropHeight = $cropWidth = 0;

			# Calculating proportionality
			if ( $proportional ) {
				if ( $width == 0 )
					$factor = $height / $height_old;
				elseif ( $height == 0 )
					$factor = $width / $width_old;
				else
					$factor = min ( $width / $width_old, $height / $height_old );

				$final_width = round ( $width_old * $factor );
				$final_height = round ( $height_old * $factor );
			} else {
				$final_width = ($width <= 0) ? $width_old : $width;
				$final_height = ($height <= 0) ? $height_old : $height;
				$widthX = $width_old / $width;
				$heightX = $height_old / $height;

				$x = min ( $widthX, $heightX );
				$cropWidth = ($width_old - $width * $x) / 2;
				$cropHeight = ($height_old - $height * $x) / 2;
			}

			# Loading image to memory according to type
			switch ($info[2]) {
				case IMAGETYPE_JPEG :
					$file !== null ? $image = imagecreatefromjpeg ( $file ) : $image = imagecreatefromstring ( $string );
					break;
				case IMAGETYPE_GIF :
					$file !== null ? $image = imagecreatefromgif ( $file ) : $image = imagecreatefromstring ( $string );
					break;
				case IMAGETYPE_PNG :
					$file !== null ? $image = imagecreatefrompng ( $file ) : $image = imagecreatefromstring ( $string );
					break;
				default :
					return false;
			}

			# This is the resizing/resampling/transparency-preserving magic
			$image_resized = imagecreatetruecolor ( $final_width, $final_height );
			if ( ($info [ 2 ] == IMAGETYPE_GIF) || ($info [ 2 ] == IMAGETYPE_PNG) ) {
				$transparency = imagecolortransparent ( $image );
				$palletsize = imagecolorstotal ( $image );

				if ( $transparency >= 0 && $transparency < $palletsize ) {
					$transparent_color = imagecolorsforindex ( $image, $transparency );
					$transparency = imagecolorallocate ( $image_resized, $transparent_color [ 'red' ], $transparent_color [ 'green' ], $transparent_color [ 'blue' ] );
					imagefill ( $image_resized, 0, 0, $transparency );
					imagecolortransparent ( $image_resized, $transparency );
				} elseif ( $info [ 2 ] == IMAGETYPE_PNG ) {
					imagealphablending ( $image_resized, false );
					$color = imagecolorallocatealpha ( $image_resized, 0, 0, 0, 127 );
					imagefill ( $image_resized, 0, 0, $color );
					imagesavealpha ( $image_resized, true );
				}
			}

			imagecopyresampled ( $image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight );

			# Taking care of original, if needed
			if ( $delete_original ) {
				if ( $use_linux_commands )
					exec ( 'rm ' . $file );
				else
					@unlink ( $file );
			}

			# Preparing a method of providing result
			switch (strtolower($output)) {
				case 'browser' :
					$mime = image_type_to_mime_type ( $info [ 2 ] );
					header ( "Content-type: $mime" );
					$output = NULL;
					break;
				case 'file' :
					$output = $file;
					break;
				case 'return' :
					return $image_resized;
					break;
				default :
					break;
			}

			# Writing image according to type to the output destination and image quality
			switch ($info[2]) {
				case IMAGETYPE_GIF :
					imagegif ( $image_resized, $output );
					break;
				case IMAGETYPE_JPEG :
					imagejpeg ( $image_resized, $output, $quality );
					break;
				case IMAGETYPE_PNG :
					$quality = 9 - (int)((0.9 * $quality) / 10.0);
					imagepng ( $image_resized, $output, $quality );
					break;
				default :
					return false;
			}

			return true;
		}

		/*
		 * get_resized
		 *
		 * Esta funcion vincula a una imágen con un modelo foráneo, posteriormente
		 * elimina de la base de datos la imàgen que ya no està relacionada.
		 *
		 * @param $imgData 			= BLOB: Binary data as string of the image to resize.
		 * @param $w				= INT: New dynamic width.
		 * @param $h				= INT: New Dynamic height
		 * @param $porportional		= BOOL: Proportion, true or false.
		 * @param $q				= NUMBER: JPG end quality, percenet. 100% is almost lossless.
		 * @param $mime				= STRING: MIME data for conversion.
		 *
		 * @returns Array with image data, size and mime.
		 *
		 * @Author Daniel Lepe
		 * @Date 17/11/2015
		 * @Version 1.0
		 */
		public function get_resized($imgData, $w, $h, $proportional = true, $q = 90, $mime){
			if(is_string($w)) $w = preg_replace('/\.\w+/im', null, $w);
			if(is_string($h)) $h = preg_replace('/\.\w+/im', null, $h);
			// INIT
			$resized 	= null;
			$img		= null;

			// MAKE RESIZE
			$resized = $this -> smart_resize_image (
				null,
				'return',
				$w,
				$h,
				$q,
				$proportional,
				false,
				false,
				$imgData
			);

			header ( "Content-type: " . $mime );

			// MAKE IMG
			switch($mime){
				case 'image/gif':
					imagegif($resized);
					break;
				case 'image/png':
					imagepng($resized);
					break;
				case 'image/jpg': case 'image/jpeg':
					imagejpeg($resized);
					break;
			}
			die();

		}


		/*
		 * Single_model_attach
		 *
		 * Esta funcion vincula a una imágen con un modelo foráneo, posteriormente
		 * elimina de la base de datos la imàgen que ya no està relacionada.
		 *
		 * Daniel Lepe 2014
		 */

		private function single_model_attach ( $imagenes_id, $foraign_id, $foraign_model , $foraign_field) {
			$borrar_id = null;
			// Inicalmente es nulo el borrado de imágen local.

			$prev = $this -> getOneRow (array('sql' =>  "SELECT $foraign_field FROM $foraign_model where id = $foraign_id") );
			if ( !empty ( $prev [ $foraign_field ] ) )
				$borrar_id = $prev [ $foraign_field ];


			$result = $this -> update ( $foraign_model, array ($foraign_field => $imagenes_id ), " id = $foraign_id");


			if ( $result ) {

				if ( is_numeric ( $borrar_id ) )
					$this -> delete ( $this -> name, "id = $borrar_id" );

				return true;

			} else {

				$this -> response['msg'] = ( 'Ha ocurrido un error en el vinculado de imágen con modelo foráeno.' );

			}
		}

		/*
		 * HABTM model Attach
		 *
		 * Esta funcion vincula a una imágen con un modelo HABTM foráno
		 *
		 * Daniel Lepe 2014
		 */

		private function habtm_model_attach ( $imagenes_id, $foraign_id, $foreign_table ) {

			$habtm = $foreign_table . "_has_" . $this -> name;

			$result = $this -> insert ( $habtm, array (
				'imagenes_id' => $imagenes_id,
				$foreign_table . '_id' => $foraign_id
			) );

			if ( $result ) {
				return true;
			} else {
				die ( 'Ha ocurrido un error en el vinculado de imágen con modelo foráeno.' );
			}
		}

		/*
		 * Save to DataBase
		 *
		 * Se guarda a la base de datos la imágen
		 *
		 * Danie Lepe 2014
		 */

		private function save_to_database ( $element ) {

			if ( $this -> base64 ) {
				$data = base64_encode ( file_get_contents ( $element [ 'tmp_name' ] ) );

				$imagen = array (
					'imagen' => $data,
					'out_table' => $this -> out_table,
					'mime' => $element [ 'type' ]
				);

				$result = $this -> db_insertar ( $this -> name, $imagen );

				return $result;
			} else {
				$data = chunk_split ( mysql_real_escape_string ( file_get_contents ( $element [ 'tmp_name' ] ) ) );

				$conn = db_connect ( );

				if ( !$conn )
					die ( 'Imposible proceder, no se pudo conectar' );

				$sql = "INSERT INTO `%s`  (%s) VALUES (%s);";

				$result = @mysql_query ( $sql, $conn );

				$reg_id = @mysql_insert_id ( $conn );

				if ( $result == FALSE ) {
					echo "Error en la inserción, volcado de datos:" . HTML_EOL;
					debug ( $sql );
					debug ( $result );
					return false;
				}

				if ( empty ( $reg_id ) || $reg_id == NULL ) {
					return true;
				} else {
					return $reg_id;
				}
			}
		}

		/*
		 * Pop Image
		 *
		 * Manda al layout principal una imágen en especìfico.
		 *
		 * Daniel Lepe 2014
		 */

		public function pop_image ( $id ) {
			$img = db_read ( 'SELECT * FROM ' . $this -> name . " WHERE id = " . $id );
			header ( 'Content-Length: ' . strlen ( base64_decode ( $img [ 'imagen' ] ) ) );
			//		header('Content-Disposition: attachment; filename="' . $id . '.jpg"');
			header ( "Content-type: " . $img [ 'mime' ] );
			if ( $this -> base64 ) {
				echo base64_decode ( $img [ 'imagen' ] );
			} else {
				echo $img [ 'imagen' ];
			}
			// die();
		}

		/*
		 * Build Target Images
		 *
		 * Revisa y extra de $_FILES todas las imagenes procesables.
		 *
		 * Daniel Lepe 2014
		 */

		private function build_target_images ( ) {
			if ( is_array ( $_FILES ) and (!empty ( $_FILES )) ) {
				$this -> targets = $_FILES;
				if ( isset ( $_FILES [ '_' ] ) )
					$this -> targets = $this -> _fix_files_array ( $_FILES [ '_' ] );
				foreach ( $this->targets as $k => $possible_img ) {
					if ( !in_array ( $possible_img [ 'type' ], array (
						'image/jpeg',
						'image/jpg',
						'image/png'
					) ) ) {
						unset ( $this -> targets [ $k ] );
					}
				}
			}
		}


		private function _fix_files_array ( $array ) {

			$aFiles = array ( );

			foreach ( $array as $field => $dataNode ) {
				foreach ( $dataNode as $k => $val ) {
					$aFiles [ $k ] [ $field ] = $val;
				}
			}

			return $aFiles;
		}


	}
