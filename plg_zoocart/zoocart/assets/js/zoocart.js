;(function ($, ZX, window, document, undefined) {
 	"use strict";

 	// set notify defaults
 	$.when(ZX.ready()).done(function(){
 		ZX.notify.loadAssets().done(function(){
 			$.UIkit.notify.message.defaults.pos = 'top-center';
 		});
 	});

 	// hide tooltip when button pressed
 	$('html').on('click', '.uk-button', function(){
 		if($(this).data('tooltip')) $(this).data('tooltip').hide();
 	});

})(jQuery, jQuery.zx, window, document);


// zoocartCart
;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('zoocartCart', {

		init: function() {},

		/**
		 * update
		 */
		update: function(){
			var $this = this;

			// get cart data
			var data = $this.getFormData();

			// trigger pre cart event
			$this.trigger('preCartUpdate', data);

			// request
			return ZX.ajax.requestAndNotify({
				url: ZX.url.get('ajax:', {controller: 'cart', task: 'validateCart'}),
				data: data,
				queue: 'cart'
			}, {
				group: 'cart'
			}).done(function(response){
				$this._update(response);
				// trigger post cart event
				$this.trigger('postCartUpdate', response);
			});
		},

		/**
		 * _update
		 */
		_update: function(data) {
			var $this = this, ele, updated = false;

			// update items
			if(data.items !== undefined) $.each(data.items, function(id, value) {
				var item_row = $this.find('.zx-zoocart-cart-item[data-id="'+id+'"]'),
					quant = value[0], price = value[1], total = value[2], weight = value[3];

				// only update prices if have changed
				if($('.zx-zoocart-cart-item-quantity input', item_row).val(quant) !== quant ||
						$('.zx-zoocart-cart-item-price', item_row).data('value') !== price ||
							$('.zx-zoocart-cart-item-totalprice', item_row).data('value') !== total) {

					// the quantity could have been adjusted, update
					$('.zx-zoocart-cart-item-quantity input', item_row).val(quant);
					$('.zx-zoocart-cart-item-price', item_row).data('value', price).html(formatCurrency(price));
					$('.zx-zoocart-cart-item-totalprice', item_row).data('value', total).html(formatCurrency(total));
					$('.zx-zoocart-cart-item-weight', item_row).data('value', weight).html(weight);

					// let know there was an update
					updated = true;
				}
			});

			// update weight
			ele = $this.find('#zoocart-cart-weight');
			if (ele && (ele.data('value') !== data.weight)) {
				ele.data('value', data.weight).html(data.weight);
				updated = true;
			}

			// update fees
			ele = $this.find('.zx-zoocart-cart-totals-paymentfee [data-value]');
			if(ele.data('value') !== data.payment_fee) {
				ele.data('value', data.payment_fee)
					.html(formatCurrency(data.payment_fee)).zx('animate', 'pulse');
				updated = true;
			}

			// update shipping
			ele = $this.find('.zx-zoocart-cart-totals-shippingfee [data-value]');
			if(ele.data('value') !== data.shipping_fee) {
				ele.data('value', data.shipping_fee)
					.html(formatCurrency(data.shipping_fee)).zx('animate', 'pulse');
				updated = true;
			}

			// update discount
			ele = $this.find('.zx-zoocart-cart-totals-discounts [data-value]');
			if(ele.data('value') !== data.totals.discounts) {
				ele.data('value', data.totals.discounts)
					.html('-'+formatCurrency(data.totals.discounts)).zx('animate', 'pulse');
				updated = true;
			}

			// update totals
			ele = $this.find('.zx-zoocart-items-table-totals-subtotal [data-value]');
			if(ele.data('value') !== data.totals.subtotal) {
				ele.data('value', data.totals.subtotal)
					.html(formatCurrency(data.totals.subtotal)).zx('animate', 'pulse');
				updated = true;
			}

			ele = $this.find('.zx-zoocart-items-table-totals-taxes [data-value]');
			if(ele.data('value') !== data.totals.taxes) {
				ele.data('value', data.totals.taxes)
					.html(formatCurrency(data.totals.taxes)).zx('animate', 'pulse');
				updated = true;
			}

			ele = $this.find('.zx-zoocart-items-table-totals-total [data-value]');
			if(ele.data('value') !== data.totals.total) {
				ele.data('value', data.totals.total)
					.html(formatCurrency(data.totals.total)).zx('animate', 'pulse');
				updated = true;
			}

			// return state, true if there was some value update
			return updated;
		},

		/*
		 * getFormData
		 */
		getFormData: function() {
			return this.element.serializeForm();
		}
	});

	/**
	 * accounting formatMoney shortcut
	 */
	var formatCurrency = function(val) {
		return accounting.formatMoney(val);
	};


	// items - zoocartCart plugin
	// ========================================================================

	ZX.zoocartCart.plugin('items', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-cart-items');

			/*
			 * On Item quantities change
			 */
			$this.element.on('keyup', '[name*="quantity"]', function()
			{
				var value = $(this).val();

				// if it's not a number
				if (value !== '') { // allow empty value for manual editing
					if(!(!isNaN(parseFloat(value)) && isFinite(value)) || value <= 0){
						$(this).val(1);
					}
				}

			}).on('change', '[name*="quantity"]', function(){

				// can't be empty
				if ($(this).val() === '') $(this).val(1);

				var id = $(this).parents('.zx-zoocart-cart-item').data('id');
				$this.validateQuantity(id);
			});

			/*
			 * On Item remove
			 */
			$this.element.on('click', '.zx-zoocart-cart-item-remove', function(){
				var id = $(this).parents('.zx-zoocart-cart-item').data('id');
				$this.removeItem(id);
			});
		},

		/**
		 * validateQuantity
		 */
		validateQuantity: function(id) {
			var $this = this;

			// get cartitem row
			var row = $this.com.find('.zx-zoocart-cart-item[data-id='+id+']');

			var spin_holder = $('.zx-zoocart-cart-item-totalprice', row);

			// show spinner
			spin_holder.zx('spin', {'affix':'replace'});

			// removes queued requests, important to avoid delays when several and quick updates are made
			ZX.ajax.queue.clear('cart');

			// request
			$this.com.update().done(function(response){

				// set notice
				$.UIkit.notify(ZX.lang._('ZC_CART_UPDATED'), {status: 'success', group: 'cart'});

			}).always(function(){
				// remove spin instance, important
				spin_holder.zx('spin.off');
			});
		},

		/**
		 * removeItem from cart
		 */
		removeItem: function(id) {
			var $this = this;

			// get cartitem row
			var row = $this.com.find('.zx-zoocart-cart-item[data-id='+id+']');

			// show spinner
			$('.zx-zoocart-cart-item-remove', row).zx('spin');

			/*
			 * On pre Cart update, send the removing item id
			 */
			this.com.one('preCartUpdate', function(e, data){
				data.remove_item = id;
				data.module = $($this.com.options.moduleContainer).length;
			});

			// update cart. This way cart is recalculated after item removing
			$this.com.update().done(function(response){

				// remove row
				row.animate({"opacity":0}, function(){
					// remove
					row.remove();

					// if no more items, reload the page
					if($this.com.find('.zx-zoocart-cart-item').length === 0) {
						window.location.reload(true);
					}
				});

				// update module
				if(response.module) $($this.com.options.moduleContainer).html(response.module);
			});
		}
	});

	// coupon - zoocartCart plugin
	// ========================================================================

	ZX.zoocartCart.plugin('coupon', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-cart-coupon');
			this.remove_btn = $('.zx-zoocart-cart-coupon-remove', $this.element);

			/*
			 * On pre Cart update
			 */
			this.com.on('preCartUpdate', function(e, data){
				$.extend(true, data, $this.getFormData());
			});

			/*
			 * On Coupon code input
			 */
			$this.element.on('change', 'input', function(e) {
					
				// if input manually emptied, just trigger removal event
				if($(this).val() === '') {
					$this.remove_btn.trigger('click');
				} else {
					$this.validate();
				}
			});

			/*
			 * On Coupon code removal
			 */
			$this.remove_btn.on('click', function(e) {
				e.preventDefault();

				// reset input
				$('input', $this.element).val('');

				// reset report
				$('.zx-zoocart-cart-coupon-report', $this.element).removeClass('uk-text-success uk-text-danger').html('');

				// hide
				$(this).addClass('uk-hidden');

				// if the coupon was valid, revalidate the cart again without it
				if($this.element.data('status') === true) {

					// show spinner
					$this.element.zx('spin');

					$this.com.update().always(function(response){
						// stop spinner
						$this.element.zx('spin.off');
					});

					$this.element.data('status', '');
				}
			});
		},

		/**
		 * validate
		 */
		validate: function(selected) {
			var $this = this;

			// show spinner
			$this.element.zx('spin');

			// request
			$this.com.update().done(function(response){

				// extend default response object
				response = $.extend(true, {
					discounts: {
						coupon: {
							success: false
						}
					}
				}, response);

				// set status
				var status = response.discounts.coupon.success ? true : false;
				$this.element.data('status', status);

				// if success, hide the removal button
				if(status)
					$this.remove_btn.addClass('uk-hidden');
				// else, show it
				else
					$this.remove_btn.removeClass('uk-hidden');

				// set report text
				$('.zx-zoocart-cart-coupon-report', $this.element)
					.removeClass('uk-text-success uk-text-danger')
					.addClass('uk-text-'+(status ? 'success' : 'danger'))
					.html(response.discounts.coupon.report);

			}).always(function(response){

				// stop spinner
				$this.element.zx('spin.off');
				
			}).fail(function(response){

				// something went wrong, reset
				$this.remove_btn.trigger('click');
			});
		},

		/*
		 * getFormData
		 */
		getFormData: function() {
			return this.element.serializeForm();
		}
	});


	// variations - zoocartCart plugin
	// ========================================================================

	ZX.zoocartCart.plugin('variations', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-cart-items');

			/*
			 * On variation change
			 */
			$('.zx-zoocart-variations [data-uk-dropdown]', $this.element).on('click', 'li a', function(e){
				e.preventDefault();

				// save dom references
				var dropdown = $(e.delegateTarget),
					target = $(this),
					btn = $('button', dropdown);

				// show spinner
				btn.zx('spin');

				// hide the options list to avoid selecting another while still performing
				$('.uk-dropdown', dropdown).addClass('uk-hidden');

				// update form input
				$('input', dropdown).val(target.data('value'));

				// update cart
				$this.com.update().done(function(response){

					// update dropdown data
					dropdown.data('selected', target.data('value'));

					// update button attr display
					$('.zx-x-attr', btn).html(target.html());

					// hide selected option from list and unhide others
					target.closest('li').addClass('uk-hidden').siblings('li').removeClass('uk-hidden');
				})

				// something went wrong
				.fail(function(){
					// revert changes
					$('input', dropdown).val(dropdown.data('selected'));
				})

				// always do
				.always(function(){
					// hide spinner
					btn.zx('spin.off');
					// make sure the dropdown is closed
					dropdown.removeClass('uk-open');
					// unhide the options list
					$('.uk-dropdown', dropdown).removeClass('uk-hidden');
				});
			});
		}
	});

})(jQuery, jQuery.zx, window, document);


