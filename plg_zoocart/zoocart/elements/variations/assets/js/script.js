;(function ($, ZX, window, document, undefined) {
	"use strict";

	ZX.component('zoocartVariationsElementEdit', {

		defaults: {
			elm_id: '',
			type: '',
			item_id: 0,
			variants: {}
		},

		init: function() {
			var $this = this;

			// set variants
			$this.variants = $this.options.variants;

			// initial validation
			$this.validateAttributes();

			// on each new instance
			$this.element.on('click', 'p.add a', function() {

				var $instances = $('ul.repeatable-list > .repeatable-element', $this.element);
				
				// for each instance
				$instances.each(function(index, instance){
					
					// reset the tabs index
					$('.nav-tabs a', $(instance)).each(function(){
						var href = $(this).attr('href').replace(/(variations-)\d+/g, '$1'+index);
						$(this).attr('href', href);
					});

					$('.tab-content .tab-pane', $(instance)).each(function(){
						var id = $(this).attr('id').replace(/(variations-)\d+/g, '$1'+index);
						$(this).attr('id', id);
					});

					// update the checkbox id
					var id = $('.zlux-x-checkbox input', $(instance)).attr('id')+index;
					$('.zlux-x-checkbox input', $(instance)).attr('id', id);
					$('.zlux-x-checkbox label', $(instance)).attr('for', id);
				});

				// assign combination to new instance
				$this.assignNextPossibleCombination($instances.last());
				
				// validate
				$this.validateAttributes();
			});

 			// when one instance checked as default, unselect the others
			$this.element.on('click', '.zlux-x-checkbox input', function() {
				$('.zlux-x-checkbox input', $this.element).not($(this)).removeAttr('checked');
			});

			// init
			$this.initDefaultOption();
			$this.initAttributesTab();
			$this.initPriceTab();
			$this.initElementsTab();
		},
		initDefaultOption: function() {
			var $this = this;

			// if only one instance, mark it as default
			if ($('ul.repeatable-list > .repeatable-element', $this.element).length == 1) {
				$('.zlux-x-checkbox input', $this.element).prop('checked', true);
			}

			// when instance deleted
			$this.element.on('instance.deleted', function(e, instance) {

 				// update variants matrix
 				var name = $this.getAttributesCombinedName(instance);
 				$this.variants[name] = false;

 				// if only one instance left, mark it as default
 				if ($('ul.repeatable-list > .repeatable-element', $this.element).length == 1) {
 					$('.zlux-x-checkbox input', $this.element).prop('checked', true);
 				}

 				// validate
 				$this.validateAttributes();
 			});
		},
		initAttributesTab: function() {
			var $this = this;

			 // on each attribute update
 			$this.element.on('change', '[id*="-attributes"] select', function(e) {
 				var $instance = $(this).closest('.repeatable-element');

 				// if combination exist
 				if ($this.doesCombinationExist($instance)) {
 					$this.validateAttributes();
 					$this.assignNextPossibleCombination($instance);
 				}

 				// validate
 				$this.validateAttributes();
 			});
		},
		initPriceTab: function() {
			var $this = this;

			// price element override workflow
			$('[id*="-price"] .repeatable-element', $this.element).each(function(e, el) {
				var $el = $(el);

				// if price has not been overrided yet
				if ($('.input-price', $el).val() === '') {
					$this.removeNameAttr($el);
				}
				
				// on price change
				$el.on('change', '.input-price-pretty', function(){
					
					// if overrided
					if ($(this).val() !== '') {
						// get name attr back to allow overriding
						$this.recoverNameAttr($el);
					} else {
						$this.removeNameAttr($el);
					}
				});
			});
		},
		initElementsTab: function() {
			var $this = this;

			// set element edit modal event
			$this.element.on('click', '[data-variations] button', function()
			{
				// init vars
				var $button = $(this);

				// close other dialogs
				$button.closest('.element').find('[data-variations] button').not($button).each(function(i, btn){
					btn = $(btn);
					if(btn.data('dialog')) btn.data('dialog').dialog('close');
				});
					
				// if dialog inited
				if($button.data('dialog')) {

					// open it
					$button.data('dialog').dialog('open');	

				// else, init it
				} else {

					// init vars
					var $dom = $button.closest('[data-variations]').find('div'); // element data dom reference
					$this.data = $(this).closest('[data-variations]').data('variations');

					// save dom reference in the button data
					$button.data('dom', $dom);

					// set dialog
					var $dialog = $.zlux.dialog({
						title: $this.data.name,
						width: '600',
						dialogClass: 'zl-bootstrap zx-zoocart-variations zlux-dialog-full creation-form',
						scrollbar: true
					}).bind("InitComplete", function() {

						// on mouseleave
						$dialog.widget.on('mouseleave', function(){
							// keep updated the data in order to be saved by ZOO
							$this.saveModalContent($button);
						}).on('change', 'input, textarea, select', function(){
							$this.saveModalContent($button);
						});

						// update the modal fields names when sorting main instances
						$('ul.repeatable-list', $this.element).on("sortstop", function(event, ui) {
							$this.updateModalIndexes();
						});

						// load content
						$this.loadElementContent($button);
					});

					// save dialog reference
					$button.data('dialog', $dialog);

					// init
					$dialog.toggle();
				}
			});
		},
		removeNameAttr: function(element) {
			$('[name]', element).each(function() {
				// save current name value
				$(this).data('name', $(this).attr('name'));
				// remove value
				$(this).attr('name', '');
			});
		},
		recoverNameAttr: function(element) {
			$('[name]', element).each(function() {
				$(this).attr('name', $(this).data('name'));
			});
		},
		loadElementContent: function($button) {
			var $this = this;

			// get instance index
			var count = $('ul.repeatable-list > li.repeatable-element', $this.element).length,
				index = $button.closest('.repeatable-element').data('initial-index');

			// request
			ZX.ajax.request({
				url: ZX.url.get('ajax:', {controller: 'zlframework', task: 'callelement'}),
				data: {
					method:'getElementEdit',
					elm_id: $this.options.elm_id,
					type: $this.options.type,
					item_id: $this.options.item_id,
					args:{
						identifier: $this.data.identifier,
						index: index !== undefined ? index : count-1 // if index not set, get next free index
					}
				}
			}).done(function(response){
				// set the data
				$button.data('dialog').content.html($('<div class="element" />').append(response.html));

				// set the sub list class
				$('ul.repeatable-list', $button.data('dialog').content).addClass('repeatable-list-level2');

				// update indexes, the main instances could have been reordered
				$this.updateModalIndexes();

				// save content to element dom in order to be saved by ZOO
				$this.saveModalContent($button);

				// show the content
				$button.data('dialog').initContent();

				// evaluate script
				eval(response.script);
			});
		},
		saveModalContent: function($button) {
			$button.data('dom').html( $('input, textarea, select', $button.data('dialog').content).clone() );
		},
		updateModalIndexes: function() {
			var $this = this;

			// iterate level1 instances
			$('ul.repeatable-list > li.repeatable-element', $this.element).each(function(instance_index){

				// for each modal button
				$('[data-variations] button', $(this)).each(function(){

					// update the modal content name attributes
					if($(this).data('dialog')) {
						$('[name^="elements"]', $(this).data('dialog').content).each(function(){
							var name = $(this).attr('name').replace(/(elements\[\w{8}-\w{4}-\w{4}-\w{4}-\w{12}\])(\[-?\d+\])/, '$1[' + instance_index + ']');
							$(this).attr('name', name);
						});
					}
				});
			});
		},
		validateAttributes: function() {
			var $this = this,
				$instances = $('ul.repeatable-list > .repeatable-element', $this.element);

			// reset variants
			$this.variants = $.extend({}, $this.options.variants);

			// for each instance
			$instances.not('.hidden').each(function(index, instance){

				// mark the used combination
				$this.variants[$this.getAttributesCombinedName($(instance))] = true;
			});

			// if no more combinations disable new instances button
			if (!$this.getNextPossibleCombination()) {
				$('p.add a', $this.element).hide();
			} else {
				$('p.add a', $this.element).show();
			}
		},
		getAttributesCombinedName: function($instance) {
			var $names = [];
			$.each($('[id*="-attributes"] [data-attr] select', $instance), function(){
				$names.push($(this).val());
			});

			return $names.join('.');
		},
		doesCombinationExist: function($instance) {
			return this.variants[this.getAttributesCombinedName($instance)] === false ? false : true;
		},
		getNextPossibleCombination: function() {
			var $this = this;

			var result = false;
			$.each($this.variants, function(i,v){
				if (v === false) {
					result = i;

					// stop iteration
					return false;
				}
			});

			if (result) {
				return result.split('.');
			}

			return false;
		},
		assignNextPossibleCombination: function($instance){
			var $this = this;

			// get next posible combination
			var $next_combination = $this.getNextPossibleCombination();

			if ($next_combination) {

				// set combination
				$.each($('[id*="-attributes"] [data-attr] select', $instance), function(i, v){
					$(this).val($next_combination[i]);
				});

			} else return false;
		}
	});

})(jQuery, jQuery.zx, window, document);