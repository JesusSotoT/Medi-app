<?php

/**
 *
 * LOG COMPONENT CLASS
 *
 * Provee de una sencilla interfáz para su uso en controladores, 
 * donde se guardará en la tabla de log todos los registros que 
 * este le pase, anidando los datos via componente al registro 
 * con declaración explícita de usuario.
 * 
 * @ Author Daniel Lepe
 * @ Version 1.0
 * @ Date 12/08/2015
 */

class LogComponent extends Component {
    
    public $models = array('Backend');
    
    /**
     *
     * test_access_to_session
     *
     * Permite probar el acceso al recurso de Session.
     * 
     * @ Author Daniel Lepe
     * @ Version 1.0
     * @ Date 12/08/2015
     */
    public function test_access_to_session () {
        breakpoint($this -> Session -> user());
    }
    
    /**
     *
     * write
     *
     * Escribe en la tabla de Log los datos enviados desde controlador.
     * 
     * @ Author Daniel Lepe
     * @ Version 1.0
     * @ Date 12/08/2015
     */
    public function write($logType, $description, $value = null){
        if(!$this -> _write_validate($logType, $description, $value))
            return false;   // SI LLEGA
        
        // CONSTRUYE EL REGISTRO
        $data = array(
            'administradores_id'    => $this -> Session -> user('id'),
            'type'                  => $logType,
            'description'           => $description,
            'value'                 => $value,
            'created'               => date('Y-m-d H:i:s')
        );
        
        // ALMACENA
        return $this -> Backend -> insert('logs', $data);
        
    }
    
    /**
     *
     * sys_write
     *
     * Escribe en la tabla de Log los datos enviados desde controlador 
     * sin tomar en cuenta el id del usuario actual. 
     * Útil para logs de sistema.
     * 
     * @ Author Daniel Lepe
     * @ Version 1.0
     * @ Date 12/08/2015
     */
    public function sys_write($logType, $description, $value = null){
        if(!$this -> _write_validate($logType, $description, $value))
            return false;   // SI LLEGA
        
        // CONSTRUYE EL REGISTRO
        $data = array(
            'type'                  => $logType,
            'description'           => $description,
            'value'                 => $value,
            'created'               => date('Y-m-d H:i:s')
        );
        
        // ALMACENA
        return $this -> Backend -> insert('logs', $data);
        
    }
    
    /**
     *
     * _write_validate
     *
     * Valida los parámetros enviados a la función de Log.
     * 
     * @ Author Daniel Lepe
     * @ Version 1.0
     * @ Date 12/08/2015
     */
    private function _write_validate($logType, $description, $value){
        if(!is_string($logType) or !is_string($description))
            die('[LogComponent::write] No es operable el componente si $logType o $description no son textos');
            
        if(!(3 <= strlen($logType)) or !(strlen($logType) <= 45))
            die('[LogComponent::write] El tamaño de $logType debe oscilar entre 3 y 45 caracteres.');
            
        if(!(5 <= strlen($description)) or !(strlen($description) <= 255))
            die('[LogComponent::write] El tamaño de $description debe oscilar entre 5 y 255 caracteres.');
        
        if(is_array($value))
            die('[LogComponent::write] $value Es un campo opcional que por fuerza debe ser texto.');
        
        if(!(strlen($value) <= 12))
            die('[LogComponent::write] El tamaño de $value debe ser menor o igual a 12');
        
        // PUEDE CONTINUAR
        return true;
    }
    
}