// zoocartCheckout
;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('zoocartCheckout', {

		init: function() {
			var $this = this;

			// save Cart instance reference
			this.cart = $('#zx-zoocart-cart').data('zoocartCart');

			/*
			 * On Place Order event
			 */
			$('.zx-zoocart-checkout-placeorder', $this.element).on('click', function(){
				var btn = $(this);
				

				// request js validation
				if($this.validateFieldsets()) {

					// spin
					btn.zx('spin');
					
					// if positive, request server validation
					return ZX.ajax.requestAndNotify({
						url: ZX.url.get('ajax:', {controller: 'cart', task: 'validateAndPlaceOrder'}),
						data: $this.getFormData(),
						queue: 'cart' // same as cart one
					}).done(function(response){

						// redirect to pay view
						window.location.href = response.pay_url;
					}).fail(function(){

						// spin off
						btn.zx('spin.off');
					});

				} else {
					// animate to indicate the validation failed
					$(this).zx('animate', 'shake');
				}
			});

			/*
			 * On step value update
			 */
			this.on('fieldsetValueUpdated', function(e, element){
				$(element).closest('.zx-zoocart-checkout-fieldset').find('.zx-zoocart-checkout-fieldset-title')
					.removeClass('uk-text-danger');
			});
		},

		/**
		 * getFormData
		 */
		getFormData: function() {
			var $this = this,
				data = {};

			$.each(ZX.extensions.zoocartCheckout.plugins, function(name, plugin){

				// skip no fieldset plugins
				if(!name.match(/^fieldset/g)) return true;

				$.extend(true, data, plugin.getValues());
			});

			return data;
		},

		/**
		 * validateFieldsets
		 */
		validateFieldsets: function() {
			var $this = this;

			// validate plugins
			var status = true;
			$.each(ZX.extensions.zoocartCheckout.plugins, function(name, plugin){

				// skip no fieldset plugins
				if(!name.match(/^fieldset/g)) return true;

				// skip plugins without validation
				if(ZX.utils.typeOf(plugin.validate) !== 'function') return true;

				// or without an element
				if(!plugin.element.length) return true;

				// validate
				var validation = plugin.validate();

				// make sure is array
				if(ZX.utils.typeOf(validation) !== 'array') validation = [validation];

				// process validations objects
				$.each(validation, function(i, val){

					// find title
					var title = val.element !== undefined ? $(val.element).closest('.zx-zoocart-checkout-fieldset').find('.zx-zoocart-checkout-fieldset-title') : false;

					// no validated
					if(val.status === false) {
						if(title) title.addClass('uk-text-danger').zx('animate', 'pulse');
						status = false;

					// validated!
					} else {
						if(title) title.removeClass('uk-text-danger');
					}
				});
			});

			return status;
		}
	});


	// fieldsetShipping - zoocartCheckout plugin
	// ========================================================================

	ZX.zoocartCheckout.plugin('fieldsetShipping', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-checkout-shipping');

			if(!this.com.options.enable_shipping){
				return;
			}

			/*
			 * On pre Cart update
			 */
			this.com.cart.on('preCartUpdate', function(e, data){
				$.extend(true, data, $this.getValues());
			});

			/*
			 * On post Cart update
			 */
			this.com.cart.on('postCartUpdate', function(e, data){
				var selected = $('[data-uk-button-radio] button, input', $this.element).filter('.uk-active').val(),
					newcontent = $(data.shipping_rates).html();

				// replace shippings with filtered ones
				$this.element.html(newcontent);

				// apply previous selection
				$this.element.find('[data-uk-button-radio] [value="'+selected+'"]').addClass('uk-active');
			});

			/*
			 * On Shipping method change
			 */
			$this.element.on('change', '[data-uk-button-radio]', function(){
				$this.com.cart.update();

				// trigger event
				$this.com.trigger('fieldsetValueUpdated', $this.element, $this.getValues());
			});
		},

		/*
		 * getValues
		 */
		getValues: function() {
			var $this = this,
				data = {},
				value = '',
				methods = $('[data-uk-button-radio] button, input', $this.element);

			// single shipping
			if(methods.length === 1 && $this.com.options.autoselect_shipping) {
				// get method
				data.shipping_method = methods.val();

				// get shipping
				data.shipping_plugin = methods.attr('data-shipping-plugin');

			// multiple payments
			} else {
				var checked = methods.filter('.uk-active');

				// if no selection abort
				if(!checked.length) return data;

				// get method
				data.shipping_method = (checked ? checked.val() : '');

				// get plugin
				data.shipping_plugin = (checked ? checked.attr('data-shipping-plugin') : '');
			}

			return data;
		},

		/**
		 * validate
		 */
		validate: function() {
			var $this = this;

			// return status object
			return {
				status: (!this.com.options.enable_shipping) ? true : ($.isEmptyObject($this.getValues()) ? false : true),
				element: $this.element
			};
		}
	});


	// fieldsetPayment - zoocartCheckout plugin
	// ========================================================================

	ZX.zoocartCheckout.plugin('fieldsetPayment', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-checkout-payment');

			/*
			 * On pre Cart update
			 */
			this.com.cart.on('preCartUpdate', function(e, data){
				$.extend(true, data, $this.getValues());
			});

			/*
			 * On Shipping method change
			 */
			$('[data-uk-button-radio]', $this.element).on('change', function(){
				$this.com.cart.update();

				// trigger event
				$this.com.trigger('fieldsetValueUpdated', $this.element, $this.getValues());
			});
		},

		/*
		 * getValues
		 */
		getValues: function() {
			var $this = this,
				data = {},
				value = '',
				methods = $('[data-uk-button-radio] button, input', $this.element);

			// single payment
			if(methods.length === 1) {
				// get method
				value = methods.val();

			// multiple payments
			} else {
				var checked = methods.filter('.uk-active');

				// if no selection abort
				if(!checked.length) return data;

				// get method
				value = (checked ? checked.val() : '');
			}
			
			data.payment_method = value;

			return data;
		},

		/**
		 * validate
		 */
		validate: function() {
			var $this = this;
			
			// return status object
			return {
				status: $.isEmptyObject($this.getValues()) ? false : true,
				element: $this.element
			};
		}
	});


	// fieldsetAddress - zoocartCheckout plugin
	// ========================================================================

	ZX.zoocartCheckout.plugin('fieldsetAddress', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-checkout-address');

			/**
			 * On pre Cart update
			 */
			this.com.cart.on('preCartUpdate', function(e, data){

				// extend the post data
				$.extend(true, data, $this.getValues());
			});

			// init addresses
			this.element.each(function(){
				$this.initAddress($(this));
			});
		},

		initAddress: function(element) {
			var $this = this,
				type = element.data('type'),
				form = $('.zx-zoocart-address-manager-form', element),
				manager = $('[data-zx-zoocart-address-manager]', element);

			/**
			 * Init Address Form, displayed if no saved addresses exist
			 */
			if(form.length) {
				form.zx('zoocartAddressForm', {type:type});
				$this.setFormEvents(form);
			}
			
			/**
			 * Same as billing feature
			 */
			if(type == 'shipping')
			{
				// set fields initial status
				var form_fields = $('.zx-zoocart-checkout-address-hidden', element);

				// click event
				$('.zx-zoocart-checkout-address-sameasbilling button', element).on('click', function(){
					var btn = $(this),
						input = btn.siblings('input[name="same_as_billing"]');

					// make sure the icon width is not altered
					$('i', btn).width($('i', btn).width());

					// toggle button state
					$('i', btn).toggleClass('uk-icon-square-o');

					// toggle form fields
					form_fields.slideToggle({duration:'fast', progress: function(){

						// force matching grid
						$("[data-uk-grid-match]").each(function() {
							var grid = $(this);
							if (grid.data("gridMatchHeight")) grid.data("gridMatchHeight").match();
						});
					}});
					
					// toggle input value
					input.val(input.val() === '1' ? 0 : 1);
				});
			}

			/**
			 * Set manager events
			 */
			if(manager.length) {

				// auto choose any edited address
				manager.on('addressSaved', function(e, address){
					$this.chooseAddress(address.id, element);
					// update cart
					$this.com.cart.update();
					// trigger event
					$this.com.trigger('fieldsetValueUpdated', element);
				});
			}

			/**
			 * Show/Choose other addresses
			 */
			var change_btn = $('.zx-zoocart-checkout-address-change', element);

			// save current button content
			change_btn.data('content', change_btn.html());

			// set fixed height
			change_btn.width(change_btn.width());

			// on click
			change_btn.on('click', function(e){
				e.preventDefault();

				// content/arrow toggle
				if($(this).hasClass('zx-x-open')) {
					$(this).html($(this).data('content'));
				} else {
					$(this).html('').append('<i class="uk-icon-chevron-up"></i>');
				}

				// hide/unhide partially the chosen address
				$('.zx-zoocart-checkout-address-chosen', element).animate({
					opacity: $(this).hasClass('zx-x-open') ? 1 : 0.3
				});

				// toggle addresses
				$('.zx-zoocart-checkout-address-others', element).slideToggle({duration: 'fast'});

				// abort any inited form editing
				$('[data-zx-zoocart-address-manager]', element).data('zoocartAddressManager').abortEditing();

				// toggle open class
				$(this).toggleClass('zx-x-open');
			});

			// Choose another address
			element.on('click', '.zx-zoocart-address-manager-row-choose', function(e){
				e.preventDefault();
				$this.chooseAddress($(this).closest('li').data('id'), element);

				// update cart
				$this.com.cart.update();

				// trigger event
				$this.com.trigger('fieldsetValueUpdated', element);
			});
		},

		/*
		 * chooseAddress
		 */
		chooseAddress: function(id, element) {
			var row = $('.zx-zoocart-address-manager-rows [data-id="'+id+'"]');

			// set new address content as chosen
			$('.zx-zoocart-checkout-address-chosen', element).html( $('.zx-zoocart-address-manager-row-preview', row).html() )
				.append($('<input type="hidden" value="'+id+'" name="address_id">'));

			// change btn status
			$('.zx-zoocart-address-manager-row-choose', row).html('<i class="uk-icon-dot-circle-o"></i>');
			// change previous default button status
			row.siblings().find('.zx-zoocart-address-manager-row-choose').html('<i class="uk-icon-circle-o"></i>');

			// restore choose button
			var choose_btn = $('.zx-zoocart-checkout-address-change', element);
			choose_btn.html( choose_btn.data('content') ).removeClass('zx-x-open');

			// restore chosen visibility
			$('.zx-zoocart-checkout-address-chosen', element).css({opacity: 1});

			// hide wrapper
			$('.zx-zoocart-checkout-address-others', element).slideUp({
				duration: 'fast'
			});
		},

		/*
		 * setFormEvents
		 */
		setFormEvents: function(form, element) {
			var $this = this;

			form.on('countryUpdated vatUpdated', function(response){
					
				// update cart
				$this.com.cart.update().done(function(response){

					// update country data
					$('[data-mapping="country"]', form).data('mapping-data', response.address.billing.country);

					// update vat data
					$('[data-mapping="vat"]', form).data('mapping-data', response.address.billing.vat);

					// validate VAT
					form.data('zoocartAddressForm').validateVat();
				});
			});
		},

		/*
		 * getValues
		 */
		getValues: function() {
			var data = {address:{}};

			this.element.each(function(i, element){
				var form = $('.zx-zoocart-address-manager-form', element),
					type = $(element).data('type');

				// get form data
				if(form.length){
					data.address[type] = form.data('zoocartAddressForm').getFormData();

				// or get chosen address id
				} else {
					data.address[type] = {
						id: $('.zx-zoocart-checkout-address-chosen input', element).val()
					};
				}
			});

			return data;
		},

		/**
		 * validate
		 */
		validate: function() {
			var $this = this,
				status = [];

			this.element.each(function(i, element){
				var form = $('.zx-zoocart-address-manager-form', element);

				if(form.length) status.push({
					status: form.data('zoocartAddressForm').validateForm()
				}); else {
					status.push({
						status: true
					});
				}
			});
			
			// return status object
			return status;
		}
	});


	// fieldsetTermsAgreement - zoocartCheckout plugin
	// ========================================================================

	ZX.zoocartCheckout.plugin('fieldsetTermsAgreement', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-checkout-terms');

			/*
			 * On pre Cart update
			 */
			this.com.cart.on('preCartUpdate', function(e, data){
				$.extend(true, data, $this.getValues());
			});

			// click event
			/*
			 * On agreement checkbox change
			 */
			$('.zx-zoocart-checkout-terms-agree .uk-button', $this.element).on('click', function(){
				var btn = $(this),
					input = btn.siblings('input');

				// make sure the icon width is not altered
				$('i', btn).width($('i', btn).width());

				// toggle button state
				$('i', btn).toggleClass('uk-icon-square-o uk-icon-check-square-o');
				
				// toggle input value
				input.val(input.val() === '1' ? 0 : 1);

				// trigger event
				$this.com.trigger('fieldsetValueUpdated', $this.element, $this.getValues());
			});
		},

		/*
		 * getValues
		 */
		getValues: function() {
			return this.element.serializeForm();
		},

		/**
		 * validate
		 */
		validate: function() {
			var $this = this;
			
			// return status object
			return {
				status: $this.getValues().terms === '0' ? false : true,
				element: $this.element
			};
		}
	});

})(jQuery, jQuery.zx, window, document);


