;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('zoocartVariationsElement', {

		defaults: {
			elm_id: '',
			type: '',
			item_id: 0,
			layout: ''
		},

		init: function() {
			var $this = this;

			// on variation change
			$('[data-uk-dropdown]', $this.element).on('click', 'li a', function(e){
				e.preventDefault();

				// save dom references
				var $dropdown = $(e.delegateTarget),
					$target = $(this),
					$variations = $dropdown.closest('.zx-zoocart-variations'), // variations wrapper
					$button = $('button', $dropdown),
					$current_value = $dropdown.data('selected');

				// show spinner
				$button.zx('spin');

				// hide the options list to avoid selecting another while still performing
				$('.uk-dropdown', $dropdown).addClass('uk-hidden');

				// update dropdown data
				$dropdown.data('selected', $target.data('value'));

				// get all attributes values
				var $attrs = {};
				$('[data-uk-dropdown]', $variations).each(function(){
					$attrs[$(this).data('attr')] = $(this).data('selected');
				});

				// get variations
				$this.loadVariationsContent($attrs).done(function(response){

					// update button attr display
					$('.zx-x-attr', $button).html($target.html());

					// hide selected option from list and unhide others
					$target.closest('li').addClass('uk-hidden').siblings('li').removeClass('uk-hidden');

					// apply variations
					if(response.variations) $.each(response.variations, function(hash, value){
						$('[data-zoocart-hash="'+hash+'"]').html(value);
					});
				})

				.fail(function(){
					// revert changes
					$dropdown.data('selected', $current_value);
				})

				.always(function(){
					// hide spinner
					$button.zx('spin.off');
					// make sure the dropdown is closed
					$dropdown.removeClass('uk-open');
					// unhide the options list
					$('.uk-dropdown', $dropdown).removeClass('uk-hidden');
				});
			});
		},

		loadVariationsContent: function($attrs) {
			var $this = this;

			// request
			return ZX.ajax.request({
				url: ZX.url.get('ajax:', {controller: 'zlframework', task: 'callelement'}),
				data: {
					method:'getVariationsContent',
					elm_id: $this.options.elm_id,
					type: $this.options.type,
					item_id: $this.options.item_id,
					args:{
						layout: $this.options.layout,
						attrs: $attrs
					}
				}
			});
		}
	});

	// init code
	var triggerevent = $.UIkit.support.touch ? "click" : "mouseenter";

	$(document).on(triggerevent+'.variations.zoocart', '[data-zx-zoocart-variations]', function(e) {
		var ele = $(this);

		if (!ele.data('zoocartVariationsPlugin')) {
			var variations = ZX.zoocartVariationsElement(ele, ZX.utils.options(ele.data('zx-zoocart-variations')));
			e.preventDefault();
		}
	});

})(jQuery, jQuery.zx, window, document);