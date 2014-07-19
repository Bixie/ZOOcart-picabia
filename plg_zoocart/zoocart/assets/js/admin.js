/* ===================================================
 * ZOOcart Taxes
 * https://zoolanders.com
 * ===================================================
 * Copyright (C) JOOlanders SL 
 * http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * ========================================================== */
 ;(function ($, ZX, window, document, undefined) {
 	"use strict";

	ZX.component('zoocartDiscountEdit', {

		init: function() {
			var $this = this;

			var options = {
				errorClass : 'uk-form-danger',
				errorsWrapper : '<div class=\"uk-text-danger\"></div>',
				errorTemplate : '<div></div>'
			};

			// init
			$('#adminForm').parsley(options).subscribe('parsley:form:validated', function (formInstance) {
				// Check dates superposition:
				var start = $('#publishup').val().replace(/(\s[\d]{2}:[\d]{2}):[\d]{2}/,'');
				var finish = $('#publishdn').val().replace(/(\s[\d]{2}:[\d]{2}):[\d]{2}/,'');
				var valid = true;

				if(start && finish){
					var startDate = new Date(start);
					var endDate = new Date(finish);
					if(endDate<startDate){
						// Wrong date interval, generate error:
						$('#interval_valid' ).removeClass('uk-hidden');
						$('.zx-interval' ).addClass('uk-form-danger');
						valid = false;
					}else{
						$('#interval_valid' ).addClass('uk-hidden');
						$('.zx-interval' ).removeClass('uk-form-danger');
						valid = true;
						}
				}
				return valid;
			} );
		}

	});

 	ZX.component('zoocartTaxes', {

		init: function() {
			var $this = this;

			$this.initOrdering();
		},

		/*
		 * Order change request
		 */
		initOrdering: function() {
			var $this = this;

			// Catch current order of list items:
			var catchOrder = function(){
				var order = [];
				$('[data-row]', $this.element ).each(function(index, row){
					order.push($(row).data('row' ).id);
				});

				return order;
			}

			// on enable/disable request
			$('.uk-nestable', $this.element).on('nestable-change', function(e, item)
			{
				var data = JSON.stringify(catchOrder());

				// request
				ZX.ajax.requestAndNotify({
					url: ZX.url.get('ajax:', {controller: 'taxes', task: 'setResourceOrder'}),
					data: 'order='+data
				}, {
					group: 'resourceorder'
				});
			});
		}
	});

	ZX.component('zoocartSubsEdit', {

		// Init function
		init: function() {
			var $this = this;

			$('#toolbar-apply button').removeAttr('onclick')
				.on('click', function(e){
					e.preventDefault();
					if($this.validateSubscription()){
						$this.saveSubscription();
					}
			});

			$('#toolbar-save button, #toolbar-save-new button').removeAttr('onclick')
				.on('click', function(e){
					e.preventDefault();
					if($this.validateSubscription()){
						Joomla.submitbutton('save');
					}
				});
		},

		// Validate Subscription
		validateSubscription: function(){

			var $this = this;

			var options = {
				errorClass : 'uk-form-danger',
				errorsWrapper : '<div class=\"uk-text-danger\"></div>',
				errorTemplate : '<div></div>'
			};

			var allValid = true;

			var fieldsValid = $('#adminForm').parsley(options).subscribe('parsley:form:validated', function (formInstance) {
				// Check dates superposition:
				var start = $('#publishup').val().replace(/(\s[\d]{2}:[\d]{2}):[\d]{2}/,'');
				var finish = $('#publishdn').val().replace(/(\s[\d]{2}:[\d]{2}):[\d]{2}/,'');

				if(start && finish){
					var startDate = new Date(start);
					var endDate = new Date(finish);
					if(endDate<startDate){
						// Wrong date interval, generate error:
						$('#interval_valid' ).removeClass('uk-hidden');
						$('.zx-interval' ).addClass('uk-form-danger');
						allValid = false;
					}else{
						$('#interval_valid' ).addClass('uk-hidden');
						$('.zx-interval' ).removeClass('uk-form-danger');
						allValid = true;
					}
				}
			} ).validate();

			return allValid && fieldsValid;
		},

		// Saving subscription:
		saveSubscription: function(){

			var $this = this;
			$('#task', $this.element ).val('save');
			var $data = $('form', $this.element ).serialize();
			var $wrapper = $($this);

			// request
			ZX.ajax.requestAndNotify({
				url: ZX.url.get('ajax:', {controller: 'subscriptions', task: 'save'}),
				data: $data,
				type: 'POST'
			}, {
				group: 'statustoggle'
			}).always(function(response){
					// stop spinner
					$wrapper.zx('spin.off');
					// update icon state

				} );
		}

	});

	/*
	 * Describes form behavior (abstract)
	 */
	ZX.component('zoocartForm', {

		// Init function
		init: function() {
			var $this = this;

			$('#toolbar-apply button').removeAttr('onclick')
				.on('click', function(e){
					e.preventDefault();
					if($this.validate()){
						$this.save();
					}
				});

			$('#toolbar-save button, #toolbar-save-new button').removeAttr('onclick')
				.on('click', function(e){
					e.preventDefault();
					if($this.validate()){
						Joomla.submitbutton('save');
					}
				});
		},

		// Validate form
		validate: function(){
			var $this = this;

			var options = {
				errorClass : 'uk-form-danger',
				errorsWrapper : '<div class=\"uk-text-danger\"></div>',
				errorTemplate : '<div></div>'
			};

			return $('#adminForm').parsley(options ).validate();
		},

		// Save form data by AJAX
		save: function(){
			var $this = this;
			$('input[name="task"]', $this.element ).val('save');
			var controller = $('input[name="controller"]', $this.element ).val();
			var $data = $('form', $this.element ).serialize();
			var $wrapper = $($this);

			// request
			ZX.ajax.requestAndNotify({
				url: ZX.url.get('ajax:', {controller: controller, task: 'save'}),
				data: $data,
				type: 'POST'
			}).always(function(response){
					// stop spinner
					$wrapper.zx('spin.off');
				} );
		}

	});

	/*
	 * Describes togglable lists behavior
	 */
	ZX.component('zoocartTogglable', {

		// Init function
		init: function() {
			var $this = this;

			$this.initStatusToggle();
		},

		/*
		 * Publish toggle request
		 */
		initStatusToggle: function() {
			var $this = this;

			// on enable/disable request
			$('.zl-x-status', $this.element).on('click', 'a', function(e)
			{
				e.preventDefault();

				var $btn = $(this),
					$data = $btn.closest('[data-row]').data('row'),
					$wrapper = $(e.delegateTarget);

				// show spinner
				$wrapper.zx('spin');

				// detect controller:
				var ctrl = $('[name="controller"]' ).val();

				// request
				ZX.ajax.requestAndNotify({
					url: ZX.url.get('ajax:', {controller: ctrl, task: 'toggleResourceState'}),
					data: $data,
					type: 'GET'
				}, {
					group: 'statustoggle'
				}).always(function(response){
						// stop spinner
						$wrapper.zx('spin.off');
						// update icon state
						if(response.new_state !== undefined) if(response.new_state == 1) {
							// update tooltip
							$btn.data('tooltip').tip = ZX.lang._('ZL_TIP_STATUS_ENABLED');
							// and icon
							$btn.html('<i class="uk-icon-check uk-text-success"></i>');
						} else {
							// update tooltip
							$btn.data('tooltip').tip = ZX.lang._('ZL_TIP_STATUS_DISABLED');
							// and icon
							$btn.html('<i class="uk-icon-times uk-text-danger"></i>');
						}
					});
			});
		}
	});

})(jQuery, jQuery.zx, window, document);