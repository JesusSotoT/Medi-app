<?php

	/**
	 * Backend Model
	 *
	 * Contiene todas las consultas y manejo de datos para el uso general del
	 * backend.
	 *
	 * Debe administrar los siguientes puntos:
	 *
	 *  Administradores (Antes Usuarios)
	 * 	+ Alta
	 *  + Baja
	 *  + Edición
	 *  + Perfil
	 *
	 *  Departamentos
	 * 	+ Alta
	 *  + Baja
	 *  + Edición
	 *
	 *  Logueo
	 *   + Consulta de Usuario
	 *   + Actualización de última visita
	 *
	 *  Permisos
	 *   + getUserPermissions
	 *
	 *  Modulos (Menu principal) (Trabaja con MenuComponent y MenuHelper)
	 *   + Obtención y construcción de Árbol de menus
	 *
	 * Adaptado para version 3.x
	 *
	 * @Author Daniel Lepe 2015
	 * @Version 1.1 
	 */

	class BackendModel extends Model {
		// INITS 
        public $behaviors = array ( 'Imagenes' );
		public $tables = array ( 'administradores', 'permisos', 'modulos', 'departamentos' );
		public $base64 = true; // Verdadero si las imágenes se guararán bajo formato Base64.
        
        private static $permissions = NULL;
        private static $getUserRolesSQL = "SELECT da.departamentos_id, d.titulo FROM departamentos_has_administradores da INNER JOIN departamentos d ON (d.id = da.departamentos_id) WHERE administradores_id = :user_id";
        private static $permisos_for_executionSQL = "SELECT A.id, A.clave, A.titulo, A.permiso, A.heredado, A.SUP FROM ( SELECT p.id, p.clave, p.titulo, ct.permiso, FALSE AS heredado, 'Usuario' as ORIGEN, FALSE as SUP FROM permisos p LEFT OUTER JOIN administradores_has_permisos ct ON (ct.permisos_id = p.id) WHERE ct.administradores_id = [id] UNION SELECT p.id, p.clave, p.titulo, ct.permiso, TRUE AS heredado, d.titulo as ORIGEN, ct.permiso as SUP FROM permisos p LEFT OUTER JOIN departamentos_has_permisos ct ON (ct.permisos_id = p.id) INNER JOIN departamentos d ON (d.id = ct.departamentos_id) WHERE ct.departamentos_id = [dep_id] UNION SELECT p.id, p.clave, p.titulo, 0 AS permiso, TRUE AS heredado, 'SIN DEFINIR' as ORIGEN, FALSE as SUP FROM permisos p ) A GROUP BY A.id";
		private static $sqlPermisos = array (
			'departamentos'      => "SELECT * FROM ( SELECT p.id, p.clave, p.titulo, ct.permiso, FALSE as heredado FROM permisos p LEFT OUTER JOIN departamentos_has_permisos ct ON (ct.permisos_id = p.id) WHERE ct.departamentos_id = [id] UNION SELECT p.id, p.clave, p.titulo, 0 as permiso, FALSE as heredado FROM permisos p ) A GROUP BY A.id ORDER BY A.titulo DESC",
			'administradores'    => "SELECT A.id, A.clave, A.titulo, A.permiso, A.heredado, A.SUP FROM ( SELECT p.id, p.clave, p.titulo, ct.permiso, FALSE AS heredado, 'Usuario' as ORIGEN, FALSE as SUP FROM permisos p LEFT OUTER JOIN administradores_has_permisos ct ON (ct.permisos_id = p.id) WHERE ct.administradores_id = [id] UNION SELECT p.id, p.clave, p.titulo, ct.permiso, TRUE AS heredado, d.titulo as ORIGEN, ct.permiso as SUP FROM permisos p LEFT OUTER JOIN departamentos_has_permisos ct ON (ct.permisos_id = p.id) INNER JOIN departamentos d ON (d.id = ct.departamentos_id) WHERE ct.departamentos_id IN (SELECT departamentos_id FROM departamentos_has_administradores WHERE administradores_id = [id]) UNION SELECT p.id, p.clave, p.titulo, 0 AS permiso, TRUE AS heredado, 'SIN DEFINIR' as ORIGEN, FALSE as SUP FROM permisos p ) A GROUP BY A.id"
        );
        
        // GETS
		/**
		 * Retreive Menu
		 *
		 * Obtiene todos los criterios a los cuales tiene acceso el usuario, y devuelve
		 * los datos para que sea construido el array multinivel contenedor del menù.
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public function retrieveMenu ( ) {
			return $this -> getAllRows ( array (
				'from' => 'modulos',
				'order_by' => 'posicion asc'
			) );
		}

		/**
		 * Get User Permissions
		 *
		 * Obtiene los permisos de un usario, si es la primera vez que se ejecuta,
		 * consulta a la base de datos, si no, obtiene el contenido de la variable
		 * estáticamente protegida.
		 *
		 * @Author Daniel Lepe
		 * @Version 1.0
		 */
		public function getUserPermissions ( ) {

			if ( is_null ( self::$permissions ) )
				self::$permissions = $this -> getAllRows ( array ( 'from' => 'permisos' ) );

			return self::$permissions;
		}
        
        /**
		 * Get Permisos Departamento Padre
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public function getPermisosDepartamentoPadre ( $administradorId ) {

			$dep_id = $this -> getAllRows ( array ( 'sql' => str_replace ( '[id]', $administradorId, self::$sqlPermisos [ 'admin_dep_id' ] ) ) );

			$dep_id = $dep_id [ 'departamentos_id' ];

			$permisos = $this -> getPermisos ( $dep_id, 'departamentos' );

			$return = array ( );

			foreach ( $permisos as $p ) {
				$return [ $p [ 'id' ] ] = $p [ 'permiso' ];
			}

			return $return;
            
		}
        
        /**
		 * getUserRolesArray
		 *
		 * Devuleve arreglo simple de los roles a los que pertenece un usuario.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
        public function getUserRolesArray($id = null){
            // INIT
            $data = $this -> getAllRows(array('sql' => $this -> bind(self::$getUserRolesSQL, array('user_id' => $id))));
            $response = array();
            
            // PROCCESS
            foreach($data as $k => $d){
                $response[] = $d['departamentos_id'];
            }
            
            // RETURN
            return $response;
        }

        /**
		 * getPermisos
		 *
		 * Devulever permisos desde Departamento y Administradores
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public function getPermisos ( $id, $context ) {
			$contextfield = $context . "_id";
			$contexttable = $context . "_has_permisos";
            $permisos = $this -> getAllRows ( array ( 'sql' => str_replace ( '[id]', $id, self::$sqlPermisos [ $context ] )));
			return $permisos ;
		}
        
        /**
         * permisos_for_execution
         *
         * Devuelve el arreglo de permisos para ejecución general.
         * 
         * @Author Daniel Lepe 2015
         * @Version 1.0
         */
        public function permisos_for_execution($id, $dynamicRole = null){
            $permisos = $this -> getAllRows (
                array ( 'sql' => str_replace ('[id]', $id, 
                        str_replace ( '[dep_id]', $dynamicRole, self::$permisos_for_executionSQL )
                )));
            return $permisos;
        }
        
        /**
		 * setPermisos
		 *
		 * Establece permisos para un contexto específico de administradores/departamento
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
		public function setPermisos ( $id, $context, $data ) {
			$contextfield = $context . "_id";
			$contexttable = $context . "_has_permisos";

			// Borra el contenido original
			$this -> delete ( $contexttable, "$contextfield = $id" );

			foreach ( $data as $pid => $permission ) {
				if ( $permission != 'UNSET' ) {
					if ( !$this -> insert ( $contexttable, array (
							'permisos_id' => $pid,
							'permiso' => $permission,
							$contextfield => $id
						), true, false ) )
						return false;
				}
			}

			// Reescribe
			return true;
		}

        /**
		 * getDeparments
		 *
		 * Obtiene los departamentos generales a los que pertenece un usuario, con todo y título.
		 *
		 * @Author Daniel Lepe 2015
		 * @Version 1.0
		 */
        public function getDeparments($user_id = null){
            // VALIDATES
            if(is_null($user_id)) 
                die("[BackendModel::getDeparments] No se puede operar sin id de usuario.");
            
            $return = $this -> getArrayPair(array(
                // 'get_query' => true,
                'sql' => $this -> bind(self::$getUserRolesSQL, 
                                    array("user_id" => $user_id))));
            
            return $return;
            
        }
        
        // SAVES
		/**
		 * Save Administrador
		 *
		 * Guarda la informaciòn de un administrador
		 *
		 * @Author Daniel Lepe 2014
		 * @Version 1.0
		 */
		public function saveAdministrador ( $data, $id = null, $check_roles = true ) {

            // INIT
            $administrador = $data['administradores'];
            $roles = array();
            $this -> response['status'] = false;            
            $this -> response['msg'] = 'Administrador exitosamente' . ((is_null($id))? ' creado.' : ' actualizado.');
            $this -> response['class'] = 'success';
            
            // VALIDATION
            if(empty($administrador)){ 
                $this -> response['msg'] = 'No se recibieron datos';
                $this -> response['class'] = 'danger';
                return false; 
            }
            
            // ROLE CHECKING
            if($check_roles){
                if(!isset($data['_']['roles'])){ 
                    $this -> response['msg'] = 'No se puede guardar un usuario que no perteneceza a por lo menos 1 Rol de usuarios.';
                    $this -> response['class'] = 'danger';
                    return false; 
                } else {
                    $roles = $data['_']['roles'];
                }
            }
            
            // VALIDA PASSWORD
			if ( isset ( $administrador [ 'password' ] ) ) {
				if ( !empty ( $administrador [ 'password' ] ) ) {
					$administrador [ 'password' ] = md5 ( $administrador [ 'password' ] );
				} else {
					unset ( $administrador [ 'password' ] );
				}
			}
			
            // VALIDA EMAIL
			if(isset($administrador [ 'email' ]) and !$this -> _revision_email ( $administrador [ 'email' ], $id ) )
                return true;
            
            // VALIDATIONS
			if ( is_null ( $id ) ) {

				$email = $administrador [ 'email' ];

				if ( !isset ( $administrador [ 'password' ] ) ) {
					$this -> response['msg'] = "No podemos guardar un administrador sin contraseña.";
                    $this -> response['class'] = 'warning';
					return false;
				}

				if(!$this -> insert ( 'administradores', $administrador )){
                    $this -> response['msg'] = "No se pudo crear";
                    $this -> response['class'] = 'warning';
					return false;
                }
                
                $id = $this -> id;
                
			} else {
                
				if ( !$this -> update ( 'administradores', $administrador, "id = $id") ) {
					$this -> response['msg'] = "No se pudo actualizar";
                    $this -> response['class'] = 'warning';
					return false;
				}

			}
			
            // SAVES IMAGES
			if(!empty($_FILES))
				$this -> Imagenes -> upload_and_attach ( $id, 'administradores', 256, 256, 80 );
            
            // SAVES ROLES
            if(!empty($roles))
                $this -> saveRolesAdministrador($roles, $id);
            
            // RETURN
            $this -> response['status'] = true;
			return true;
		}
        
        public function saveRolesAdministrador ($data, $id = null){
            
            // CLEAN
            $this -> delete('departamentos_has_administradores', "administradores_id = $id");
            
            // INSERT
            foreach($data as $did){
                $this -> insert('departamentos_has_administradores', array(
                    'administradores_id' => $id,
                    'departamentos_id' => $did
                   )
                );
            }
            
            return $id;
        }

		/**
		 *	Save Departamento
		 *
		 * 	Actualiza los datos del departamento, o lo crea si no incluye un ID.
		 *
		 * @Author Daniel Lepe 2014
		 */
		public function saveDepartamento ( $data, $id = null ) {

			if ( is_null ( $id ) ) {
				$id = $this -> insert ( 'departamentos', $data );
			} else {
				unset ( $data [ 'id' ] );
				$this -> update ( 'departamentos', $data, "id = $id" );
			}

			return $id;
		}
        
        // LOCAL METHODS
        /**
		 * _revision_email
		 *
		 * @Author Daniel Lepe
		 * @Version 1.0
		 * */
		protected function _revision_email ( $email, $id = null ) {

			if ( !preg_match ( '/^[_a-zA-Z0-9]([\-+_%.a-zA-Z0-9]+)?@([_+\-%a-zA-Z0-9]+)(\.[a-zA-Z0-9]{0,6}){1,2}([a-zA-Z0-9]$)/', $email ) ) {
				$this -> error = "El correo no está bien formateado, no podemos continuar.";
				return false;
			}

			if ( is_null ( $id ) ) {
				$where = "email like '$email'";
			} else {
				$where = "email like '$email' AND id <> $id";
			}

			$prevData = $this -> getOneRow ( array (
				'from' => 'administradores',
				'where' => $where
			) );

			if ( !empty ( $prevData ) ) {
				$this -> response['msg'] = "El correo ya existe, no podemos continuar.";
                $this -> response['class'] = 'warning';
				return false;
			}

			return true;
		}
        
        // LOGS
        // GET LOG TYPES
        public function getLogTypes(){
            // INIT
            $sql = "SELECT DISTINCT type, id FROM logs order by type desc"; $return = array();
            // PERFORM QUERY
            $return = $this -> getArrayPair(array('sql' => $sql));
            // RETURN
            return array_flip($return);
        }
        
        // PAGINATE
        public function getLogs ($type, $user){
            // EFECTÚA LA LIMPIEZA
            if(is_string($type) and is_numeric($user)){
                // VALIDA PARÁMETROS
                return $this -> paginateAllRows(array('from' => 'logs', 'where' => "type = '$type' and administradores_id = $user"));    
            }
            
            if(is_string($type)){
                // VALIDA PARÁMETROS
                return $this -> paginateAllRows(array('from' => 'logs', 'where' => "type = '$type'"));    
            }

            if(is_numeric($user)){
                // VALIDA PARÁMETROS
                return $this -> paginateAllRows(array('from' => 'logs', 'where' => "administradores_id = $user"));    
            }
            
            // VALIDA PARÁMETROS
            return $this -> paginateAllRows(array('from' => 'logs'));    
        }
        
        // CLEAN
        public function cleanLogs ($type, $user){
            // EFECTÚA LA LIMPIEZA
            if(is_string($type) and is_numeric($user)){
                // VALIDA PARÁMETROS
                return $this -> delete('logs', "type = '$type' and administradores_id = $user");    
            }
            
            if(is_string($type)){
                // VALIDA PARÁMETROS
                return $this -> delete('logs', "type = '$type'");    
            }

            if(is_numeric($user)){
                // VALIDA PARÁMETROS
                return $this -> delete('logs', "administradores_id = $user");    
            }
            
            // VALIDA PARÁMETROS
            return $this -> delete('logs', "1 = 1");    
        }
	}