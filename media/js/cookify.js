/**
 * COOKIFY
 * 
 * Permite manejar las cookies del explorador directamente. En ellas, extiende
 * la posibilidad de guardar objetos de cualquier tipo, borrar las variables en cookies
 * y consultarlas desde cualquier ubicación.
 *
 * @Author Daniel Lepe
 * @Version 1.1
 * @Date 17/09/2015
 */

var cookify = {

	createCookie : function(name, value, days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			var expires = "; expires=" + date.toGMTString();
		} else
			var expires = "";
		
		switch(typeof value){
			case 'object': case 'function': case 'array':
				value = JSON.stringify(value);
				break;
		}
		
		document.cookie = name + "=" + value + expires + "; path=/";
	},

	readCookie : function(name) {
		
		// INIT
		var nameEQ = name + "=",
			ca = document.cookie.split(';'),
			proccessReturn = function  (data){
				var parsed;
				try {
					parsed = JSON.parse(data);
					return parsed;
				} catch (err){
					return data;
				}
			};
		
		// LOOP SEARCH
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ')
			c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0)
				return proccessReturn(c.substring(nameEQ.length, c.length));
		}
		
		// NOTHING GET, RETURN NULL
		return null;
	},

	eraseCookie : function(name) {
		var createCookie = function(name, value, days) {
				if (days) {
					var date = new Date();
					date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
					var expires = "; expires=" + date.toGMTString();
				} else
					var expires = "";
				document.cookie = name + "=" + value + expires + "; path=/";
			};
		createCookie(name, "", -1);
	}
	
};