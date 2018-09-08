<?php

/**
 * Administradores Controller
 *
 * Maneja y administra todo el contexto general del backend.
 *
 * @Author Daniel Lepe
 * @Version 1.2
 */
class Administradores extends AppController {

	public $components = array('Menu', 'Security', 'Multidepartment', 'Mail', 'Log');

	public $models = array('Backend');

	public $autoLoginOnPWDReset = true;

	protected static $allowedContext = array('administrador', 'departamento');

    protected $publicAccessActions = array(
        'admin_miperfil',
        'admin_login',
        'admin_logout',
        'admin_forgottenpassword',
        'admin_passwordbackemailsent',
        'admin_roleas',
        'admin_resetpassword');

	// BEFORE ACTION
	public function beforeAction(){
		parent::beforeAction();

		// VALIDATION
		if(!in_array($this -> action, $this -> publicAccessActions )
           and !$this -> Security -> hasPermission('administradores_root'))
			$this -> set_404();
	}

	public function admin_index() {
		$this -> title = 'Administradores';
	}

	public function admin_borrardepartamento($did = null) {
	 	 if(!is_numeric($did))$this -> set_404();
		 $depto = $this -> Backend -> getOneRow(array('from' => 'departamentos', 'where' => "id = $did"));
		 if(empty($depto)) $this -> set_404();
		 if($this -> Backend -> delete('departamentos', "id = $did")){
		 	$this -> Session -> setFlash(sprintf('Rol %s exitosamente eliminado', $depto['titulo']), 'success');
		 } else {
		 	$this -> Session -> setFlash(sprintf('Ha ocurrido un error eliminado el rol "%s", talvez aún tiene usuarios vinculados.', $depto['titulo']), 'warning');
		 }
		 $this -> go_to($this -> referer());
	 }

