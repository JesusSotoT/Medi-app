<?php
/**
 * FALL BACK DE TRANSLATE OFRECE LA FUNCION PLANA i 
 * PARA EVITAR INDEFINICIONES O FALLAS EN EL COMPORTAMIENTO NORMAL DE MVC
 * 
 * Ahora adopta una funcionalidad similar a Translate, pero de manera mas modesta.
 * 
 * @Author Daniel Lepe
 * @Version 1.0
 * @Date 10/08/2015
 */

class DictionaryInc extends AppConfig {
    // CONFIGS
    public static $defaultLangFile = 'es_mx';
        
    // STATICS
    public static $selectedLangFile = null;
    public static $dictionary = null;
    
    // INIT
    public static function init () {
        // DEFINE EL DICCIONARIO A CARGAR
        if (is_null(self::$selectedLangFile)) {
            if(isset(APP::$languagesCFG)){
                foreach(APP::$languagesCFG['dictionaries'] as $l){
                    if($l['prefix'] == APP::$languagesCFG['default_prefix']){
                        self::$selectedLangFile = $l['file'];
                    }
                }
            } else {
                self::$selectedLangFile = self::$defaultLangFile;
            }
        }
        
        // CARGA EL DICCIONARIO SI ES QUE NO LO HA CARGADO ANTERIORMENTE
        if(empty(self::$dictionary))
            self::load_dictionary();
        
        if(self::$languagesCFG['debug_translation'])
            breakpoint(self::$dictionary);
    }
    
    // LOAD DICTIONARY
    public static function load_dictionary () {
        // VALIDATE AND LOADS
        if (self::$selectedLangFile != NULL) {
            $route = self::path('Langs') . self::$selectedLangFile . '.ini';
            self::$dictionary = parse_ini_file($route);
        }
    }
    
    // CARGA LAS TRADUCCIONES DESDE LOS DICCIONARIOS
    public static function load_translations() {
        // VALIDATE AND LOADS
        if (self::$selectedLang['file'] != NULL) {
            $route = self::path('Langs') . self::$selectedLang['file'] . '.ini';
            self::$translation = parse_ini_file($route);
        }
        // IF PRINT_NON
        // IF DEBUG IS REQUIRED
        if(self::$languagesCFG['debug_translation']){
            breakpoint(self::$translation);
        }
    }
    
    // DICCIONARIO
    public static function dictionary( $string, $debug=FALSE ){
        // IMPRIME LA TRADUCCIÓN
        if (isset(self::$dictionary[$string])) {
            return self::$dictionary[$string];
        } else {
            return $string;
        }
    }
}

function i ($string){
    DictionaryInc::init();
    return DictionaryInc::dictionary($string);
}