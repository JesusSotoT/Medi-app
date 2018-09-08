<?php

/**
 *
 * HTML HELPER
 *
 * Helper básico para la creación de Ligas, Etiquetas de imágenes,
 * migas de pan y  paginadores.
 *
 * @Author Daniel Lepe
 * @Version 2.0
 */

class HtmlHelper extends AppConfig {

    // PUBLIC INITS
    public $definitionList = '';
    public $prettyURL = true;

    public $cardsGrid = array (
        'cellClass'      => 'col-sm-6 col-md-4 col-lg-3',
        'thumbnail'      => 'thumbnail',
        'caption'        => 'caption',
        'thumnailWidth'  => '100%',
        'activeClass'    => 'thumbnail-clickable'
    );

    // PROTECTED INITS
    protected $breadcrumbs = array ( );

    // PRIVATED INITS
    private $place_holders = array( 'HTML_BODY' => "<node_ph id='id_ph' class='classes_ph'>html_ph</node_ph>");

    private $pagination_cfg  = array(
        'wrapper'       => 'UL#paginator',
        'row'           => 'LI.page',
        'back'          => '.back',
        'forth'         => '.forth',
        'page'          => '.page',
        'active'        => '.page.current',
        'inactive'      => '.page.inactive',
        'first'         => '.first',
        'last'          => '.last',
        'expose'        => 2,
        'back_text'     => '<',
        'forth_text'    => '>',
        'first_text'    => '<<',
        'last_text'     => '>>'
    );

    public function __construct(){
      $this -> prettyURL = (isset(APP::$allow_pretty_url))? APP::$allow_pretty_url : true;
    }

    /**
     * DL
     *
     * Crea un listado de definiciones a partir de un array mononivel.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function dl ( $data ) {
        if ( !is_array ( $data ) or empty ( $data ) )
            return null;

        $class = $this -> definitionList;

        $return = "<dl class='$class'>";

        foreach ( $data as $label => $value ) {
            $return .= sprintf ( "<dt>%s:</dt>", strtoupper ( str_replace ( '_', ' ', $label ) ) );
            $return .= sprintf ( "<dd>&nbsp;%s</dd>", $value );
        }

        $return .= "</dl>";

        return $return;
    }

    /**
     * Encode URL
     *
     * Codifica una cadena de texto a URL para que sea URL friendly.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function encodeURL ( $url ) {

        $url = preg_replace ( '/[ ]|\//', '_', $url );
        $url = strtr ( strtolower ( $url ), array (
            'ñ' => 'n',
            'ü' => 'u',
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'ú'
        ) );
        return $url;
    }

    /**
     * Add Crumb
     *
     * Agrega una miga de pan.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function addCrumb ( $title, $url = null, $options = array() ) {
        $active = false;

        if ( isset ( $options [ 'active' ] ) )
            $active = true;

        $this -> breadcrumbs [ ] = array (
            'title' => $title,
            'url' => $url,
            'options' => $options,
            'active' => $active
        );

    }

    /**
     * Breadcrumbs
     *
     * Crea el UL de las migas de pan.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function breadcrumbs ( $options = array() ) {

        $method = 'ol';
        $class = 'breadcrumb';

        if ( isset ( $options [ 'method' ] ) )
            $method = $options [ 'method' ];

        if ( isset ( $options [ 'class' ] ) )
            $class = $options [ 'class' ];

        $breadcrumb = "";

        if ( !empty ( $this -> breadcrumbs ) ) {

            $breadcrumb .= "<$method class='$class'>";

            foreach ( $this -> breadcrumbs as $b ) {
                if ( $b [ 'active' ] or is_null ( $b [ 'url' ] ) ) {
                    $active = null;
                    if ( $b [ 'active' ] )
                        $active = 'active';
                    $breadcrumb .= "<li class='$active'><span> " . $b [ 'title' ] . " </span></li>";
                } else {
                    $breadcrumb .= "<li>" . $this -> link ( $b [ 'title' ], $b [ 'url' ], $b [ 'options' ] ) . "</li>";
                }
            }

            $breadcrumb .= "</$method>";

        }

        return $breadcrumb;
    }

    /**
     * Link
     *
     * Funcion que permite la creación de Links siguiendo el modo
     * [URLBase]/controlador/accion/parametros
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function link ( $text, $url, $options = array(), $msg = null ) {
        $url = $this -> url ( $url );

        if ( is_array ( $options ) ) {
            $opt_string = "";
            foreach ( $options as $attr => $val ) {
                $opt_string .= " $attr = '$val' ";
            }
        }
        if ( $msg != NULL ) {
            return "<a onclick='return confirm(\"$msg\")' href='$url' $opt_string>$text</a>";
        } else {
            return "<a href='$url' $opt_string>$text</a>";
        }
    }

    /**
     * Table Link
     *
     * Link simple, pero foramtea específicamente para ser usado en tablas.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function btnLink ( $title, $url, $class = null, $icon = 'entypo-right-dir', $options = array() ) {

        if ( is_null ( $class ) )
            $class = 'default';

        $msg = null;

        if ( preg_match ( '/^glyphicon\-/', $icon ) ) {
            $icon = 'glyphicon ' . $icon;
        }

        if ( preg_match ( '/^fa\-/', $icon ) ) {
            $icon = 'fa ' . $icon;
        }

        if ( isset ( $url [ 'confirm' ] ) ) {
            $msg = $url [ 'confirm' ];
            unset ( $url [ 'confirm' ] );
        }

        $text = sprintf ( "<i class='%s'></i> %s", $icon, $title );

        if ( !isset ( $options [ 'class' ] ) )
            $options [ 'class' ] = 'btn btn-sm btn-icon icon-left btn-' . $class;

        return " " . $this -> link ( $text, $url, $options, $msg );
    }

    /**
     * Script
     *
     *  Esta función devuelve las etiquetas correctas para cargar un Javascript.
     *
     * Ahora permite que se configure via $loadResourcesFrom un webroot en otra parte del servidor.
     * Ahora permite la bandera $printServaer, con la que la URL aparecerá indicando el nombre del servidor.
     *
     * Ahora permite que se le pase la opción context especificar un archivo de contexto distinto
     *
     * @Author Daniel Lepe 2014
     * @Version 1.5
     * @Date  17/09/2015
	 *
     * Ahora permite que se le pase la opción src_only para que devuelva solo string de URL
     *
     * @Author Daniel Lepe
     * @Version 1.3
     * @Date  20/08/2015
     */
    public function script ( $script, $options=array() ) {

        if ( !preg_match ( '/\.js$/', $script ) )
            $script .= '.js';

        $id = self::$appVersion;

        if ( self::$debug )
            $id = uniqid ( );

        if(!(self::$debug_disablejs and self::$debug))
            $url = $this -> url (  $this -> get_resources_prefix($options)  . 'js', true, false, true) . "/" . $script;

        if(is_array($options) and isset($options['src_only']) and $options['src_only']){

          if($this -> prettyURL){
            return sprintf("%s?v=%s", $url, $id);
          } else {
            return sprintf("%s", $url);
          }

        } else {
            return sprintf ( "<script type='text/javascript' src='%s?v=%s'></script>", $url, $id );
        }
    }

