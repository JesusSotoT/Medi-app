<?php

/**
 * DownloadablesComponent
 *
 * Ofrece un conjunto de herramientas para la creación de archivos CSV, y potencialmente otros formatos.
 * 
 * Las herramientas generales son
 * 
 * + make_csv ($data = array(), $name=string) $data debe ser un arreglo multinivle y string es opcional
 * + download () Procesa encabezados y el archivo para detecta
 * 
 * @Author Daniel Lepe 2015
 * @Version 1.0
 * @Date 01/09/2015 
 */

class DownloadablesComponent extends Component {
    // INIT
    public $keep_file = false;
    public $response = array('status' => true);
    private $file_current = null;
    private $file_names = array();
    private $file = array();
    private $data_for_file = array();
    private $type = null;
    private $headers = array(
        'csv'   =>  'text/csv',
        'zip'   => 'application/zip'
    );
    
    public function __destruct () {
        // BORRA EL ARCHIVO SI EL FLAG DE MANTENIMIENTO DE ARCHIVO ES FALSO.
        if($this -> keep_file == false){
            foreach($this -> file_names as $f){
                unlink(APP::temp($f));
            }
        }
    }
    
    // MAKERS
    public function make_csv($data = array(), $name=null){
        if(!$this -> response['status']){
            return false;
        }elseif(empty($data) or !isset($data[0])){
            $this -> response['status'] = false;
            $this -> response['msg'] = "No se puede continuar, \$data está vacío.";
            $this -> response['class'] = 'warning';
            return false;
        } elseif (!is_array($data[0])) {
            $this -> response['status'] = false;
            $this -> response['msg'] = "No se puede continuar, \$data no es multinivel.";
            $this -> response['class'] = 'warning';
            return false;
        }
        
        // SIGNA DATOS
        $this -> data_for_file = $data;
        
        // NOMBRE
        $this -> _build_name($name, 'csv');
        $this -> type = 'csv';
        
        // CONSTRUYE CSV
        if(is_string($this -> file_current)) {
            $this -> _build_csv();
        } else {
            die('Ocurrió un error');
        }
    }
    
    // BUILD NAME
    private function _build_name($name, $ext = null){
        // INIT
        $filename = null;
        
        // STRING
        if(is_string($name)){
            $filename = $name;
        } else {
            $filename = uniqid($ext);
        }
        
        // ASIGNA EXTENCIÓN AL FICHERO
        if(!preg_match('/\.' . $ext . '$/', $filename))
            $filename .= '.' . $ext;
        
        $this -> file_names [] = $filename;
        $this -> file_current = $filename;
    }
    
    // BUILD CSV
    private function _build_csv ( ) {
        // CHECK AND OPEN FILES
        $file = fopen(APP::temp($this -> file_current), "w+");
        
        // ADD HEADERS
        $headers = $this -> data_for_file[0];
        foreach($headers as $k => $t){ $headers[$k] = $k; }
        fputcsv($file, $headers);
        
        // DATA LOOP
        foreach ($this -> data_for_file as $line) {
            fputcsv($file, $line);
        }

        // CLOSE
        fclose($file); 
        
        $this -> response['status'] = true;
    }
    
    // MAKE ALL FILES ZIP
    public function zip_all ( $name = 'compressed' ) {
        // VALIDA
        if(!class_exists('ZipArchive'))
            die('ZipArchive No existe en este servidor!');
        
        // BUILD NAME
        $this -> _build_name($name, 'zip');
        
        // CREA ZIP
        $zip = new ZipArchive();
        
        // VALIDA EL RECIÉN CREADO ARCHIVO
        if($zip -> open(APP::temp($this -> file_current), 
						ZipArchive::CREATE)!== TRUE)
		{
            die('No se puede crear un ZIP en este contexto.');
        }
        
        // AGREGA ARCHIVOS
        foreach($this -> file_names as $k => $n){
            if($n != $this -> file_current){
                ($zip -> addFile(APP::temp($n), $n));
            }
        }
        
        // FINALIZA
        $zip -> close();
    }
    
    // DOWNLOAD
    public function download() {
        if($this -> response['status']){
            header(sprintf('Content-Type: %s', $this -> headers['csv']));
            header(sprintf('Content-Disposition: attachment; filename="%s"', $this -> file_current));
            readfile(APP::temp($this -> file_current));
            die();
        }   
    }
    
    // GET RESOURCE LOCATION
    public function get_resource_location() {
        return APP::temp($this -> file_current);  
    }
    
}