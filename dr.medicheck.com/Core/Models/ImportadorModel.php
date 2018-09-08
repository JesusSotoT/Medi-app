<?php

class ImportadorModel extends Model {
    // ARREGLO GENERAL DE DATOS
    public $data = array();
    
    // FUNCION PRINCIPAL
    public function sync ($tabla){
        // APERTURA EL TIEMPO ILIMITADAMANTE.
        set_time_limit(-1);
        
        // CONFIGURA CONEXIÓN COMO ORIGEN
        $this -> reset_connection ('sync');
        
        // OBTIENE BLOQUE DE DATOS DE TABLA DE ORIGEN
        $this -> data = $this -> getAllRows(array('from' => $tabla));
        
        // SETS COUNTS
        $this -> response['rows'] = count($this -> data);
        
        // REVISA CONTENIDO
        if(count($this -> response['rows']) ==  0){
            $this -> response['status'] = true;
            return true;
        }
        
        // RECONFIGURA CONEXIÓN COMO DESTINO
        $this -> reset_connection ('local');
        
        // DESHABILITA REVISIÓN DE LLAVES FORÁNEAS
        $this -> executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        
        // LIMPIA LA TABLA ACTUAL
        $this -> executeQuery('truncate ' . $tabla);
        
        // VUELCA LOS DATOS EN LA TABLA
        foreach($this -> data as $row){
            if(!$this -> insert($tabla, $row)){
                $this -> response['status'] = false;
                $this -> response['msg'] = 'Error en insert';
                return false;
            }
        }
        
        // FINALIZA
        $this -> response['status'] = true;
        $this -> response['msg'] = 'Terminado.';
        return true;
    }
    
}