    /**
     * js
     *
     * Alias de script.
     *
     * @Author Daniel Lepe
     * @Version 1.0
     * @Date 20/08/2015
     */
    public function js($script, $options = array()){
        return $this -> script($script, $options);
    }

    /**
     * Script
     *
     *  Esta función devuelve las etiquetas correctas para cargar un JSON.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.2
     */
    public function json ( $json ) {
      // INIT
      $return = null;
      $url = null;

      // PROC
      if ( !preg_match ( '/\.json$/', $json ) )
          $json .= '.json';

      $id = self::$appVersion;

      if ( self::$debug )
          $id = uniqid ( );

      $url = $this -> url (  $this -> get_resources_prefix()  . 'json', true, false, true );

      // PRETTY URL CONFIG VALIDATION
      if($this -> prettyURL){
        $return = sprintf ( "%s/%s?v=%s", $url, $json, $id );
      } else {
        $return = sprintf ( "%s/%s", $url, $json );
      }

      return $return;
    }

    /**
     * Img
     *
     * Ahora permite que se configure via $loadResourcesFrom un webroot en otra parte del servidor.
     * Esta función devuelve las etiquetas correctas para cargar una imágen.
	   *
     * Ahora permite que se le pase la opción context especificar un archivo de contexto distinto
     *
     * @Author Daniel Lepe 2014
     * @Version 1.4
     * @Date  17/09/2015
	   *
     * Ahora permite que se le pase la opción src_only para que devuelva solo string de URL
     *
     * @Author Daniel Lepe 2014
     * @Version 1.3
     * @Date  20/08/2015
     */
    public function img ( $img, $options = array() ) {

        $id = self::$appVersion;

        if ( self::$debug )
            $id = uniqid ( );

        $attrs = "";

        foreach ( $options as $attr => $val ) {
            if ( $attrs != "" )
                $attrs .= " ";
            $attrs .= sprintf ( '%s="%s"', $attr, $val );
        }

        if ( is_string ( $img ) ) {
            $url = $this -> url (  $this -> get_resources_prefix($options) . 'images', true, false, true ) . '/' . $img;
        } elseif ( is_array ( $img ) ) {
            $url = $this -> url ( $img );
        }

        if(isset($options['src_only']) and $options['src_only']){
            return $url;
        } else {
          if($this -> prettyURL){
            $return = sprintf ( "<img src='%s?v=%s' %s />", $url, $id, $attrs );
          } else {
            $return = sprintf ( "<img src='%s' %s />", $url, $attrs );
          }
        }
        return $return;
    }

