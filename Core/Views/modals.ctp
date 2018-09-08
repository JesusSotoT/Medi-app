    <!-- Cargando -->
    <div id="modal_loading" class="modal fade modal_mini" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="box">
                    <div class="load">
                      <div class="gear one">
                        <svg id="blue" viewbox="0 0 100 100" fill="#fff">
                          <path d="M97.6,55.7V44.3l-13.6-2.9c-0.8-3.3-2.1-6.4-3.9-9.3l7.6-11.7l-8-8L67.9,20c-2.9-1.7-6-3.1-9.3-3.9L55.7,2.4H44.3l-2.9,13.6      c-3.3,0.8-6.4,2.1-9.3,3.9l-11.7-7.6l-8,8L20,32.1c-1.7,2.9-3.1,6-3.9,9.3L2.4,44.3v11.4l13.6,2.9c0.8,3.3,2.1,6.4,3.9,9.3      l-7.6,11.7l8,8L32.1,80c2.9,1.7,6,3.1,9.3,3.9l2.9,13.6h11.4l2.9-13.6c3.3-0.8,6.4-2.1,9.3-3.9l11.7,7.6l8-8L80,67.9      c1.7-2.9,3.1-6,3.9-9.3L97.6,55.7z M50,65.6c-8.7,0-15.6-7-15.6-15.6s7-15.6,15.6-15.6s15.6,7,15.6,15.6S58.7,65.6,50,65.6z"></path>
                        </svg>
                      </div>
                      <div class="gear two">
                        <svg id="pink" viewbox="0 0 100 100" fill="#fff">
                          <path d="M97.6,55.7V44.3l-13.6-2.9c-0.8-3.3-2.1-6.4-3.9-9.3l7.6-11.7l-8-8L67.9,20c-2.9-1.7-6-3.1-9.3-3.9L55.7,2.4H44.3l-2.9,13.6      c-3.3,0.8-6.4,2.1-9.3,3.9l-11.7-7.6l-8,8L20,32.1c-1.7,2.9-3.1,6-3.9,9.3L2.4,44.3v11.4l13.6,2.9c0.8,3.3,2.1,6.4,3.9,9.3      l-7.6,11.7l8,8L32.1,80c2.9,1.7,6,3.1,9.3,3.9l2.9,13.6h11.4l2.9-13.6c3.3-0.8,6.4-2.1,9.3-3.9l11.7,7.6l8-8L80,67.9      c1.7-2.9,3.1-6,3.9-9.3L97.6,55.7z M50,65.6c-8.7,0-15.6-7-15.6-15.6s7-15.6,15.6-15.6s15.6,7,15.6,15.6S58.7,65.6,50,65.6z"></path>
                        </svg>
                      </div>
                      <div class="gear three">
                        <svg id="yellow" viewbox="0 0 100 100" fill="#fff">
                          <path d="M97.6,55.7V44.3l-13.6-2.9c-0.8-3.3-2.1-6.4-3.9-9.3l7.6-11.7l-8-8L67.9,20c-2.9-1.7-6-3.1-9.3-3.9L55.7,2.4H44.3l-2.9,13.6      c-3.3,0.8-6.4,2.1-9.3,3.9l-11.7-7.6l-8,8L20,32.1c-1.7,2.9-3.1,6-3.9,9.3L2.4,44.3v11.4l13.6,2.9c0.8,3.3,2.1,6.4,3.9,9.3      l-7.6,11.7l8,8L32.1,80c2.9,1.7,6,3.1,9.3,3.9l2.9,13.6h11.4l2.9-13.6c3.3-0.8,6.4-2.1,9.3-3.9l11.7,7.6l8-8L80,67.9      c1.7-2.9,3.1-6,3.9-9.3L97.6,55.7z M50,65.6c-8.7,0-15.6-7-15.6-15.6s7-15.6,15.6-15.6s15.6,7,15.6,15.6S58.7,65.6,50,65.6z"></path>
                        </svg>
                      </div>
                      <div class="lil-circle"></div>
                      
                    </div>
                    <div class="text">espere...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gracias -->
    <div id="modal_gracias" class="modal fade modal_mini" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <button type="button" class="close btn_closemodal" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h2>¡Gracias!</h2>
                <br>
                <!--<?php echo $this -> Html -> img('all/medicheck.png'); ?>-->
                <h4>Tu mensaje ha sido enviado.</h4>
                <h4>Nos contactaremos a la brevedad</h4>
                <br>
            </div>
        </div>
    </div>

    <!-- Error -->
    <div id="modal_error" class="modal fade modal_mini" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <button type="button" class="close btn_closemodal" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h2>¡Error!</h2>
                <br>
                <p>Ocurrio un error en su solicitud.<br>Por favor intente nuevamente.</p>
                <br>
            </div>
        </div>
    </div>

    <!-- Modal general -->
    <div id="modal_example" class="modal fade modal_gral" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content txt_center">
                <button type="button" class="close btn_closemodal" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <i class="fa fa-check-square-o" aria-hidden="true" style="font-size: 60px;"></i><br>
                <br>
                <h4>Producto agregado al cotizador</h4>
                <p></p>
                <br>
                <a href="#"></a>
            </div>
        </div>
    </div>