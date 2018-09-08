/**
 * jQuery MVCLITE
 *
 * Herramienta para validado de formularios. 
 * Funciona con un arreglo general contenedor de los datos de validado.
 *
 * Require jQuery.
 *
 * @Author Daniel Lepe 2015
 * @Version 1.0
 */
var jQuery = jQuery || null;
(function ($) {
	'use strict';
	var settings = {
			debug: true,
			automaticClass: 'mvclite',
			validation:	{
				itemNotValidated: 'mvc-not-validated',
				carrier: 'div',
				validationHintClass: 'mvc-validation-hint'
			},
			events: {
				trap: 'submit',
				onSuccess: 'success',
				onValidationError: 'warning',
				onError: 'danger'
			},
			sendMethod: 'POST',
			submitBtn: {
				normal: null,
				submitting: 'Procesando',
				done: 'Realizado',
				errorToFix: 'Corrige los errores'
			}
		},
		validatedElement = 0,
		clearValidation = function (form) {
			var $form = $(form);
			validatedElement = 0;
			$.each($form.find('input, textarea'), function (i, o) {
				$(o).parent().removeClass(settings.validation.itemNotValidated);
				$(o).parent().find('.' + settings.validation.validationHintClass).remove();
			});
		},
		validationControl = function (form, data) {
			'use strict';
			var $form = $(form);
			$.each(data, function (i, validation) {
				var $input = $form.find(("[name='tag']").replace(/tag/, validation.field)),
					$parent = $input.parent(),
					html = ('<c/>').replace(/c/, settings.validation.carrier);
				$parent.addClass(settings.validation.itemNotValidated, 600);
				$parent.append(html);
				$parent.find(settings.validation.carrier).addClass(settings.validation.validationHintClass);
				$parent.find(settings.validation.carrier).text(validation.msg);
				$parent.find(settings.validation.carrier).on('click', function (e) {
					$(e.currentTarget).fadeOut(300);
				});
				if(validatedElement == 0){
					setTimeout(function(){
						'use strict';
							$input.focus();
					}, 500);
				}
				validatedElement ++;
			});
		},
		performOnSuccess = function (form) {
			var $form = $(form),
				urlToGo = $form.data('onsuccess');
			if (urlToGo !== undefined) {
				window.location.href = urlToGo;
			} 
		},
		submitControl = function (e, form) {
			// INITS {
			e.preventDefault();
            console.log('fired mvclite::submit');
			clearValidation(form);
            
			var $form = $(form),
				data = new FormData(),
				form_data = $form.serializeArray(),
				action = $form.attr('action');
			// }
			settings.submitBtn.normal = $form.find('button[type=submit]').text();
			$form.find('button[type=submit]').text(settings.submitBtn.submitting);
			$.each($form.find("input[type='file']"), function (it, file) {
				data.append(it, file.files[0]);
			});
			$.each(form_data, function (i, val) {
				data.append(val.name, val.value);
			});
			$.ajax({
				url: action,
				processData: false,
				cache: false,
				method: settings.sendMethod,
				contentType: false,
				data: data,
				success: function (response) {
					if (response.status) {
						$form.trigger(settings.events.onSuccess, [response]);
						// CLOSES MODAL IF ANY
						$('#' + $('.modal.fade.in').attr('id')).modal('hide');
						performOnSuccess(form);
					} else {
						if (typeof response.validation === 'object') {
							$form.trigger(settings.events.onValidationError, [response]);
							validationControl(form, response.validation);
							$form.find('button[type=submit]').text(settings.submitBtn.errorToFix);
						} else {
							$form.trigger(settings.events.onError, [response]);
							$form.find('button[type=submit]').text(settings.submitBtn.normal);
						}
					}
					if(typeof set_flash === 'function'){
						set_flash(response.msg, response.class);
					}
					if (settings.debug) {
						console.log(response);
					}
				}
			});
		};
	$.fn.mvclite = function (action) {
		this.each(function (i, element) {
			$(element).off(settings.events.trap);
			$(element).on(settings.events.trap, function (e) {
				submitControl(e, this);
			});
		});
	};
	$(function () {
		$('.' + settings.automaticClass).mvclite();
	});
}(jQuery));