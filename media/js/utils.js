/**
 * utils.js
 *
 * Comprende algunas herramientas de uso común, como redireccionado.
 *
 * @Version 1.0
 * @Author Daniel Lepe
 */
var window = window || {},
	Utils = {
		serverRoot: null,
		goto: function (destiny) {
			'use strict';
			// INIT {
			var url = this.getRoot();
			// }
			// BUILD NEW URI {
			url += destiny;
			url = url.replace(/\/\//ig, '/');
			// }
			// REDIRECTS {
			window.document.location = url;
			window.document.href = url;
			// }
			return true;
		},
		getRoot: function () {
			'use strict';
			return this.serverRoot;
		},
		relocate: function (from, to) {
			'use strict';
			var urlBack = window.document.URL,
				// regex = null,
				destiny = '';
			// PROCESAMIENTO DEL ORIGEN
			from = from.replace(/^\/|\/$/img, '');
			// PROCESAMIENTO DEL DESTINO
			if (to !== null && to !== undefined) {
				destiny = to;
			}
			urlBack = urlBack.replace(from, destiny);
			window.document.location = urlBack;
			window.document.href = urlBack;
			return true;
		}
	};