    /**
     * get_resources_prefix
     *
     * Devuelve el prefijo de ruta de los recursos de medios. Ahora permite que se
	 * le envie el parametro para forzamiento de contexto 'context'
     *
	 * @Author Daniel Lepe
	 * @Version 1.3
	 * @Date 17/09/2015
	 *
     * @Author Daniel Lepe
     * @Version 1.2
     */
    public function get_resources_prefix($options = array()){
		// CONTEXT PREVALIDATION
        if(isset(self::$loadResourcesFrom) and is_string(self::$loadResourcesFrom)){
            $prefix = self::$loadResourcesFrom . '/';
        } else {
            $prefix = '';
        }

		// CONTEXT OVERWRITING
		if(isset($options['context']) and is_string($options['context'])) {
			$prefix .= '_' . $options['context'] . '/';
		} else if ( !is_null ( self::$context ) ) {
            $prefix .= '_' . self::$context . '/';
		}

        return $prefix;
    }

    /**
     * CSS
     *
     *  Esta función devuelve las etiquetas correctas para cargar un CSS.
     *
     * Ahora permite que se le pase la opción context especificar un archivo de contexto distinto
     *
     * @Author Daniel Lepe 2014
     * @Version 1.3
     * @Date  17/09/2015
	 *
     * Ahora permite que se configure via $loadResourcesFrom un webroot en otra parte del servidor.
     * Ahora permite mandar sintaxis para impresión de nombre del servidor.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.3
     */
    public function css ( $css, $options=array() ) {

        if ( !preg_match ( '/\.css$/', $css ) )
            $css .= '.css';

        $id = self::$appVersion;

        if ( self::$debug )
            $id = uniqid ( );

        if(!(self::$debug_disablecss and self::$debug))
            $url = $this -> url (  $this -> get_resources_prefix($options)  . 'css', true, false, true ) . "/" . $css;

        if(is_array($options) and isset($options['src_only']) and $options['src_only']){
          if($this -> prettyURL){
            return sprintf("%s?v=%s", $url, $id);
          } else {
            return sprintf("%s", $url);
          }
        } else {
            return sprintf ( "<link rel='stylesheet' href='%s?v=%s'/>", $url, $id );
        }
    }

    /**
     * style
     *
     * Alias de css.
     *
     * @Author Daniel Lepe
     * @Version 1.0
     * @Date 17/09/2015
     */
    public function style($css, $options = array()){
        return $this -> css($css, $options);
    }

