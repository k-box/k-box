define("modules/panels", ["jquery", "DMS", "combokeys", "language"], function ($, DMS, _combokeys, Lang) {
    
	console.log('panels module initialization...');

	var _available_panels = undefined;

	var _opened_panels = undefined, 
		_opened_panel_id = undefined, 
		_panelContent = null;

	var keyb = new _combokeys(document);


	var panel_error_content = '<a href="#close" title="' + Lang.trans('panels.close_btn') + '" class="close icon-navigation-white icon-navigation-white-ic_close_white_24dp"></a><div class="header"><h4 class="title">%title%</h4></div><p>%message%</p>';


	function _initialize(template){
		$(document).append(template);
	}


	function _getPanel(){

		var $modal = $('.js-panel');

		if($modal){
			_panelContent = $modal.find('.js-panel-content');
		}

		return $modal;

	}


	function _closeBtnCallback(){
		
		module.closeAll();
		
		return false;

	}


	function _formPostCallback(evt){

		var $form = $(this);

		var params = $form.serializeJSON();

		DMS.Ajax.postHtml($form.attr('action'), params, function(data){

			_available_panels.html(data);

		}, function(obj, err, text){

			console.log('Error', err, text);
			
		}, true);

		evt.preventDefault();
		return false;

	}

	function _clickCallback(evt){
		
		console.log("Click callback", this, evt);

		var that = $(this),
			panel = that.parents('.js-panel').find('h4.title'),
			data = that.data();
		
		if(panel.length > 0){
			data.title = panel.text();
		}
		_opened_panels.trigger('dms:panel-click', [data]);

		evt.preventDefault();
		return false;

	}


	var module = {

		showProgress: function(id){
			_panelContent.html( Lang.trans('panels.loading_message') );
		},

		showError: function(id){
			_panelContent.html(newContent);
		},

		updateContent: function(id, newContent){
			_panelContent.html(newContent);
			if(newContent.indexOf(Lang.trans('panels.loading_message')) === -1){
            	_opened_panels.trigger('dms:panel-loaded', [_opened_panels]);
			}
		},

		openAjax: function(id, vm, url, params, options){

			if(module.isOpened(id)){
				// same info opened, so need to be closed
				return module.close(id);
			}

			var panel = module.open(id, vm, options);

			

			// if(_opened_panel_id !== id){

				module.updateContent(id, '<a href="#close" title="' + Lang.trans('panels.close_btn') + '" class="close icon-navigation-black icon-navigation-black-ic_close_black_24dp"></a> ' + Lang.trans('panels.loading_message') );

				if(!params){
					params = {};
				}

				DMS.Ajax.getHtml(url, params, function(ok){

					module.updateContent(id, ok);

					_opened_panel_id = id;

				}, function(obj, err, text){
					
					console.error(obj, err, text);
					
					var message = obj.status === 404 ? Lang.trans('errors.not_found') : (obj.status === 403 ? Lang.trans('errors.403_title') : obj.responseText);
					var content = panel_error_content.replace('%title%', Lang.trans('panels.load_error_title')).replace('%message%', message);

					module.updateContent(id, content);
				});
			// }
            return panel;
		},

		isOpened: function(id){
			return _opened_panel_id === id;
		},

		anyOpened: function(){
			return _opened_panels; //DMS.Utils.countKeys(_opened_panels) > 0;
		},
		
		open: function(id, vm, options){

			if(module.isOpened(id)){
				// same info opened, so need to be close
				return module.close(id);
			}

			// the info has not being shown

			var default_options = {
				callbacks: {
					click: $.noop(),
					open: $.noop(),
				}
			};

			options = $.extend(default_options, options);

			_opened_panels = _getPanel();
				
			_opened_panels.addClass('c-panel--visible');
			
			keyb.bind('esc', module.closeAll);

			_opened_panels.on('click', '.js-panel-close', _closeBtnCallback);
			_opened_panels.on('submit', 'form', _formPostCallback);

			_opened_panels.on('click', '[data-action]', _clickCallback);

			_opened_panels.on('dms:panel-click', options.callbacks.click);
            
            _opened_panels.trigger('dms:panel-open', [_opened_panels]);

			return _opened_panels;

		},

		/**
		 * Closes an opened panel by it's id
		 * @param  {string} id the id of the panel to close
		 */
		close: function(id){

			if(module.isOpened(id)){
				_opened_panels.removeClass('c-panel--visible');
				_opened_panels.off('click', '.js-panel-close', _closeBtnCallback);
				_opened_panels.off('submit', 'form', _formPostCallback);
				_opened_panels.off('click', '[data-action]', _clickCallback);
				_opened_panels.off('dms:panel-click');
				_opened_panels = undefined;
				_panelContent = null;
				keyb.unbind('esc');

				_opened_panel_id = undefined;
			}

		},

		closeAll: function(){

			_opened_panels.removeClass('c-panel--visible');
			_opened_panels.off('click', '.js-panel-close', _closeBtnCallback);
			_opened_panels.off('submit', 'form', _formPostCallback);
			_opened_panels.off('click', '[data-action]', _clickCallback);
			_opened_panels.off('dms:panel-click');
			_opened_panels = undefined;
			_panelContent = null;

			keyb.unbind('esc');

			_opened_panel_id = undefined;

		},

	};

	return module;
});
