$(document).on('ready', function() {

    // == Formularios == //
    $(".varios-form").submit(function(evt){
        //console.log(JSON.stringify(evt));
        evt.preventDefault();

        $("#modal_loading").modal("show"); // Muestra mensaje de "enviando"

        var form = $(this);

        var new_data = new FormData();
        $('input[type="file"]').each(function(){
          new_data.append($(this).attr('name'), this.files[0]);
        });
        
        var dataform = form.serializeArray();
        
        $(dataform).each(function(){
          new_data.append(this.name, this.value);
        });

        $.ajax({
            type: form.attr('method'),
            data: new_data,
            url: form.attr('action'),
            cache: false,
            contentType: false,
            processData: false,  
            success: function(data){
                //flash(data.msg,data.class);
                if(typeof data.url != 'undefined'){
                    //setTimeout(window.location.reload(),90000);
                }
              
               $("#modal_loading").modal("toggle"); // Oculta ventana "enviando"
               $("#modal_gracias").modal("show"); // Muestra ventana "enviado"
               document.getElementById("form_all").reset(); // Borrar formulario

            },error: function(data){
                //flash(data.msg,data.class);
                //console.log(JSON.stringify(data));
                $("#modal_loading").modal("toggle"); // Oculta ventana "enviando"
                $("#modal_error").modal("show"); // Mostrar ventana "error"
            }

        });

    });

    
	// -- Anclas Suaves -->
		// Remove the class from the image
	    $('.preload-img').on('load', function(){
	        $(this).removeClass('preload-img');
	    });
	    $('.page-scroll a').bind('click', function(event) {
	        var $anchor = $(this);
	        $('html, body').stop().animate({
	        scrollTop: $($anchor.attr('href')).offset().top
	    }, 1500, 'easeInOutExpo');
	        event.preventDefault();
	        });
    // ------------- //  


    // -- Owl Carousel -- 
       // Pag Home
        $(".owl-bannerhome").owlCarousel({
            items: 1,
            autoplay: true,
            autoplayTimeout: 5000,
            smartSpeed: 900,
            /*autoplayHoverPause: true,*/
            nav: false,
            navText: [" "," "],
            dots: false,
            loop: false, //Nota: si solo hay 1 elemento, cambiar esto a false
            mouseDrag: true
        });  


    // --- OTROS ---
        // Btn muestra u oculta menu
        $( ".btn_movil" ).click(function() {
            var btn_movilval = $(this).attr("value");

            if(btn_movilval == "off"){
                $(this).attr("value","on");
                $(".row_menu").addClass("row_menu_movil");
                $(".btn_myacc").attr("style","z-index: 3;");
            }
            else{
                $(this).attr("value","off");
                $(".row_menu").removeClass("row_menu_movil");
                $(".btn_myacc").attr("style","");
            }
          
        }); 


        // PERMITIR SCROLL tras uso de varios modals de bootstrap, NO BORRAR
        $(document).on('hidden.bs.modal', function (event) {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        });
        // =================================== //

});