    /**
     * Currency
     *
     * Formatea un numero como moneda
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function currency ( $number, $fractional = true ) {
        if ( $fractional ) {
            $number = sprintf ( '%.2f', $number );
        }
        while ( true ) {
            $replaced = preg_replace ( '/(-?\d+)(\d\d\d)/', '$1,$2', $number );
            if ( $replaced != $number ) {
                $number = $replaced;
            } else {
                break;
            }
        }
        return "$" . $number;
    }

   /**
     * Cards
     *
     * Al igual que table, imprime toda la información de un array multinivel pero en
     * formato de cartas responsivas
     *
     * Daniel Lepe 2014
     */
    public function cards ( $data, $titleField = null, $imgField = null, $actionsField = null, $removeNulls = true ) {

        $return = '<div class="row">';

        if ( is_array ( $data ) and !empty ( $data [ 0 ] ) ) {

            foreach ( $data as $cell ) {

                $id = uniqid ( 'thumb' );

                $return .= sprintf ( "<div class='%s'>", $this -> cardsGrid [ 'cellClass' ] );

                if ( !empty ( $cell [ 'active' ] ) ) {
                    // Verifica si se le ha pasado un parámetro Active, si es asi, hace activa la
                    // carta activa.
                    $return .= sprintf ( "<div data-href='%s' class='%s' id='%s'>", $this -> url ( $cell [ 'active' ] ), $this -> cardsGrid [ 'thumbnail' ] . " " . $this -> cardsGrid [ 'activeClass' ], $id );
                    $script = "<script>$('#%s IMG, #%s DIV.caption ').on('click', function(){  window.document.location=$('#%s').data('href'); } );</script>";
                } else {
                    $return .= sprintf ( "<div class='%s' id='%s'>", $this -> cardsGrid [ 'thumbnail' ], $id );
                }

                if ( !is_null ( $imgField ) and isset ( $cell [ $imgField ] ) ) {
                    $return .= str_replace ( '<img ', sprintf ( '<img width="%s" ', $this -> cardsGrid [ 'thumnailWidth' ] ), $cell [ $imgField ] );
                    unset ( $cell [ $imgField ] );
                }

                $action = NULL;

                if ( !is_null ( $actionsField ) and isset ( $cell [ $actionsField ] ) ) {
                    $action = '<DIV class="actionBtn">' . $cell [ $actionsField ] . "</DIV>";
                    unset ( $cell [ $actionsField ] );
                }

                $return .= sprintf ( "<div class='%s'>", $this -> cardsGrid [ 'caption' ] );

                if ( !is_null ( $titleField ) and isset ( $cell [ $titleField ] ) ) {
                    $return .= sprintf ( '<h3>%s</h3>', $cell [ $titleField ] );
                    unset ( $cell [ $titleField ] );
                }

                foreach ( $cell as $label => $text ) {
                    // No se toma en cuenta la llave active ni los contenidos vacíos.
                    if ( !is_null ( $text ) and $label != 'active' ) {
                        $return .= "<p>";
                        $return .= sprintf ( "<strong>%s: </strong><br/>%s&nbsp;", strtoupper ( str_replace ( '_', ' ', $label ) ), $this -> truncar ( ($text), 50 ) );
                        $return .= "</p>";
                    }
                }

                $return .= "</div>";
                // Finaliza la DIV del Caption

                if ( !is_null ( $action ) )
                    $return .= $action;

                $return .= "</div>";
                // Finaliza la DIV de Thumbnail

                if ( isset ( $script ) )// Agrega el Script si existe a la salida.
                    $return .= str_replace ( "%s", $id, $script );
                $return .= "</div>";
                // Finaliza la DIV de la Celda
            }

        } else {
            return "<h2>Array vacío</h2>";
        }

        $return .= '</div>';

        return $return;
    }

