<?php

class Sync extends AppController {
    public $models  = array('Importador');
    public $to_import = array(

    );
    // INDEX
    public function admin_index(){
        $this -> title = "Sincronizador de base datos";
        $this -> set('to_import', $this -> to_import);
    }

    // PERFORM
    public function admin_sync( $tabla = null ){
        if(is_string($tabla))
            $this -> Importador -> sync($tabla);
        $this -> json($this -> Importador -> response);
    }
}