// zoocartSlides
;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('zoocartSlides', {

		init: function() {
			this.initSlides();
		},

		/**
		 * initSlides
		 * reference: http://tutorialzine.com/2009/11/beautiful-apple-gallery-slideshow
		 */
		initSlides: function() {
			var $this = this,
				container = $('.zx-zoocart-slides-container', $this.element),
				nav = $('.zx-zoocart-slides-nav', $this.element);

			// set height
			$this.setSlidesHeight();

			// set nav actions
			nav.on('click', 'a', function(e) {
				e.preventDefault();
				var pos = $(this).parent().index();

				// slide animation
				container.stop().animate({marginLeft:-$this.slides_positions[pos]+'px'},450);
			});

			// set next btn action
			container.on('click', '.zx-zoocart-slides-next .uk-button', function(e) {
				e.preventDefault();
				var pos = $(this).closest('.zx-zoocart-slides-slide').index()+1;

				// slide animation
				container.stop().animate({marginLeft:-$this.slides_positions[pos]+'px'},450);

				// switch nav
				$('.uk-active', nav).removeClass('uk-active').next().addClass('uk-active');
			});

			var resizeTimer;
			$(window).resize(function() {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function(){
					$this.setSlidesHeight();
				}, 100);
			});
		},

		/**
		 * setSlidesHeight
		 */
		setSlidesHeight: function() {
			var $this = this,
				totWidth=0,
				positions = [],
				win_width = $(window).width(),
				container = $('.zx-zoocart-slides-container', $this.element),
				nav = $('.zx-zoocart-slides-nav', $this.element);

			// reset
			$this.element.css({
				width: 'auto',
				marginLeft: 0
			});


			var offset = $this.element.offset().left;

			// set wrapper widht to window 100% but with negative margin to start with offset 0
			$this.element.css({
				width: win_width,
				marginLeft: '-'+offset+'px'
			});
			
			// init slides
			$('.zx-zoocart-slides-slide', $this.element).each(function(i){

				// set each slide width to 100%
				$(this).width(win_width);

				// apply padding to content wrapper
				$(this).children().css({
					padding: '0 '+offset+'px'
				});

				/* Loop through all the slides and store their accumulative widths in totWidth */
				positions[i] = totWidth;
				totWidth += win_width;

				/* The positions array contains each slide's commulutative offset from the left part of the container */
			});

			/* Change the slides wrapper div's width to the exact width of all the slides combined */
			container.width(totWidth);

			// set slides initial position
			var pos = $('.uk-active', nav).index();
			container.css({marginLeft:-positions[pos]+'px'},450);

			// save the positions
			$this.slides_positions = positions;
		}
	});

	// auto init
	$(document).on("uk-domready", function(e) {
		$("[data-zx-zoocart-slides]").each(function() {
			var ele = $(this);

			if (!ele.data("zoocartSlides")) {
				var obj = ZX.zoocartSlides(ele, $.UIkit.Utils.options(ele.attr("data-zx-zoocart-slides")));
			}
		});
	});

})(jQuery, jQuery.zx, window, document);