    /**
     * dataTable
     *
     * Devuelve una tabla HTML desde un Array multinivel, que resulte ser paginable
     * via Datatable.js
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function dataTable ( $data, $options = array() ) {
        $table_class = 'table-bordered';
        $id = uniqid ( 'table' );
        if ( isset ( $options [ 'class' ] ) )
            $table_class = $options [ 'class' ];
        if ( isset ( $options [ 'id' ] ) )
            $id = $options [ 'id' ];

        if ( !is_array ( $data ) )
            die ( 'Array no recibido!' );
        $headers = array ( );
        if ( isset ( $options [ 'show' ] ) ) {
            foreach ( $options['show'] as $origin => $header ) {
                $headers [ ] = strtoupper ( str_replace ( '_', ' ', $header ) );
            }
        } elseif ( isset ( $data [ 0 ] ) ) {
            foreach ( $data[0] as $key => $header ) {
                if ( $key != 'class' )// TR class bootstrapp
                    $headers [ ] = strtoupper ( str_replace ( '_', ' ', $key ) );
            }
        }

        $return = "<table class='table $table_class datatable' id='$id'>";
        {
            $return .= "<thead>";
            $return .= "<tr class='replace-inputs'>";
            foreach ( $headers as $th ) {
                $th_class = str_replace ( 'á', 'a', $th );
                $th_class = str_replace ( 'é', 'e', $th_class );
                $th_class = str_replace ( 'í', 'i', $th_class );
                $th_class = str_replace ( 'ó', 'o', $th_class );
                $th_class = str_replace ( 'ú', 'u', $th_class );
                $th_class = str_replace ( 'ñ', 'n', $th_class );
                $th_class = str_replace ( 'Á', 'a', $th_class );
                $th_class = str_replace ( 'É', 'e', $th_class );
                $th_class = str_replace ( 'Í', 'i', $th_class );
                $th_class = str_replace ( 'Ó', 'o', $th_class );
                $th_class = str_replace ( 'Ú', 'u', $th_class );
                $th_class = str_replace ( 'Ñ', 'n', $th_class );
                $th_class = str_replace ( ' ', '_', strtolower ( $th_class ) );
                if ( $th != 'ID' )
                    $return .= "<th>$th</th>";
            }
            $return .= "</tr>";
            $return .= "<tr >";
            foreach ( $headers as $th ) {
                $th_class = str_replace ( 'á', 'a', $th );
                $th_class = str_replace ( 'é', 'e', $th_class );
                $th_class = str_replace ( 'í', 'i', $th_class );
                $th_class = str_replace ( 'ó', 'o', $th_class );
                $th_class = str_replace ( 'ú', 'u', $th_class );
                $th_class = str_replace ( 'ñ', 'n', $th_class );
                $th_class = str_replace ( 'Á', 'a', $th_class );
                $th_class = str_replace ( 'É', 'e', $th_class );
                $th_class = str_replace ( 'Í', 'i', $th_class );
                $th_class = str_replace ( 'Ó', 'o', $th_class );
                $th_class = str_replace ( 'Ú', 'u', $th_class );
                $th_class = str_replace ( 'Ñ', 'n', $th_class );
                $th_class = str_replace ( ' ', '_', strtolower ( $th_class ) );
                if ( $th != 'ID' )
                    $return .= "<th>$th</th>";
            }
            $return .= "</tr>";
            $return .= "</thead>";
        } {
            $return .= "<tbody>";
            foreach ( $data as $k => $tr ) {
                $class = '';

                // TR class bootstrapp
                if ( isset ( $tr [ 'class' ] ) ) {
                    $class = $tr [ 'class' ];
                    unset ( $tr [ 'class' ] );
                }

                $rowId = $id;

                if ( isset ( $tr [ 'id' ] ) ) {
                    $rowId .= $tr [ 'id' ];
                } else {
                    $rowId .= $k;
                }

                $return .= "<tr class='$class' id='$rowId'>";

                if ( isset ( $options [ 'show' ] ) ) {
                    foreach ( $options['show'] as $origin => $key ) {
                        if ( is_string ( $origin ) ) {
                            $return .= "<td>" . $tr [ $origin ] . "</td>";
                        } else {
                            $return .= "<td>" . $tr [ $key ] . "</td>";
                        }
                    }
                } else {
                    if ( !is_array ( $tr ) )
                        die ( 'El array es de un solo nivel... no se puede crear la tabla.' );
                    foreach ( $tr as $title => $td ) {
                        if ( empty ( $options [ 'show' ] ) || in_array ( $title, $options [ 'show' ] ) ) {
                            if ( $title != 'id' ) {
                                $return .= "<td>" . $td . "</td>";
                            }
                        }
                    }
                }
                $return .= "</tr>";
            }
            $return .= "</tbody>";
        }
        $return .= "</table>";
        $return .= $this -> dataTableScript ( $id );
        return $return;
    }

    /**
     * dataTableScript
     *
     * Devuelve una función con el Script para data table.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function dataTableScript ( $id ) {
        $lang = $this -> json ( 'lang/DatatablesSpanish.json' );
        $script = "<script type=\"text/javascript\">
                    $(function(){
                        var table = $(\"#$id\").dataTable({
                            \"language\": { \"url\": \"$lang\" },
                            \"sPaginationType\": \"bootstrap\",
                            \"aLengthMenu\": [[10, 25, 50, -1], [10, 25, 50, \"All\"]],
                            \"bStateSave\": true
                        });

                        table.columnFilter({
                            \"sPlaceHolder\" : \"head:after\"
                        });
                    });
                </script>";
        return $script;

    }

    /**
     * Table
     *
     * Devuelve una tabla HTML desde un Array multinivel.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function table ( $data, $options = array() ) {
        $table_class = 'table-condensed table-striped table-responsive';

        if ( isset ( $options [ 'class' ] ) )
            $table_class = $options [ 'class' ];

        if ( !is_array ( $data ) )
            die ( 'Array no recibido!' );
        $headers = array ( );
        if ( isset ( $options [ 'show' ] ) ) {
            foreach ( $options['show'] as $origin => $header ) {
                $headers [ ] = strtoupper ( str_replace ( '_', ' ', $header ) );
            }
        } elseif ( isset ( $data [ 0 ] ) ) {
            foreach ( $data[0] as $key => $header ) {
                if ( $key != 'class' )// TR class bootstrapp
                    $headers [ ] = strtoupper ( str_replace ( '_', ' ', $key ) );
            }
        }

        $return = "<table class='table $table_class'>";
        {
            $return .= "<thead>";
            $return .= "<tr>";
            foreach ( $headers as $th ) {
                $th_class = str_replace ( 'á', 'a', $th );
                $th_class = str_replace ( 'é', 'e', $th_class );
                $th_class = str_replace ( 'í', 'i', $th_class );
                $th_class = str_replace ( 'ó', 'o', $th_class );
                $th_class = str_replace ( 'ú', 'u', $th_class );
                $th_class = str_replace ( 'ñ', 'n', $th_class );
                $th_class = str_replace ( 'Á', 'a', $th_class );
                $th_class = str_replace ( 'É', 'e', $th_class );
                $th_class = str_replace ( 'Í', 'i', $th_class );
                $th_class = str_replace ( 'Ó', 'o', $th_class );
                $th_class = str_replace ( 'Ú', 'u', $th_class );
                $th_class = str_replace ( 'Ñ', 'n', $th_class );
                $th_class = str_replace ( ' ', '_', strtolower ( $th_class ) );
                $return .= "<th class='th_$th_class'>$th</th>";
            }
            $return .= "</tr>";
            $return .= "</thead>";
        } {
            $return .= "<tbody>";
            foreach ( $data as $tr ) {
                $class = '';

                // TR class bootstrapp
                if ( isset ( $tr [ 'class' ] ) ) {
                    $class = $tr [ 'class' ];
                    unset ( $tr [ 'class' ] );
                }

                $return .= "<tr class='$class'>";
                if ( isset ( $options [ 'show' ] ) ) {
                    foreach ( $options['show'] as $origin => $key ) {
                        if ( is_string ( $origin ) ) {
                            $return .= "<td>" . $tr [ $origin ] . "</td>";
                        } else {
                            $return .= "<td>" . $tr [ $key ] . "</td>";
                        }
                    }
                } else {
                    if ( !is_array ( $tr ) )
                        die ( 'El array es de un solo nivel... no se puede crear la tabla.' );
                    foreach ( $tr as $title => $td ) {
                        if ( empty ( $options [ 'show' ] ) || in_array ( $title, $options [ 'show' ] ) )
                            $return .= "<td>" . $td . "</td>";
                    }
                }
                $return .= "</tr>";
            }
            $return .= "</tbody>";
        }
        $return .= "</table>";
        return $return;
    }

    /**
     * Truncar
     *
     * Devuelve una cadena recortada.
     *
     * @Author Daniel Lepe 2014
     * @Version 1.0
     */
    public function truncar ( $string, $length, $replacer = '...' ) {
        if ( strlen ( $string ) > $length ) {
            $aPreg = array ( );
            $startBlock = preg_match ( "/^.{1,$length}/", $string, $aPreg );
            return trim ( $aPreg [ 0 ] ) . $replacer;
        }
        return $string;
    }

