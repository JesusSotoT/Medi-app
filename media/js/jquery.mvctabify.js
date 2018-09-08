/**
 * jQuery MVC Tabify
 *
 * Permite el uso de elementos tabificadores.
 * 
 * Reacciona de manera automática con la estructura:
 * 
 *
 * <DIV class="tabify">
 *	 <DIV class="tabs">
 *			<ul>
 *				<li class="tab" for="#ReferedTabContent"> CONTENT TITLE </li>
 *			</ul>
 *	 </DIV>
 *	
 *	 <DIV class="tabified">
 *			<div id="ReferedTabContent">
 *				CONTENT
 *			</div>
 *	 </DIV>
 * </DIV>
 *
 * VERSION 1.O
 *
 * @Author Daniel Lepe 2015
 * @Version 1.0
 */

var cookify = cookify || null;
var jQuery = jQuery || null;

(function ($) {
	'use strict';
	// INIT
	var settings = {
			autoObject: '.tabify', // APPLICABLE TO ALL OBJECTS IN REQUEST
			tabifyingElement: '.tab',
			tabbed: '.tabified',
			tabbedElement: '.tab'
		};
	
	// MAIN FUNCTION
	$.fn.mvctabify = function (action) {
		this.each(function (i, element) {
			var header = $(element);
			// CONTROLS
			$.each(header.find(settings.tabifyingElement), function(i, o) {
				$(o).on('click', function (e) {
					e.preventDefault();
					header.find(settings.tabifyingElement).removeClass('active');
					$(this).addClass('active');
					$(settings.tabbed).find(settings.tabbedElement).removeClass('active');
					$(settings.tabbed).find($(this).attr('href')).addClass('active');
				});
			});
			
			header.find(settings.tabifyingElement + ':first-child').trigger('click');
		});
		
	};

	$(function () {
		$(settings.autoObject).mvctabify();
	});
}(jQuery));