// zoocartAuthenticate
;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('zoocartAuthenticate', {

		init: function() {
			var $this = this;

			/*
			 * On login request
			 */
			$this.element.on('submit', '#zx-zoocart-form-authenticate-login', function(e) {
				e.preventDefault();
				var form = $(this),
					submit_btn = $('[type="submit"]', form);

				// sping
				submit_btn.zx('spin');

				// request
				ZX.ajax.requestAndNotify({
					url: ZX.url.get('ajax:', {controller: 'cart', task: 'login'}),
					data: form.serializeForm(),
					queue: 'authenticate'
				},{
					group: 'authenticate'
				}).done(function(response){
					window.location.reload(true);
				}).always(function(){
					submit_btn.zx('spin.off');
				});
			});

			/*
			 * On register request
			 */
			$this.element.on('submit', '#zx-zoocart-form-authenticate-register', function(e) {
				e.preventDefault();
				var form = $(this),
					submit_btn = $('[type="submit"]', form);

				// spin
				submit_btn.zx('spin');

				// request
				ZX.ajax.requestAndNotify({
					url: ZX.url.get('ajax:', {controller: 'cart', task: 'register'}),
					data: form.serializeForm(),
					queue: 'authenticate'
				},{
					group: 'authenticate'
				}).done(function(response){
					window.location.reload(true);
				}).always(function(){
					submit_btn.zx('spin.off');
				});
			});
		}
	});

})(jQuery, jQuery.zx, window, document);