    /**
     * p
     *
     * Devuelve una cadena como parrafo HTML convirtiendo los saltos de linea en bloques de párrafo.
     *
     * @Author Daniel Lepe
     * @Date 12/11/2015
     * @Version 1.0
     */
    public function p ( $string ) {
       	$textBlocks = array();
		$matchs = array();
		preg_match_all('/(.*)[\n\r|\n|\r]/mi', $string, $matchs);
		if(!empty($matchs)){
			foreach($matchs[1] as $k => $m){
				$m = trim($m);
				if(!is_null($m))
					$textBlocks[] = "<p>" . ($m) . "</p>";
			}
		}

        return implode($textBlocks, PHP_EOL);
    }

    /**
     * pagination_set
     *
     * Envía los datos pertinentes de configuración al arreglo de configuración de paginado.
     *
     * @Author Daniel Lepe
     * @Version 1.0
     */
    public function pagination_set($cfg, $alternate = null){
        if(is_string($cfg) and is_string($alternate)){
            $this -> _set_config($cfg, $alternate);
        } elseif(is_array($cfg)){
            foreach($cfg as $k => $alternate){
                $this -> _set_config($k, $alternate);
            }
        }
    }

    /**
     * _set_config
     *
     * @Author Daniel Lepe
     * @Version 1.0
     */
    private function _set_config($cfg, $alternate){
        if(isset($this -> pagination_cfg[$cfg])){
            $this -> pagination_cfg[$cfg] = $alternate;
        } else {
            die('No se ha encontrado la clave solicitada en el diccionario de elementos para configuración de paginado. Los elementos correctos son: '
                . implode(", ", array_flip($this -> pagination_cfg)));
        }
    }

    /**
     * is_paginated
     *
     * Devuelve verdadero si se ele han pasado datos de paginado.
     *
     * @Author Daniel Lepe
     * @Version 1.0
     */
    public function is_paginated ( ) {
        return ( isset($this -> _pagination) and is_array( $this -> _pagination ) );
    }

