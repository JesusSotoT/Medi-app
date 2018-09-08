Modernizr.load([
	{
		test : Modernizr.textshadow,
		nope : ['js/polyfills/text-shadow/jquery.textshadow.js', 'js/polyfills/text-shadow/jquery.textshadow.css' ], //, 'presentational.css']
		callback: function (url, result, key) {

			if ( ! result) {
				$('[data-polyfill="text-shadow"]').textshadow();
		    }
		}
	},
	{
		test : Modernizr.input.placeholder,
		nope : ['js/polyfills/placeholder/jquery.placeholder.min.js'],
		callback: function (url, result, key) {

			if (! result) {
				$('input, textarea').placeholder();
			}
		}
	}
]);