// zoocartAddresses
;(function ($, ZX, window, document, undefined) {
 	"use strict";

 	ZX.component('zoocartAddressManager', {

 		defaults: {
 			type: 'billing'
 		},
		
		init: function() {
			var $this = this;

			// set references
			$this.type = $this.options.type;

			// save the ermpty address row
			$this.row = $this.find('.zx-zoocart-address-manager-row.uk-hidden').remove();

			/*
			 * On edit/add event
			 */
			$this.element.on('click', '.zx-zoocart-address-manager-row-edit, .zx-zoocart-address-manager-row-add', function(e) {
				var btn = $(this), id;

				// if editing, find the address id
				if(btn.is('.zx-zoocart-address-manager-row-edit')) {
					id = btn.closest('li').data('id');
				}
				
				// show spin
				btn.zx('spin');

				// load form
				$this.loadForm(id).always(function(){
					btn.zx('spin.off');
				});
			});

			/*
			 * On set as default event
			 */
			$this.element.on('click', '.zx-zoocart-address-manager-row-setasdefault', function(e) {
				var btn = $(this),
					row = btn.closest('li');

				// show spin
				btn.zx('spin');

				// delete
				$this.setAsDefault(row.data('id')).done(function(){
					// change btn status
					btn.html('<i class="uk-icon-dot-circle-o"></i>').prop('disabled', true);
					btn.siblings().filter('.zx-zoocart-address-manager-row-delete').prop('disabled', true);
					// change previous default button status
					row.siblings().find('.zx-zoocart-address-manager-row-setasdefault:disabled').html('<i class="uk-icon-circle-o"></i>').prop('disabled', false);
					row.siblings().find('.zx-zoocart-address-manager-row-delete:disabled').prop('disabled', false);
				});
			});

			/*
			 * On Delete event
			 */
			$this.element.on('click', '.zx-zoocart-address-manager-row-delete', function(e) {
				e.preventDefault();

				var btn = $(this),
					row = btn.closest('li');
				
				ZX.notify.closeAll().confirm(ZX.lang._('ZC_ADDRESS_CONFIRM_DELETE')).done(function(){
					btn.zx('spin');

					// delete
					$this.deleteAddress(row.data('id')).done(function(){
						// remove
						row.animate({"opacity":0}, function(){
							row.remove();
						});
					}).always(function(){
						btn.zx('spin.off');
					});
				});
			});
		},

		/**
		 * swapElements
		 */
		swapElements: function(hide, show) {
			return $.when(hide.fadeOut('fast')).done(function(){
				show.fadeIn('fast');
			});
		},

		/**
		 * abortEditing
		 */
		abortEditing: function(hide, show) {
			var form = this.find('.zx-zoocart-address-manager-form'),
				list = $('.zx-zoocart-address-manager-rows, .zx-zoocart-address-manager-row-add, .zx-zoocart-address-manager-empty', this.element);
			this.swapElements(form, list).done(function(){
				form.remove();
			});
		},

		/**
		 * loadForm
		 */
		loadForm: function(id) {
			var $this = this;

			// request
			return ZX.ajax.requestAndNotify({
				url: ZX.url.get('ajax:', {controller: 'addresses', task: 'getAddressForm'}),
				data: {
					id: id,
					type: $this.type
				},
				queue: 'loadaddressform'
			}).done(function(response){

				// get rows
				var rows = $this.find('.zx-zoocart-address-manager-rows'),

				// get list of other elements, rows + the add button + the empty msg
				list = rows.add($this.find('.zx-zoocart-address-manager-row-add')).add($this.find('.zx-zoocart-address-manager-empty')),

				// prepate the form wrapper
				form = $('<div class="zx-zoocart-address-manager-form uk-form uk-container-center" />').hide()

				// set the loaded content
				.html(response.html)
				// init form component
				.zx('zoocartAddressForm', {type: $this.type, id:id})
				// apend it to the manager
				.appendTo($this.element);

				// assignt form buttons action
				$('.zx-zoocart-address-manager-form-save', form).on('click', function(){

					// init spin & save button reference
					var btn_save = $(this).zx('spin');

					/*
					 * On form save event
					 */
					form.data('zoocartAddressForm').saveForm().done(function(response){
						var row;

						// it editing
						if(id !== undefined) {
							// get row
							row = rows.find('[data-id="'+response.address.id+'"]');
						} else {
							// set new row
							row = $('<li data-id="'+response.address.id+'" />').html($this.row.html());
						}

						// update title
						row.find('.zx-zoocart-address-manager-row-title').html(response.address.title);

						// update preview
						row.find('.zx-zoocart-address-manager-row-preview').html(response.address.preview);

						// if new row
						if(id === undefined) {
							// append
							row.appendTo(rows);

							// if first address, mark as default
							if(rows.children().length === 1) {
								row.find('.zx-zoocart-address-manager-row-setasdefault, .zx-zoocart-address-manager-row-delete').prop('disabled', true);
							}

							// hide empty message
							$this.find('.zx-zoocart-address-manager-empty').addClass('uk-hidden');
						}

						// swap view
						$this.swapElements(form, list).done(function(){
							form.remove();
						});

						// trigger event
						$this.trigger('addressSaved', response.address);
						
					}).always(function(){
						btn_save.zx('spin.off');
					});
				});

				/*
				 * On form country/vat change event
				 */
				form.on('countryUpdated vatUpdated', function(response){

					// spin save button for action indication
					var btn_save = $('.zx-zoocart-address-manager-form-save', form).zx('spin');

					// request
					ZX.ajax.request({
						url: ZX.url.get('ajax:', {controller: 'addresses', task: 'validateAddress'}),
						data: $.extend(form.data('zoocartAddressForm').getFormData(), {
							type: $this.type
						}),
						queue: 'editingaddress'
					}).done(function(response){

						// update country data
						$('[data-mapping="country"]', form).data('mapping-data', response.address.country);

						// update vat data
						$('[data-mapping="vat"]', form).data('mapping-data', response.address.vat);

						// validate VAT
						form.data('zoocartAddressForm').validateVat();
					}).always(function(){
						btn_save.zx('spin.off');
					});
				});

				/*
				 * On form editing abort event
				 */
				$('.zx-zoocart-address-manager-form-abort', form).on('click', function(){
					$this.abortEditing();
				});

				// display form
				$this.swapElements(list, form);

				// trigger event
				$this.trigger('formLoaded', form);
			});
		},

		/*
		 * set Address as default
		 */
		setAsDefault: function(id) {
			return ZX.ajax.requestAndNotify({
				url: ZX.url.get('ajax:', {controller: 'addresses', task: 'setDefault'}),
				data: {
					id: id
				}
			});
		},

		/*
		 * deleteAddress
		 */
		deleteAddress: function(id) {
			return ZX.ajax.requestAndNotify({
				url: ZX.url.get('ajax:', {controller: 'addresses', task: 'remove'}),
				data: {
					id: id
				}
			});
		}
	});

	// auto init
	$(document).on("uk-domready", function(e) {
		$("[data-zx-zoocart-address-manager]").each(function() {
			var ele = $(this);

			if (!ele.data("zoocartAddressManager")) {
				var obj = ZX.zoocartAddressManager(ele, $.UIkit.Utils.options(ele.attr("data-zx-zoocart-address-manager")));
			}
		});
	});

})(jQuery, jQuery.zx, window, document);