    /**
     * Pagination
     *
     * @Author Daniel Lepe
     * @Version 1.0
     */
    public function paginate () {
        $pagination;

        // VALIDATION
        if(!isset($this -> _pagination))
            die('No está disponible la paginacion en esta versión. Por favor asegurate de tener el MVCLite 2.5 o superior.');

        // PROCCESS PAGINATION
        $this -> _proccess_pagination();

        // BUILDS PAGINATION
        $pagination = $this -> _build_pagination();

        // RETURN
        return $pagination;
    }

    /*
    Array
    (
        $this -> _pagination[limit]         => 30
        $this -> _pagination[skip]          => 0
        $this -> _pagination[items]         => 2457
        $this -> _pagination[pages]         => 82
        $this -> _pagination[current_page]  => 1
        // DYNAMICS
        $this -> _pagination[back]          => 1 // TRUE FOR AVAILABLE, ELSE INACTIVE
        $this -> _pagination[forth]         => 1 // TRUE FOR AVAILABLE, ELSE INACTIVE
        $this -> _pagination[bounding]      => [min => 1, max => 4]
    )
    */

	/**
     * pagination_get
     *
     *  Devuelve un valor en específico de la configuración devuelta por el paginado
	 * del request.
     *
     * @Author Daniel Lepe
     * @Version 1.0
	 * @Date 21/09/2015
     */
	public function pagination_get($string = null){
		// PREVALIDATION
		if(!$this -> is_paginated())
			die("No se puede usar HTMLHelper::pagination_get en un request sin paginado");

		// INIT
		$keys = array('limit','skip','items','pages','current_page','back','forth');

		// VALIDATION
		if(is_null($string) or !in_array($string, $keys))
			die("Se debe pasar uno de los siguientes parámetros pagination_get::()<br/>"
				. implode(', ', $keys));

		// PROCES
		return $this -> _pagination[$string];
	}

    private function _proccess_pagination(){
        // INIT
        $expose = $this -> pagination_cfg['expose'];
        $min = 0;
        $max = 0;

        // SETS ACTIVE/INACTIVE FOR BACK AND FORTH
        if($this -> _pagination['current_page'] <= 1){
            $this -> _pagination['back']    = false;
        } else {
            $this -> _pagination['back']    = $this -> _pagination['current_page'] - 1;
        }
        if($this -> _pagination['current_page'] >= $this -> _pagination['pages']){
            $this -> _pagination['forth']    = false;
        } else {
            $this -> _pagination['forth']    = $this -> _pagination['current_page'] + 1;
        }

        // SETS PAGINATION LEFT SINGLE LINKS
        $min += $this -> _pagination['current_page'] - $expose;
        if($min < 1){
            $max += abs($min);
            $min = 1;
        }

        $max += $this -> _pagination['current_page'] + ($expose);
        if($max > $this -> _pagination['pages']){
            $min -= abs($max - $this -> _pagination['pages']);
            $max = $this -> _pagination['pages'];
        }

        // REVALIDATES MIN IN CASE OF OVERFLOW
        if($min < 1){
            $min = 1;
        }

        $this -> _pagination['bounding'] = array('min' => $min, 'max' => $max);
    }

    private function _build_pagination () {
        // INIT
        $content = ''; $list = array(); $pages = array(); $assambled = array();

        // BUILDS FIRST, LAST, BACK AND FORTH
        $list = $this -> _build_first_to_last ();

        // BUILDS PAGES
        $pages = $this -> _build_pages ();

        // ASSAMBLE
        $assambled = $this -> _assamble_pagination($list, $pages);

        // WRAPPING
        $content = $this -> fillCSSNodeToHtml($this -> pagination_cfg['wrapper'], implode(' ', $assambled));

        // CONTENT
        return $content;
    }

    private function _assamble_pagination($list, $pages){
        // INIT
        $assambled = array();

        // ASSAMBLE
        if(isset($list['first']))
            $assambled[] = $list['first'];

        if(isset($list['back']))
            $assambled[] = $list['back'];

        foreach($pages as $p)
            $assambled[] = $p;

        if(isset($list['forth']))
            $assambled[] = $list['forth'];

        if(isset($list['last']))
            $assambled[] = $list['last'];

        // SETS ROWS
        foreach($assambled as $k => $p){

            $regex_hash = array();

            $row = $this -> pagination_cfg['row'];

            if(preg_match("/class\=\'(.+)\'/", $p, $regex_hash))
                $row .= "." . str_replace(' ', '.', $regex_hash[1]);

            // debug($row);
            $assambled[$k] = $this -> fillCSSNodeToHtml($row, $p);
        }

        // RETURN
        return $assambled;
    }

