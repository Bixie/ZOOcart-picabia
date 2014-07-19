;(function ($, ZX, window, document, undefined) {
	"use strict";

	// set notify defaults
	$.when(ZX.ready()).done(function(){
		ZX.notify.loadAssets().done(function(){
			$.UIkit.notify.message.defaults.pos = 'top-center';
		});
	});

	// declare script
	ZX.component('zoocartAddToCart', {

		defaults: {
			hash: '',
			redirectUrl: '',
			moduleContainer: '.zoocart-smallcart',
			avoid_readd: true,
			def_variations: null,
			token: ''
		},

		init: function() {
			var $this = this;

			// iterate all addtocart elements
			$('.zoocart-addtocart[data-hash="'+$this.options.hash+'"] button').click(function(e)
			{
				// abort action if state disabled
				if ($(this).hasClass('zx-x-disabled')) return false;

				// init vars
				var $button = $(this),
					$element = $button.closest('.zoocart-addtocart'),
					$item_id = $element.data('item-id');

				// start spin
				$button.zx('spin');

				// get variations data
				var variations = $this.getVariationsData($item_id);
				var data = {
					'item_id': $item_id,
					'quantity': 1,
					'variations': variations ? variations : {},
					'module': $($this.options.moduleContainer).length,
					'avoid_readd': $this.options.avoid_readd
				};

				// request
				ZX.ajax.requestAndNotify({
					url: ZX.url.get('ajax:', {controller: 'cart', task: 'addtocart'}),
					data: data
				},{
					queue: 'addtocart'
				}).done(function(response) {

					// update label
					if (response.stock !== null && response.stock < 1) {
						$('.zx-x-text', $button).html(ZX.lang._('ZC_STOCK_EXHAUSTED_LABEL'));
					} else if($this.options.avoid_readd) {
						$('.zx-x-text', $button).html(ZX.lang._('ZC_COMPLETE_LABEL'));
					}

					// update in cart quantity
					$('.zx-x-incart-quant', $button).html(response.incart_quant+'x');

					// disable if necesary
					if($this.options.avoid_readd || (response.stock !== null && response.stock < 1)) {
						$button.addClass('zx-x-disabled');
					}

					// update module
					if(response.module) $($this.options.moduleContainer).html(response.module);

					// redirect
					if($this.options.redirectUrl.length) {
						if($this.options.redirectUrl == '_reload_') {
							window.location.reload(true);
						} else {
							window.location.href = $this.options.redirectUrl;
						}
					}
					
				}).always(function() {
					// stop spinning
					$button.zx('spin.off');
				});

				return false;
			});
		},
		/**
		 * Get the variations data from Variations element dom
		 */
		getVariationsData: function(item_id)
		{
			// find the related element
			var $variations = null;
			$('.zoocart-variations').each(function(){
				if ($(this).data('zoocart-variations').item_id == item_id) {
					$variations = $(this);
					// stop iteration
					return false;
				}
			});

			// if found, get it's values
			if ($variations !== null) {
				// get all attributes values
				var $attrs = {};
				$('[data-uk-dropdown]', $variations).each(function(){
					$attrs[$(this).data('attr')] = $(this).data('selected');
				});

				return $attrs;

			// else use default variation
			} else return this.options.def_variations;
		}
	});

})(jQuery, jQuery.zx, window, document);