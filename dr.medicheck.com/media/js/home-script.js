var ancho_win = $(window).width();
var alto_win = $(window).height();

// Menu visible arriba siempre, solo si pasa el bloque 1
function menufixed() {
    var currentTop = $(window).scrollTop();
    console.log(currentTop);

    if(currentTop > alto_win && ancho_win >= 768){
        $(".header").attr("style","position: fixed"); // Menu arriba
    }
    else{
        $(".header").attr("style"," "); // Quitar style
    }
}
$(window).scroll(function(){
    menufixed();
});