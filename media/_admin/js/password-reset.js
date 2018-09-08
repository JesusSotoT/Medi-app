//
//  password-back.js
//  mvclite
//
//  Created by Daniel Lepe on 2014-12-03.
//  Copyright 2014 Daniel Lepe. All rights reserved.
//
var PasswordBack = function($) {

	var formulario = $('#passwordReset');
	var notifyObj = $('#notify');

	var showForm = function() {
		if ($('body').hasClass('login-form-fall')) {
			var focus_set = false;
			setTimeout(function() {

				$('body').addClass('login-form-fall-init');

				setTimeout(function() {
					if (!focus_set) {
						$('body FORM').find('input:first').focus();
						focus_set = true;
					}
				}, 8000);
			}, 0);
		} else {
			$('body FORM').find('input:first').focus();
		}
	};

	/**
	 * procesaFormulario
	 *
	 * Para el formulario dado, procesa los datos introducidos.
	 */
	var procesaFormulario = function( ) {
		formulario.on('submit', function(e) {
			e.preventDefault();
			formulario.find('button[type=submit]').attr('disabled', true);
			$.ajax({
				url : formulario.attr('action'),
				data : formulario.serialize(),
				method: 'POST',
				success : function(data) {
					if (data.status) {
						window.document.location.pathname = formulario.attr('action').replace(/resetpassword.*/, 'login');
					} else {
						formulario.find('button[type=submit]').attr('disabled', false);
						notify(data.msg, '¡Ha ocurrido un error!');
					}
				}
			});
		});
	};
	
	/**
	 * Notifica el error
	 */
	
	var notify = function(error, titulo) {
		notifyObj.find('H3').text(titulo);
		notifyObj.find('p').text(error);
		notifyObj.slideDown('slow');
		setTimeout(function() {
			notifyObj.slideUp('slow');
		}, 3000);
	}; 
	
	procesaFormulario();
	showForm();
};

PasswordBack($);
