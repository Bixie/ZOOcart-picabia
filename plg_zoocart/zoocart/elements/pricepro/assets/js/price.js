;(function ($, ZX, window, document, undefined) {
	"use strict";

	// declare script
	ZX.component('EditElementPrice', {

		defaults: {
			currency: {}
		},

		init: function() {
			var $this = this;

			$this.element.on('click', 'p.add a', function () {
				$this.apply();
			});
			
			$this.apply();
		},

		apply: function() {
			var op = this.options;
			var $this = this;

			$this.element.find('input').each(function() {
				
				var hidden = $(this), // hidden input
					pretty = hidden.next('.input-price-pretty'); // formated input

				// display current value
				if(hidden.val()) pretty.val($this.format(hidden.val()));

				// apply workflow
				if ( !$(this).data("initialized-price") ){
				
					pretty.on('change', function() {

						if (pretty.val() === '') {
							hidden.val('');
						} else {

							// save unformated value
							hidden.val($this.clean(pretty.val()));
						}

					}).on('focusin', function() {

						// remove the symbol
						pretty.val(pretty.val().replace($this.options.currency.symbol, ''));
					
					}).on('focusout', function() {

						if (pretty.val() !== '') {

							// clean value
							var value = $this.clean(pretty.val());

							// format
							pretty.val($this.format(value));
						}
					});
					
				} $(this).data("initialized-price", !0);
			});
		},

		/*
		 * Clean
		 */
		clean: function(value) {
			var $this = this;

			// basic check
			if (value === '') return value;

			// remove any value that is not a number, comma, dot or specific decimal separator
			value = value.replace(new RegExp('[^0-9.,'+$this.options.currency.decimal_sep+']', 'g'), '');

			// convert all non digit values to dot
			value = value.replace(/[^0-9]/g, '.');

			// if multiple dot present
			var match = value.match(/\./g);
			if(match !== null && match.length > 1) {
				value = value.split('.');
				var dec = value.pop();

				value = value.join('') + '.' + dec;
			}

			return parseFloat(value).toFixed(this.options.currency.num_decimals);
		},

		/*
		 * Format
		 */
		format: function(value) {
			var $this = this;

			// basic check
			if (!$this.options.currency || !$this.options.currency.format) return value;

			var format = $this.options.currency.format.split('/');
			return accounting.formatMoney(value, {
				symbol: $this.options.currency.symbol,
				precision: $this.options.currency.num_decimals,
				thousand: $this.options.currency.thousand_sep,
				decimal: $this.options.currency.decimal_sep,
				format: {
					pos: $.trim(format[0]),
					neg: format[1] ? $.trim(format[1]) : $.trim(format[0]),
					zero: $.trim(format[0])
				}
			});
		},

		/*
		 * Unformat
		 */
		unformat: function(value) {
			var $this = this;
			return accounting.unformat(value, $this.options.currency.decimal_sep);
		}
	});

})(jQuery, jQuery.zx, window, document);