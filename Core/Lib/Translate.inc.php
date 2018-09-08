<?php

/**
 * CLASE PARA EL MANEJO DE LAS DE TRADUCCIONES
 * 
 * FUNCINOA EN CONJUNTO CON LA VARIABLE $PREFIX LOCALIZADA EN CONFIGS.INC
 * 
 * REQUIERE UN IDIOMA POR DEFECTO, EL CUAL CARGARÁ POR OMISIÓN DE LENGUAJE EN LA URL EXPLÍCITA.
 * 
 * ADEMÁS, OFRECE HERRAMIENTAS CONCRETAS PARA EL USO DE LA BIBLIOTECA DESDE CUALQUIER SECCIÓN DEL COMPLEJO MVC Lite.
 * @Author Daniel Lepe
 * @Version 1.0
 * @Date 10/08/2015
 */

class Translate extends AppConfig {
    
    public static $translation = null;
    public static $selectedLang = null;
    public static $untranslated = array();
    
    // SIN POSIBLE LENGUAJE POR DEFECTO, CARGA DEFAULTS
    public static function set_defaults (){
        if(isset(self::$languagesCFG['default_prefix'])){
            // LOOP BUSCADOR
            foreach(self::$languagesCFG['dictionaries'] as $lang){
                if($lang['prefix']==self::$languagesCFG['default_prefix']){
                    $def = $lang;
                }
            }

            // VALIDATES IF DEFAULT LANGUAGE IS FOUND
            if(empty($def))
                die('$languagesCFG[default_prefix] Default dictionary missmatch!');

            // SETS DEFAULT LANGUAGE
            self::$selectedLang = $def;

        } else {
            die('$languagesCFG[default_prefix] NOT SET, DISABLE $languagesCFG[allow_translations]');
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

    // IMPRIME LA TRADUCCIÓN
    public static function print_translation($string, $debug=FALSE) {

        if (isset(self::$translation[$string])) {
            return self::$translation[$string];
        } else {
            // DEPURA SI ES NECESARIO
            if($debug){
                debug($aTranslations);
                debug($string);
            }
            if(self::$languagesCFG['dump_untranslated']){
                self::$untranslated[] = $string;
            }
            return $string;
        }
    }

    // IMPRIME TEXTO SIN TRADUCIR
    public static function print_untranslated(){
        if(self::$languagesCFG['allow_translations'] 
            and self::$languagesCFG['dump_untranslated']
            and !empty(self::$untranslated)){
            // PRINT HEADERS
            echo sprintf("[UNAVAILABLE TRANSLATIONS (Core/Langs/%s.ini)] <BR/><br/>", self::$selectedLang['file']);
            foreach(self::$untranslated as $t){
                echo sprintf("%s = \"%s\"<br/>", $t, $t);
            }
            die('<br/><br/>;TO HIDE THIS TRANSLATE EVERYTHING OR SET AppConfig::$languagesCFG[\'dump_untranslated\'] = false;');
        }
    }
    
    // GET PREFIX
    public static function get_prefix(){
        return self::$selectedLang['prefix'];
    }
}

// ALIAS CON TRADUCCIONES DISPONIBLES PARA SU USO EN TODO EL MVCLITE
function i ($string, $debug = false){
    return Translate::print_translation($string, $debug);
}