// zoocartAddressForm
;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('zoocartAddressForm', {

		defaults: {
			type: 'billing',
			id: null
		},

		init: function(com) {
			var $this = this;

			// save references
			this.type = this.options.type;
			this.id = this.options.id;

			// Same as billing
			if($this.type == 'shipping') {
				$('#form_same_as_billing', $this.element).on('click', function(){
					var check_row = $(this).parent(),
						fields = $('.uk-form-row', $element).not(check_row).find('.uk-form-controls >');
					
					fields.prop('disabled', $(this).is(':checked') ? true : false);
				});
			}

			// There are form details we need to adjust after rendering
			// we can't trough PHP because is rendered within submissions
			$('[name*="elements"]', $this.element).each(function(i, v)
			{
				var field = $(v),
					row = field.closest('.uk-form-row'),
					label = $('label', row),
					mapping = row.data('mapping');

				// add required property
				if(row.hasClass('required')) field.prop('required', true);

				// zip
				if(mapping == 'zip') {
					field.attr('data-parsley-minlength', 3);
				}

				// country
				if(mapping == 'country') {

					// trigger event on country change
					field.on('change', function(){
						$this.trigger('countryUpdated');
					});
				}

				// vat
				if(($this.type == 'billing') && (mapping == 'vat')) {

					// get country field
					var country_row = $('[data-mapping="country"]', $this.element),
						country_field = $('select', country_row);

					// if country field set
					if(country_row.length){

						// surround field with lang wrapper
						var lang = field.wrap('<div class="zx-zoocart-address-manager-form-vat uk-width-1-1" />').parent().prepend('<span />').find('span');

						// add the country ISO if isEU
						if(country_row.data('mapping-data').isEU) {
							lang.html(country_field.val());
						} else {
							// disable field
							field.prop('disabled', true);
						}

						// trigger event on vat change
						field.on('change', function(){
							$this.trigger('vatUpdated');
						}).on('input change', function(){
							// if country code present, remove it
							var iso = country_field.val();
							field.val(field.val().replace(new RegExp('^('+iso.toLowerCase()+'|'+iso.toUpperCase()+')', 'g'), ''));
						});

						// initial validation
						$this.validateVat();
					}
				}

				// remove hardcoded size property and apply our width
				field.removeAttr('size').addClass('uk-width-1-1');

				// trigger field update event
				$(this).on('change', function(){
					$this.trigger('fieldUpdated', $(this));
				});
			});
		},

		/*
		 * getFormData
		 */
		getFormData: function() {
			var $this = this,
				data = {};

			// set address elements
			$.extend(true, data, $this.element.serializeForm());

			// set address id
			if($this.id) data.id = $this.id;

			return data;
		},

		/*
		 * Save Address form
		 */
		saveForm: function(data) {
			var $this = this;

			// request
			return ZX.ajax.requestAndNotify({
				url: ZX.url.get('ajax:', {controller: 'addresses', task: 'save'}),
				data: $.extend($this.getFormData(), {
					type: $this.type
				}),
				queue: 'editingaddress'
			});
		},

		/*
		 * validate form
		 */
		validateForm: function(data) {
			var $this = this,
				status = true;

			// validate inputs, textareas and selects
			this.element.find('input[required], textarea[required], select[required]').each(function(i){
				var field = $(this);

				if(field.val() === '') {
					status = false;

					// set error color
					field.addClass('uk-form-danger')

					// and revalidate on next update
					.one('input change', function(){
						$this.validateForm();
					});

					// animate
					field.closest('.uk-form-row').zx('animate', 'pulse');
					
				} else {
					field.removeClass('uk-form-danger');
				}
			});

			// validate vat
			$this.validateVat();

			return status;
		},

		/**
		 * validateVat
		 */
		validateVat: function() {
			var $this = this;

			var vat_row = $('[data-mapping="vat"]', $this.element),
				field = vat_row.find('input'),
				country_row = $('[data-mapping="country"]', $this.element),
				country = country_row.length ? $('select', country_row).val() : '';

			// proceede only if VAT field present
			if(!vat_row) return;

			// if no country selected reset VAT
			if(!country.length) {
				field.val('').prop('disabled', true).removeClass('uk-form-danger');
				$('span', vat_row).html('');
				$('i', vat_row).show();
				return;
			}

			// if isEU
			if(country_row.data('mapping-data').isEU) {
				
				// show VAT
				field.prop('disabled', false);

				// set country
				$('span', vat_row).html(country);

				// and hide icon
				$('i', vat_row).hide();

			} else {
				// disable VAT
				field.prop('disabled', true);
				$('span', vat_row).html('');
				$('i', vat_row).show();
			}

			// set VIES status
			if(vat_row.data('mapping-data') !== undefined && field.val() !== '') {
				if(vat_row.data('mapping-data').vies) {
					field.removeClass('uk-form-danger');
				} else if (vat_row.data('mapping-data').vies === false){
					field.addClass('uk-form-danger');
				}
			}
		}
	});

})(jQuery, jQuery.zx, window, document);