    public function admin_borraradministrador($aid = null) {
        if(!is_numeric($aid)) $this -> set_404();

        // VALIDA QUE NO SEA EL USUARIO MISMO EL QUE TRATE DE BORRARSE SOLO.
        if($this -> Session -> user('id') == $aid){
            $this -> Session -> setFlash('No puedes borrarte tu mismo...', 'warning');
            $this -> go_to($this -> referer());
        }

		 $admin = $this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => "id = $aid"));

		 if(empty($admin)) $this -> set_404();

		 if($this -> Backend -> delete('administradores', "id = $aid")){
		 	$this -> Session -> setFlash(sprintf('El dministrador %s ha sido exitosamente eliminado', $admin['nombres']), 'success');
		 } else {
		 	$this -> Session -> setFlash(sprintf('Ha ocurrido un error eliminado al administrador %s, talvez no sea borrable por registros vinculados a su cuenta.', $admin['nombres']), 'warning');
		 }

		 $this -> go_to($this -> referer());
    }

	public function admin_departamentos() {

		$this -> title = 'Roles de administradores';

		$departamentos = ($this -> Backend -> getAllRows(array('from' => 'departamentos')));

		$this -> set('departamentos', $departamentos);
	}

	public function admin_administradores() {

		$this -> title = 'Administradores';

		$admins = ($this -> Backend -> getAllRows(array('from' => 'administradores')));

		$departamentos = ($this -> Backend -> getArrayPair(array('from' => 'departamentos')));

		$this -> set('admins', $admins);
	}

	public function admin_administrador($id = null) {

		if ((!is_numeric($id) and !is_null($id)) or !$this -> is_ajax() ) {
			$this -> Session -> setFlash('Acceso incorrecto', 'warning');
			$this -> go_to(array('controller' => 'administradores', 'action' => 'departamentos'));
		}

		if (is_numeric($id))
			$this -> title = 'Editar Administrador';

		if (is_null($id))
			$this -> title = 'Nuevo Administrador';

		if ($this -> is_post()) {

			$this -> Backend -> saveAdministrador($this -> data, $id);

            $this -> json($this -> Backend -> response);

		} else {

			if (is_numeric($id)) {
				$this -> data['administradores'] = $this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => "id = $id"));
                $this -> data['_']['roles'] = $this -> Backend -> getUserRolesArray($id);
			}

		}

		$this -> set('departamentos', $this -> Backend -> getArrayPair(array('from' => 'departamentos')));
	}

	public function admin_departamento($id = null) {

		if (!is_numeric($id) and !is_null($id)) {
			$this -> Session -> setFlash('Acceso incorrecto', 'warning');
			$this -> go_to(array('controller' => 'administradores', 'action' => 'departamentos'));
		}

		if (is_numeric($id))
			$this -> title = 'Editar rol de administración';

		if (is_null($id))
			$this -> title = 'Nuevo rol de administración';

		if ($this -> is_post()) {

			if ($this -> Backend -> saveDepartamento($this -> data['departamentos'], $id)) {
				if (is_null($id))
					$this -> Session -> setFlash('Rol creado', 'success');
				if (is_numeric($id))
					$this -> Session -> setFlash('Rol actualizado', 'success');
				$this -> go_to(array('controller' => 'administradores', 'action' => 'departamentos'));
			} else {
				$this -> Session -> setFlash('No pudimos actualizar el rol', 'warning');
			}

		} else {

			if (is_numeric($id)) {
				$this -> data['departamentos'] = $this -> Backend -> getOneRow(array('from' => 'departamentos', 'where' => "id = $id"));
			}

		}
	}

	public function admin_permisos($id = null, $context = null) {
		if (empty($id) or empty($context) or !in_array($context, self::$allowedContext))
			$this -> set_404();

		// $permisosNivelSuperior = array();

		switch($context) {
			case 'departamento' :
				$table = $context . 's';
				break;
			case 'administrador' :
				$table = $context . 'es';
				// $permisosNivelSuperior = $this -> Backend -> getPermisosDepartamentoPadre($id);
                // breakpoint($permisosNivelSuperior);
				break;
		}

		if ($this -> is_post()) {
			if ($this -> Backend -> setPermisos($id, $table, $this -> data['permisos'])) {
				$this -> Session -> setFlash('Permisos actualizados', 'success');
			} else {
				$this -> Session -> setFlash('No se pudieron actualizar los permisos', 'warning');
			}
		}

		$permisos = $this -> Backend -> getPermisos($id, $table);

        // breakpoint($permisos);

		$target = $this -> Backend -> getOneRow(array('from' => $table, 'where' => "id = $id"));

		if (isset($target['nombres']))
			$target['titulo'] = $target['nombres'];

		$this -> title = 'Permisos de ' . $target['titulo'];

		$this -> set('permisos', $permisos);

		$this -> set('context', $table);
		$this -> set('target_context', $context);
		$this -> set('id', $id);

		// $this -> set('sup', $permisosNivelSuperior);

	}

	public function admin_miperfil() {

		if( !$this -> Security -> hasPermission('administradores_root') ){
			unset($this -> data['administradores']['departamentos_id']);
		}

		if ($this -> is_post()) {
            $this -> Backend -> saveAdministrador($this -> data, $this -> Session -> user('id'));
			if ($this -> Backend -> response['status']) {
				$this -> Session -> setFlash('Tu perfil se ha guardado.', 'success');

				$user_id = $this -> Session -> user('id');

				$newProfileData = $this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => "id = $user_id"));

				$this -> Session -> update($newProfileData);

				$this -> go_to($this -> request);
			} else {
                $this -> Session -> setFlash($this -> Backend -> response['msg'], $this -> Backend -> response['class']);
            }
		} else {
			$this -> data['administradores'] = $this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => 'id = ' . $this -> Session -> user('id')));
            $this -> data['_']['roles'] = $this -> Backend -> getUserRolesArray($this -> Session -> user('id'));
		}

		unset($this -> data['administradores']['password']);

		$this -> set('departamentos', $this -> Backend -> getArrayPair(array('from' => 'departamentos')));
	}

	public function admin_logout() {
        $this -> Log -> write("user_logout", "El usuario finaliza correctamente su sesión en el Backend." );
		$this -> Session -> unAuth();
		$this -> go_to(array($this -> afterLogoutAction));
	}

	public function admin_login() {
		if ($this -> Session -> is_logged())
			$this -> go_to($this -> afterLoginAction);

		$this -> title = 'Login';

		if (Routes::is_ajax()) {
			$this -> _try_login();
		} else {
			$this -> template = 'adminLogin';
		}

	}

    /* ROLE AS */
    public function admin_roleas ($id = null) {
        // VALIDATE
        if(is_null($id)) $this -> set_404();

        // RESPONSE
        $response = $this -> Multidepartment -> loadDeparment($id);

        // VALIDATION
        if($response){
            // SETS LOG
            $this -> Log -> write("user_enrolment", sprintf("El usario se ha cambiado al rol: '%s'", $this ->  Session -> user(MultidepartmentComponent::$roleFiledInUserSession)));
            // SETS FLASH
            $this -> Session -> setFlash(sprintf('Has cambiado exitosamente de departamento a: %s',
                 $this -> Session -> user(MultidepartmentComponent::$roleFiledInUserSession)
            ), 'success');
        } else {
            $this -> Session -> setFlash(sprintf('Ocurrió un error en el cambio de departamento'), 'success');
        }

        $this -> go_to(array('controller' => 'administradores', 'action' => 'miperfil'));
    }

	private function _try_login() {

		$user = $this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => sprintf('email like "%s"', trim($this -> data['username']))));

		if (!empty($user) and $user['password'] == md5($this -> data['password']) and !$user['bloquear_acceso']) {
            // ESCRIBE EN LOGIN
			$this -> Session -> Auth($user);
            $this -> Log -> write("user_login", "Login exitoso desde backend como: " . $this ->  Session -> user(MultidepartmentComponent::$roleFiledInUserSession));
			$this -> Session -> setFlash(sprintf('Bienvenido %s', $this -> Session -> user('nombres')), 'info');
			$this -> json(array('login_status' => 'success', 'redirect_url' => $this -> url($this -> afterLoginAction)));
		}

		$this -> json(array('login_status' => 'invalid'));
	}

	public function admin_forgottenpassword() {
		$this -> title = 'Recuperado de contraseña';

		if ($this -> Session -> is_logged())
			$this -> go_to($this -> afterLoginAction);

		if (Routes::is_ajax()) {
			$this -> _procesa_envio_de_correo_para_recuperacion();
		} else {
			$this -> template = 'adminLogin';
		}
	}

	private function _procesa_envio_de_correo_para_recuperacion() {
		// Revisa el usuario
		if (!$this -> _existe_usuario_por_recuperar()) $this -> json($this -> response);
		// Construye el correo electrónico.
		$this -> _build_password_recovery();
		// Agrega los datos y requicitos para el envío.
		$this -> _send_password_reconfig_mail();
		// Procesamiento final
		$this -> Mail -> send();
		// Salida JSON
		$this -> json($this -> Mail -> response);
	}

	private function _existe_usuario_por_recuperar() {
		$this -> recovery = $this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => sprintf('email = "%s"', $this -> data['recovery']['email'])));
		if (empty($this -> recovery)) {
			$this -> response = array('status' => false, 'msg' => 'El usuario no está registrado, o no se ha escrito correctamente el correo electrónico.');
			return false;
		}
		return true;
	}

	private function _build_password_recovery() {
		$this -> Mail -> Mailtemplate -> mailSet('userId', $this -> recovery['id']);
		$this -> Mail -> Mailtemplate -> mailSet('userHash', $this -> hashify($this -> recovery['password']));
		$this -> Mail -> Mailtemplate -> mailSet('appName', self::$appName);
		$this -> Mail -> Mailtemplate -> mailSet('nombreUsuario', $this -> recovery['nombres']);
	}

	private function _send_password_reconfig_mail() {
		// Preparado del Template
		$this -> Mail -> Mailtemplate -> setView('restaurarPassword');
		$this -> Mail -> Mailtemplate -> setMailTemplate('generic');
		$this -> Mail -> Mailtemplate -> render();

		// Configuraciones especiales
		$this -> Mail -> subject = self::$appName . ' | Recuperación de contraseña';
		$this -> Mail -> agregaDestinatario($this -> recovery['email']);
	}

	public function admin_passwordbackemailsent() {
		if ($this -> Session -> is_logged())
			$this -> go_to($this -> afterLoginAction);

		$this -> title = 'Correo enviado';

		$this -> template = 'adminLogin';

	}

	public function admin_resetpassword($user_id = null, $pwdHash = null) {
		// Valida que efectivamente no esté en sesión.
		if ($this -> Session -> is_logged()) {
			$this -> Session -> setFlash('Estás exitosamente logeado, puedes cambiar tu contraseña desde aqui.', 'info');
			$this -> go_to(array('controller' => 'administradores', 'action' => 'miperfil'));
		}

		$this -> title = 'Restaurar contraseña';

		$this -> template = 'adminLogin';

		// Validado de ID
		if (!is_numeric($user_id))
			$this -> set_404();

		// Busqueda de usuario
		$this -> usuario = $this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => "id = $user_id"));

		if($this -> usuario['bloquear_acceso'])
			$this -> set_404();

		// Si no hay usuario, finaliza.
		if (empty($this -> usuario))
			$this -> set_404();

		// Validado de Hash
		if ($this -> hashify($this -> usuario['password']) != $pwdHash)
			$this -> set_404();

		if ($this -> is_ajax()) {
			$this -> _save_reseted_password($user_id);
		}
	}

	private function _save_reseted_password($user_id) {
        $usuario = array();
		if (strlen($this -> data['reset']['pass1']) < 6)
			$this -> json(array('status' => false, 'msg' => 'La contraseña debe ser mayor a 6 caracteres.'));

		if ($this -> data['reset']['pass1'] == $this -> data['reset']['pass2']) {

			$usuario['administradores'] = array('password' => $this -> data['reset']['pass1']);

            if ($this -> Backend -> saveAdministrador($usuario, $user_id, $check_roles = FALSE)) {
				if($this -> autoLoginOnPWDReset){
					$this -> Session -> setFlash('Contraseña actualizada. Bienvenido');
					$this -> Session -> Auth($this -> Backend -> getOneRow(array('from' => 'administradores', 'where' => "id = $user_id")));
				}
				$this -> json(array('status' => true, 'msg' => 'Contraseña actualizada'));
			} else {
				$this -> json(array('status' => false, 'msg' => 'Ocurrió un error actualiznado la contraseña. Contacta a Administración.'));
			}

		} else {
			$this -> json(array('status' => false, 'msg' => 'Las contraseñas no coinciden'));
		}
	}

    /**
     * admin_logs
     *
     * Prefiltro para sección de logs
     *
     * @Author Daniel Lepe
     * @Version 1.0
     * @Date 13/08/2015
     */
    public function admin_logs(){
        // INIT
        $logTypes = array();
        $this -> title = i("Registros");

        // VALIDA PERMISO DE ACCESO A ACCIÓN
        if(!$this -> Security -> hasPermission('logs_read'))
            $this -> set_404();

        // OBTIENE RESTO DE TIPOS DE LOG
        $logTypes = $this -> Backend -> getLogTypes();



        if(empty($logTypes)){
            $this -> Session -> setFlash('No hay registros de Log', 'info');
            $this -> go_to($this -> referer());
        }

        // ENVIA A LA VISTA
        $this -> set('logTypes', $logTypes);
    }

    /**
     * admin_logsfilter
     *
     * Primero el sistema detecta los eventos de login.
     * Esta acción mostrará todos los usuarios cuando tenga acceso a logs_read, de otra
     * manera denegará el acceso general.
     *
     * Permite paginar los datos filtrando principalmente por $type y en segundo lugar por usuario.
     *
     * @Author Daniel Lepe
     * @Version 1.0
     * @Date 13/08/2015
     */
    public function admin_logsfilter($type = null, $user = null){
        // INIT
        $logs = array(); $users = array();

        // VALIDA PERMISO DE ACCESO A ACCIÓN
        if(!$this -> Security -> hasPermission('logs_read'))
            $this -> set_404();

        // DEVUELVE DATOS SEGÚN PARÁMETROS ENVIADOS.
        $logs = $this -> Backend -> getLogs($type, $user);

        // OBTIENE RESTO DE USUARIOS
        $users = $this -> Backend -> getArrayPair(array('from' => 'administradores', 'values' => 'id, nombres', 'order_by' => 'nombres ASC'));

        // ESTABLECE ADMINISTRADORES EN LOGS
        foreach($logs as $k => $l){
            // UNSETTERS
            unset($logs[$k]['id']);
            unset($logs[$k]['administradores_id']);
            // ASSIGNMENTS
            $logs[$k]['RESPONSABLE'] = $users[$l['administradores_id']];
        }

        // ENVIA A LA VISTA
        foreach(array('logs',  'users') as $v)
            $this -> set($v, ${$v});
    }

    /**
     * admin_cleanlogs
     *
     * Permite borrar los logs de usuarios. Requiere el permiso logs_clean
     *
     * @Author Daniel Lepe
     * @Version 1.0
     * @Date 13/08/2015
     */
    public function admin_cleanlogs($type = null, $user = null){
        // VALIDA PERMISO DE ACCESO A ACCIÓN
        if(!$this -> Security -> hasPermission('logs_clean'))
            $this -> set_404();

        // EJECUTA LIMPIEZA
        $this -> Backend -> cleanLogs($type, $user);

        // NOTIFICA AL USUARIO
        $this -> Session -> setFlash($this -> Backend -> response['msg'], $this -> Backend -> response['class']);

        // LOG DE LIMPIEZA
        if($this -> Backend -> response['status'])
            $this -> Log -> write('full_clean', 'Efectúa limpieza del log histórico.');

        // REDIRECCIONA A LOGS
        $this -> go_to($this -> referer());
    }

}
