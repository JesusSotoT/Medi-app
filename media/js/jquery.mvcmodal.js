/**
 * jQuery MVC Modal
 *
 * Permite el uso de un MODAL administrado por jQuery. 
 * A diferencia de otro modales, este no requiere codigo inherte en el DOM del sitio, 
 * lo que hace mas rápida la carga. Además, está adaptado para que mande el TOKEN que se
 * encuentra en la cookie vm-data. 
 * 
 * Reacciona de manera automática con todas las ligas de la clase auto-modal, además.
 * 
 * VERSION 1.O
 *
 * @Author Daniel Lepe 2015
 * @Version 1.0
 */

var cookify = cookify || null;
var jQuery = jQuery || null;
var alert = alert || null;
(function (cookify, $) {
	'use strict';
	// INIT {
	var CookieName = 'vm-data',
		TOKEN = cookify.readCookie("token") || null,
		modalBody = "<section class='modal-mvc-wrapper'><div class='wrapper'><div class='close' /><div class='wrapp-container' id='modal-mvc'></div></div><div class='overlay'></div></section>",
		settings = {
			closeModalOnOverClick: false,
			bodyClass: 'mvcmodal-with',
			trigger: 'click',
			eventToCloseModal : 'MVCMODAL:close',
			automatedclass: 'auto-modal',
			texts: {
				nothingToDo: 'Nada que hacer, falta el elemento href.'
			}
		};
	// }
	// MAIN FUNCTION {
	$.fn.mvcmodal = function (element) {
		// INIT {
		var $element = $(element),
			action	= $element.attr('href') || null;
		// }
		// REVIEW {
		if (action === null) {
			alert(settings.texts.nothingToDo);
			return false;
		}
		// }
		// OPEN MODAL {
		$('BODY').prepend($(modalBody));
		$('HTML').addClass(settings.bodyClass);
		$('BODY').find('.close').on('click', function (e) {
			$('BODY').trigger(settings.eventToCloseModal); 
		});
		if(settings.closeModalOnOverClick) { 
			$('BODY').find('.overlay').on('click', function (e) {
					$('BODY').trigger(settings.eventToCloseModal); 
			});
		}
		// }
		// AJAX REQUEST {
		$.ajax({
			url: action + '/' + TOKEN,
			method: 'GET',
			success: function (data) {
				$('#modal-mvc').html(data);
			}
		});
		// }
		// CLOSE MODAL { 
		$('BODY').on(settings.eventToCloseModal, function () {
			$('BODY').find('.modal-mvc-wrapper').remove();
			$('BODY').off(settings.eventToCloseModal);
			$('HTML').removeClass(settings.bodyClass);
		});
		// }
	};
	// }
	$(function () {
		$('.' + settings.automatedclass).on(settings.trigger, function (e) {
			e.preventDefault();
			$(this).mvcmodal(e.currentTarget);
		});
	});
}(cookify, jQuery));