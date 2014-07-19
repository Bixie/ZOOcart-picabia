;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('EditElementMeasuresPro', {

		defaults: {
			unit: ''
		},

		init: function() {
			var $this = this;

			$this.element.on('click', 'p.add a', function () {
				$this.apply();
			});

			$this.apply();
		},

		apply: function() {
			var $this = this;

			$this.element.find('input.zx-x-raw').each(function() {

				var hidden = $(this), // hidden input
					pretty = hidden.next('input.zx-x-pretty'); // formated input

				// display current value
				if(hidden.val()) pretty.val($this.format(hidden.val()));

				// apply workflow
				if ( !$(this).data("initialized-measurespro") ){

					pretty.on('change', function() {

						if (pretty.val() === '') {
							hidden.val('');
						} else {

							// save unformated value
							hidden.val($this.clean(pretty.val()));
						}

					}).on('focusin', function() {

						// remove the symbol
						pretty.val(pretty.val().replace($this.options.unit, ''));

					}).on('focusout', function() {

						if (pretty.val() !== '') {

							// clean value
							var value = $this.clean(pretty.val());

							// format
							pretty.val($this.format(value));
						}
					});

				} $(this).data("initialized-measurespro", !0);
			});
		},

		/*
		 * Clean
		 */
		clean: function(value) {
			var $this = this;

			// remove any value that is not a number
			value = value.replace(new RegExp('[^0-9.,]', 'g'), '');

			// convert all non digit values to dot
			value = value.replace(/[^0-9]/g, '.');

			// if multiple dot present
			var match = value.match(/\./g);
			if(match !== null && match.length > 1) {
				value = value.split('.');
				var dec = value.pop();

				value = value.join('') + '.' + dec;
			}

			return accounting.toFixed(parseFloat(value), 2);
		},

		/*
		 * Format
		 */
		format: function(value) {
			var $this = this;

			return accounting.formatNumber(value, 2, '') + ' ' + $this.options.unit;
		},

		/*
		 * Unformat
		 */
		unformat: function(value) {
			var $this = this;
			return accounting.unformat(value, 2);
		}
	});

})(jQuery, jQuery.zx, window, document);