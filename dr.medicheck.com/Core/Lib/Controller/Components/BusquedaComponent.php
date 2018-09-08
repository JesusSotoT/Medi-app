<?php

class BusquedaComponent extends Component {
	public $models = array();
	static $keyWord = null;
	public function set_keyword($key){
		self::$keyWord = $key;	
	}
	public static function get_keyword(){
		return self::$keyWord;
	}
}