    private function _build_pages () {
        // INIT
        $pages = array();
        $i = $this -> _pagination['bounding']['min'];
        $j = $this -> _pagination['bounding']['max'];

        // LOOP BUILD
        for($i; $i <= $j; $i++){
            // BUILDS ALL
            if($i == $this -> _pagination['current_page']){
                $toBuild = $this -> pagination_cfg['active'];
            } else {
                $toBuild = $this -> pagination_cfg['page'];
            }
            $pages[] = $this -> fillCSSNodeToHtml($toBuild, $i, $i);
        }

        // RETURN
        return $pages;
    }

    private function _build_first_to_last () {

        // INIT
        $elementsList=array('first', 'last', 'back', 'forth');

        // LIKS RELATION
        $linksRelation = array(
            'first'     => 1,
            'last'      => $this -> _pagination['pages'],
            'back'      => $this -> _pagination['back'],
            'forth'     => $this -> _pagination['forth']
        );

        // BUILD
        foreach($elementsList as $label){
            // BUILDS ALL
            if(is_string($this -> pagination_cfg[$label]) and $linksRelation[$label])
               $list[$label] = $this -> fillCSSNodeToHtml($this -> pagination_cfg[$label], $this -> pagination_cfg[$label. '_text'], $linksRelation[$label]);
        }

        // RETURN
        return $list;
    }

    /**
     * fillCSSNodeToHtml
     *
     * CONVIERTE UN STRING CSS3 EN HTML DIRECTO Y SE LE COLOCA EN EL MEDIO EL VALOR CONTENIDO EN $CONTENT.
     *
     * @Author Daniel Lepe
     * @Version 1.0
     */
    public function fillCSSNodeToHtml($string, $content = null, $link = null){
        // INIT
        $string; $content; $id; $classes; $node; $hash_reciever = array();

        // CLEAN SUB-CHILD
        $string = preg_replace('/\(.*/im', null, $string);

        // GET ID
        preg_match('/\#(\w+)/im', $string, $hash_reciever );
        $id = (isset($hash_reciever[1]))? $hash_reciever[1] : null;

        // GET CLASSES
        preg_match_all('/\.(\w+)/im', $string, $hash_reciever );
        $classes = (isset($hash_reciever[1]))? implode(' ', $hash_reciever[1]) : null;

        // GET NODE
        preg_match('/^(\w+)/im', $string, $hash_reciever );
        $node = (isset($hash_reciever[1]))? strtoupper($hash_reciever[1]) : null;

        if(is_null($node) and !is_null($link)){
            $node = "A";
        }

        // SET PLACEHOLDER
        $string = $this -> place_holders['HTML_BODY'];

        // BUILD STRING
        $string = str_replace('id_ph', $id, $string);
        $string = str_replace('classes_ph', $classes, $string);
        $string = str_replace('node_ph', $node, $string);
        $string = str_replace('html_ph', $content, $string);

        // IF NODE IS A, ADDS HREF
        if(!is_null($link)){
            $string = preg_replace("/\<A[ ]/", sprintf("<A href='%s' ", $this -> _build_url_for_pagination('page', $link)), $string);
        }

        // RETURN STRING
        return $string;

    }

    /**
     * _build_url_for_pagination
     *
     * Construye la URL para el paginado, acepta via $type,
     * 'page' y en $attr el valor.
     *
     */
    private function _build_url_for_pagination($type, $attr){
        // INIT
        $request = null;

        // CONTEXT/LANGUAGE CONDITIONS
        if(is_string(APP::$context)) $request .= APP::$context  . "/";
        if(isset(APP::$language) and is_string(APP::$language['prefix'])) $request .= APP::$language['prefix'] . "/";

        // REQUEST ASSIGNMENT
        $request .= $this -> request;
        $regEX = preg_replace('/\_/', '\_', Routes::$paginationHASHING['page']);
        $regEX = preg_replace('/\#/', '\#', $regEX);

        // breakpoint($regEX);

        // PROCESS
        switch($type){
            case 'page':
                // LIMPIEZA
                if(preg_match('/\/' . $regEX .'\d+/i', $request)){
                    $request = preg_replace('/\/' . $regEX .'\d+/im', null, $request);
                }
                // NUEVA LIGA
                $request .= '/' . Routes::$paginationHASHING['page'] . $attr;
                break;
        }

        // RETURN
        return $this -> url($request);
    }

}