// zoocartOrder
;(function ($, ZX, window, document, undefined) {
 	"use strict";

 	ZX.component('zoocartOrder', {

		defaults: {
			order_id: ''
		},

		init: function() {
			var $this = this;

			// save references
			$this.id = $this.options.order_id;

			/*
			 * On Pay order button click
			 */
			$('.zx-zoocart-order-pay', $this.element).on('click', function(){
				$(this).zx('spin');
			});
		}
	});

	// fieldsetPayment - zoocartOrder plugin
	// ========================================================================

	ZX.zoocartOrder.plugin('fieldsetPayment', {

		init: function(com) {
			var $this = this;

			// save references
			this.com = com;
			this.element = com.find('.zx-zoocart-order-payment');

			/*
			 * On Shipping method change
			 */
			$('[data-uk-button-radio]', $this.element).on('change', function(){
				
				// request
				ZX.ajax.requestAndNotify({
					url: ZX.url.get('ajax:', {controller: 'orders', task: 'changePaymentMethod'}),
					data: $.extend($this.getValues(), {
						order_id: $this.com.id
					})
				}).done(function(){

					// notify
					$.UIkit.notify.closeAll();
					$.UIkit.notify(ZX.lang._('ZC_PAYMENT_METHOD_UPDATED'), {
						status: 'success',
						group: 'paymentupdate'
					});

				}).fail(function(){
					// if something went wrong reload
					window.location.reload(true);
				});
			});
		},

		/*
		 * getValues
		 */
		getValues: function() {
			var $this = this,
				data = {},
				value = '',
				methods = $('[data-uk-button-radio] button', $this.element);


			var checked = methods.filter('.uk-active');

			// if no selection abort
			if(!checked.length) return data;

			// get method
			value = (checked ? checked.val() : '');

			// set
			data.payment_method = value;

			return data;
		}
	});

})(jQuery, jQuery.zx, window, document);


