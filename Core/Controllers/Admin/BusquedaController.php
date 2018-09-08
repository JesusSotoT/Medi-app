<?php

/**
 * Clase principal de búsqueda. 
 * 
 * En esta sección se deben cargar todas las tablas que deben ser buscadas bajo ciertos criterios. Como muestra
 * se esteblecerá la búsqueda de Administradores.
 * 
 * @Author Daniel Lepe 2014
 * @Version 1.0
 */
 class Busqueda extends AppController {
 	public $models = array('Backend');
	private $results = array();
	public $components = array('Busqueda', 'Security', 'Menu', 'Multidepartment');
	private $defaultTitle = '%s encontrados:'; // Debe llevar %s para el sprintf
	
	
	// Configura el constructor de búsqueda.
	private $searchConstructor = array(
		array(
			'table' 	=> 'administradores',
			'title' 	=> 'Usuario(s) encontrados en el sistema:',
			'fields' 	=> array('nombres', 'email'),
			'url' 		=> array('action' => 'administrador', 'controller' => 'administradores'),
			'params' 	=> array('id'),
			'modal'		=> false // Resuelve la liga con una solicitud normal.
		), 
		array(
			'table' => 'departamentos',
			'fields' 	=> array('titulo', 'descripcion'),
			'url' 		=> array('action' => 'departamento', 'controller' => 'administradores'),
			'params' 	=> array('id'),
			'modal'		=> true // Resuelve la liga en un modal
		), 
	);
	
 	/**
	 * admin_index
	 * 
	 * @Author Daniel Lepe 2014
	 * @Version 1.0
	 */
	 
	 public function admin_r($search = null){
	 	
	 	if(is_null($search)){
	 		$this -> Session -> set_flash('Debes buscar algo para que funcione ésta sección.', 'info');
	 		$this -> go_to($this -> afterLoginAction);
	 	}
		
		$this -> Busqueda ->  set_keyword($search);
			
	 	$this -> title = "Busqueda de " . $search;
		
		$this -> _recursive_search();
		
		if(empty($this -> results)){
			$this -> view = 'admin_empty_results';	
		}
		$this -> set('search', $search);
		$this -> set('results', $this -> results);
	 }
	 
	 private function _recursive_search(){
	 	// Analiza el constructor
	 	$this -> _analyize_search_constructor();
		
	 	foreach($this -> searchConstructor as $configs){
	 		
	 		if(!isset($configs['modal']))
				$configs['modal'] = false;
			
	 		if(!isset($configs['title']))
				$configs['title'] = sprintf($this -> defaultTitle, ucfirst(strtolower(str_replace('_', ' ', $configs['table']))));
			
			// Procesa resultados
			$results = array(
				'title' 	=> $configs['title'],
				'modal' 	=> $configs['modal'],
				'results' 	=> $this -> _search($configs['table'], $configs['fields'], $configs['params'])	
			);
			
			$this -> results[] = array_merge($results, $configs);
			
			// Afina resultados encontrados
			$this -> _afina_resultados_encontrados();
	 	}
	 }
	 
	 private function _afina_resultados_encontrados(){
	 	foreach($this -> results as $k => $r){
	 		if(empty($r['results'])){
	 			unset($this -> results[$k]);
	 		} else {
	 			$this -> results[$k]['results'] = $this -> _build_actions($r['results'], $r['url'], $r['params']);
	 		}
	 	}
	 }
	 
	 private function _build_actions ($resultset, $urlCfg, $params){
	 	foreach($resultset as $k => $r){
	 		$actions = $urlCfg;
	 		foreach($params as $field){
	 			$actions[] = $r[$field];
	 		}
	 		$resultset[$k]['actions'] = $actions; 
	 	}
	 	return $resultset;
	 }
	 
	 private function _search($table, $searchFields, $params){
	 	$where = array(); $fields = array();
		$keyWord = BusquedaComponent::get_keyword();
		foreach($searchFields as $field){
			$where[ ] = "$field LIKE '%$keyWord%'";
		}
		$searchFields = array_merge($params, $searchFields);
		$resultset =  $this -> Backend -> getAllRows(array('from' => $table, 'values' => implode(', ', $searchFields), 'where' => implode(' OR ', $where)));
		return $resultset;
	 }
	 
	 private function _analyize_search_constructor(){
	 	foreach($this -> searchConstructor as $configs){
	 		if(!is_array($configs))
				die('$SearchConstructor, debe ser un array Multinivel');
			if(!isset($configs['fields']) or !is_array($configs['fields']))
				die('$SearchConstructor, debe ser un array Multinivel que contenga en <strong>fields</strong> un array con los campos a buscar en la tabla.');
			if(!isset($configs['url']) or !is_array($configs['url']))
				die('$SearchConstructor, debe ser un array Multinivel que contenga en <strong>url</strong> un array la configuración de la URL a disparar cuando el usuario le de click en el resultado de búsqueda.');
			if(!isset($configs['params']) or !is_array($configs['params']))
				die('$SearchConstructor, debe ser un array Multinivel que contenga en <strong>params</strong> un array la configuración de la los parámetros obtenidos de la busqueda que se pasarán a la URL encontranda.');
		}
		return true;
	 }
	 
 }
