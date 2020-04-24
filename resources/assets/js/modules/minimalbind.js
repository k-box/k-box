define("modules/minimalbind", ["jquery", "lodash"], function ($, _) {

	function _eventHandler(evt){

	}


	function _constructInvokingFunction(action, model){

		action = action.split("."); //may contain dot for separating property of the object
		var invokeOn = model;

		for (var i = 0; i < action.length; i++) {
			invokeOn = invokeOn[action[i]];
		};

		return invokeOn;
	}

	function _updateDisabledBindings(binds, view){

		_.forEach(binds, function(n){

			var $n = $(n), invokeOn = _constructInvokingFunction($n.attr('rv-disabled'), view.model);

			if(_.isFunction(invokeOn)){
				invokeOn = invokeOn.call(this, view.model);	
			}

			if(invokeOn){
				$n.addClass('disabled').attr('disabled', true);
			}
			else {
				$n.removeClass('disabled').removeAttr('disabled');
			}

		});

	}

	function _updateVisibleBindings(binds, view){

		_.forEach(binds, function(n){

			var $n = $(n), invokeOn = _constructInvokingFunction($n.attr('rv-visible'), view.model);

			if(_.isFunction(invokeOn)){
				invokeOn = invokeOn.call(this, view.model);	
			}

			if(invokeOn){
				$n.addClass('visible');
			}
			else {
				$n.removeClass('visible');
			}
						
		});

	}

	function _updateTextBindings(binds, view){

		_.forEach(binds, function(n){

			var $n = $(n), invokeOn = _constructInvokingFunction($n.attr('rv-text'), view.model);

			if(_.isFunction(invokeOn)){
				invokeOn = invokeOn.call(this, view.model);	
			}

			var newText = _.escape(invokeOn);

			if($n.text() !== newText){
				$n.text(newText);
			}
						
		});

	}
	
	function _updateHtmlBindings(binds, view){

		_.forEach(binds, function(n){

			var $n = $(n), invokeOn = _constructInvokingFunction($n.attr('rv-html'), view.model);

			if(_.isFunction(invokeOn)){
				invokeOn = invokeOn.call(this, view.model);	
			}

			var newText = _.escape(invokeOn);

			if($n.html() !== newText){
				$n.html(newText);
			}
						
		});

	}

	function _updateWidthBindings(binds, view){

		_.forEach(binds, function(n){

			var $n = $(n), invokeOn = _constructInvokingFunction($n.attr('rv-width'), view.model);

			if(_.isFunction(invokeOn)){
				invokeOn = invokeOn.call(this, view.model);	
			}

			$n.css('width', _.escape(invokeOn) + "%");
						
		});

	}

	_.templateSettings.interpolate = /{#([\s\S]+?)#}/g;
	_.templateSettings.evaluate = /{%([\s\S]+?)%}/g;


	var MinimalBindView = function MinimalBindView(selector, model, options){

		var that = this,
			template_el = undefined,
			template_area = undefined,
			template_source = undefined,
			template_data_source = undefined,
			click_bindings = undefined,
			disabled_bindings = undefined,
			visible_bindings = undefined,
			width_bindings = undefined,
			text_bindings = undefined,
			change_bindings = undefined,
			html_bindings = undefined,
			submit_bindings = undefined;

		that.model = model;
		that.selector = selector.selector;

		click_bindings = selector.find('[rv-on-click]');

		change_bindings = selector.find('[rv-on-change]');

		submit_bindings = selector.find('[rv-on-submit]');

		disabled_bindings = selector.find('[rv-disabled]');

		visible_bindings = selector.find('[rv-visible]');

		text_bindings = selector.find('[rv-text]');
		
		html_bindings = selector.find('[rv-html]');

		width_bindings = selector.find('[rv-width]');

		template_el = selector.find('[rv-template]').first();

		if(template_el && template_el.length > 0){

			template_source = template_el.html();

			template_area = template_el.parent();

			template_data_source = template_el.attr('rv-template');

		}

		that.template = (template_source) ? _.template(template_source) : function(){return ''};

		//rv-template //il risultato dell'applicazione del template lo inserisco nel parent del template stesso (vediamo che succede)

		if(click_bindings && click_bindings.length > 0){

			selector.on('click', '[rv-on-click]', function(evt){

				var invokeOn = _constructInvokingFunction($(this).attr('rv-on-click'), that.model);

				if(invokeOn){
					invokeOn.call(this, evt, that.model);
				}

			});

		}

		// console.log('change_bindings', change_bindings);

		if(change_bindings && change_bindings.length > 0){

			selector.on('change', '[rv-on-change]', function(evt){

				var invokeOn = _constructInvokingFunction($(this).attr('rv-on-change'), that.model);

				invokeOn.call(this, evt, that.model);

			});

		}

		if(submit_bindings && submit_bindings.length > 0){

			submit_bindings.on('submit', function(evt){

				var invokeOn = _constructInvokingFunction($(this).attr('rv-on-submit'), that.model);

				invokeOn.call(this, evt, that.model);

			});

		}


		_updateDisabledBindings(disabled_bindings, that);

		_updateVisibleBindings(visible_bindings, that);

		_updateTextBindings(text_bindings, that);
		
		_updateHtmlBindings(html_bindings, that);
		
		_updateWidthBindings(width_bindings, that);

		that.binds = _.union(disabled_bindings.toArray(), visible_bindings.toArray(), text_bindings.toArray(), html_bindings.toArray(), width_bindings.toArray());

		if(template_area && template_data_source){

			that.applyTemplate = function(){

				var invokeOn = _constructInvokingFunction(template_data_source, that.model);

				// console.log("Template info", template_area, template_data_source, that.template(invokeOn), template_el);

				template_area.html(that.template(invokeOn));

			};

			that.applyTemplate();
		}
		else {
			that.applyTemplate = function(){};
		}

	}

	MinimalBindView.prototype.sync = function(newData){
		
		this.applyTemplate();

		var forDisabled = _.filter(this.binds, function(el){ return el.attributes['rv-disabled'] !== undefined; }),
			forVisible = _.filter(this.binds, function(el){ return el.attributes['rv-visible'] !== undefined; }),
			forText = _.filter(this.binds, function(el){ return el.attributes['rv-text'] !== undefined; }),
			forHtml = _.filter(this.binds, function(el){ return el.attributes['rv-html'] !== undefined; }),
			forWidth = _.filter(this.binds, function(el){ return el.attributes['rv-width'] !== undefined; });

		_updateDisabledBindings(forDisabled, this);
		_updateVisibleBindings(forVisible, this);
		_updateTextBindings(forText, this);
		_updateHtmlBindings(forHtml, this);
		_updateWidthBindings(forWidth, this);


	};


	var _instances = [];

	var module = {

		/**
			Created a binded View
			
			selector the element the is the container of the element that are bindable
			model the ViewModel that is behind the view data and that is responsible from handling 
			options
		*/
		bind: function(selector, model, options){

			if ( !selector ) {
				throw "Please specify a selector or an HTML Element or a JQuery selected single element";
			}
			
			// Handle strings
			if ( typeof selector === "string" ) {
				selector = $(selector);
			}
			// HANDLE: selector = DOMElement
			else if( selector.nodeType ){
				selector = $(selector);
			}
			else if(selector instanceof jQuery) { }
			else{
				throw "Unknown selector type, sorry :(";
			}

			var instance = new MinimalBindView(selector, model, options);

			_instances.push(instance);

			return instance;

		},

		configure: function(config){

		},

		refreshAll: function(){
			_.forEach(_instances, function(i){
				i.sync();
			});
		}

	}

	return module;

});