/*
 * serializeForm
 * https://github.com/danheberden/serializeForm
 *
 * Copyright (c) 2012 Dan Heberden
 * Licensed under the MIT, GPL licenses.
 */
(function( $ ){
  $.fn.serializeForm = function() {

    // don't do anything if we didn't get any elements
    if ( this.length < 1) { 
      return false; 
    }

    var data = {};
    var lookup = data; //current reference of data
    var selector = ':input[type!="checkbox"][type!="radio"], input:checked';
    var parse = function() {

      // Ignore disabled elements
      if (this.disabled) {
        return;
      }

      // data[a][b] becomes [ data, a, b ]
      var named = this.name.replace(/\[([^\]]+)?\]/g, ',$1').split(',');
      var cap = named.length - 1;
      var $el = $( this );

      // Ensure that only elements with valid `name` properties will be serialized
      if ( named[ 0 ] ) {
        for ( var i = 0; i < cap; i++ ) {
          // move down the tree - create objects or array if necessary
          lookup = lookup[ named[i] ] = lookup[ named[i] ] ||
            ( (named[ i + 1 ] === "" || named[ i + 1 ] === '0') ? [] : {} );
        }

        // at the end, push or assign the value
        if ( lookup.length !==  undefined ) {
          lookup.push( $el.val() );
        }else {
          lookup[ named[ cap ] ]  = $el.val();
        }

        // assign the reference back to root
        lookup = data;
      }
    };

    // first, check for elements passed into this function
    this.filter( selector ).each( parse );

    // then parse possible child elements
    this.find( selector ).each( parse );

    // return data
    return data;
  };
}( jQuery ));