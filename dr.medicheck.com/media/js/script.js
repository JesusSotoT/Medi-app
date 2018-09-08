
$(document).on('ready', function() {


    $('#carrito').stacktable();
    $('#historial').stacktable();
    

    (function($) {
        $.fn.spinner = function() {
            this.each(function() {
                var el = $(this);

                // add elements
                el.wrap('<span class="spinner"></span>');     
                el.before('<span class="sub">-</span>');
                el.after('<span class="add">+</span>');

                // substract
                el.parent().on('click', '.sub', function () {
                    if (el.val() > parseInt(el.attr('min')))
                        el.val( function(i, oldval) { return --oldval; });
                });

                // increment
                el.parent().on('click', '.add', function () {
                    if (el.val() < parseInt(el.attr('max')))
                        el.val( function(i, oldval) { return ++oldval; });
                });
            });
        };
    })(jQuery);

    $('input[type=number]').spinner();

    $('.addcar, .add').click(function(){
        Carrito();
    });

    $('.dropcar ,.sub').click(function(){
        Eliminado();
    });

toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "3000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}

function Favoritos(msg) {
    toastr.success(msg, '❤');
}

function Carrito() {
    toastr.success('Se ha agregado al carrito', '<i class="fa fa-shopping-bag" aria-hidden="true"></i>');
}


function Eliminado() {
    toastr.error('Se ha eliminado del carrito', '<i class="fa fa-shopping-bag" aria-hidden="true"></i>');
}
});

