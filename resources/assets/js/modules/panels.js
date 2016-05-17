define("modules/panels", ["jquery", "DMS", "combokeys", "language"], function ($, DMS, _combokeys, Lang) {
    
	console.log('panels module initialization...');

	var _available_panels = undefined, _visible = false;

	var _opened_panels = undefined, _opened_dialog = undefined, _opened_panel_id = undefined, _opened_dialog_id = undefined, _panel_cache = undefined;

	var keyb = new _combokeys(document); //TODO: optimize this


	var panel_template = '<div class="panel"><a href="#close" title="' + Lang.trans('panels.close_btn') + '" class="close icon-navigation-black icon-navigation-black-ic_close_black_24dp"></a><div id="inner">' + Lang.trans('panels.loading_message') + '</div></div>',
		dialog_template = '<div class="dialog"><div id="inner">' + Lang.trans('panels.loading_message') + '</div></div>',
		panel_error_content = '<a href="#close" title="' + Lang.trans('panels.close_btn') + '" class="close icon-navigation-white icon-navigation-white-ic_close_white_24dp"></a><div class="header"><h4 class="title">%title%</h4></div><p>%message%</p>';


	function _initialize(template){
		$(document).append(template);
	}


	function _getPanel(){

		var $modal = $('.panel');

		if (!$modal) {
			_initialize(panel_template);
			$modal = _getPanel();
		}

		if(!_panel_cache){
			_panel_cache = $('.panel-cache');
		}

		return $modal;

	}

	function _getDialog(){

		var $modal = $('.dialog');

		if (!$modal) {
			_initialize(dialog_template);
			$modal = _getDialog();
		}

		return $modal;

	}


	function _closeBtnCallback(){
		
		module.closeAll();
		
		return false;

	}

	function _closeDialogBtnCallback(){
		
		module.dialogClose();
		
		return false;

	}


	function _formPostCallback(evt){
		
		//TODO: identify the panel

		// console.log("Form submit", this, evt);

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
			// panel = that.parents('.panel'),
			data = that.data();
		// console.log(panel, data);
		// var id = panel.data('panel-id');
		// console.log(id);
		_opened_panels.trigger('dms:panel-click', [data]);

		evt.preventDefault();
		return false;

	}

	function _formDialogPostCallback(evt){
		
		// console.log("Form submit", this, evt);

		var $form = $(this);

		var params = $form.serializeJSON();

		console.log(params);

		DMS.Ajax.post($form.attr('action'), params, function(data){

			console.log('Dialog submit success', data);

			_opened_dialog.trigger('dms:dialog-submitted', [data]);

		}, function(obj, err, text){


			if(obj.status === 422){

				console.log(obj.responseJSON);

 				var html = '<div class="alert error">';

 				$.each(obj.responseJSON, function(index, el){

 					html += '<p>' + $.isArray(el) ? el[0]: el + '</p>';

 				});

        		html += '</div>';

        		_opened_dialog.find('.error-container').html(html);


            }
            else {
            	console.log('Error', err, text);
				
            	var html = '<div class="alert error">';

            	if(obj.responseJSON && obj.responseJSON.error){

					html += '<p>' + obj.responseJSON.error + '</p>';
	            }
				else if(obj.responseJSON){

	            	$.each(obj.responseJSON, function(index, el){

	 					html += '<p>' + $.isArray(el) ? el[0]: el + '</p>';

	 				});
	            }
	            else{
	            	html += '<p>' + err + ': ' + text + '</p>';
	            }

        		html += '</div>';

        		_opened_dialog.find('.error-container').html(html);

                _opened_dialog.trigger('dms:dialog-error', [obj, err, text]);
            }

		}, true);

		evt.preventDefault();
		return false;

	}


	var module = {

		showProgress: function(id){
			_opened_panels.html( Lang.trans('panels.loading_message') );
		},

		showError: function(id){
			_opened_panels.html(newContent);
		},

		updateContent: function(id, newContent){
			_opened_panels.html(newContent);
            _opened_panels.trigger('dms:panel-loaded', [_opened_panels]);
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
					
					var content = panel_error_content.replace('%title%', Lang.trans('panels.load_error_title')).replace('%message%', obj.responseText);

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

			// console.log(options);

			_opened_panels = _getPanel();
				
			_opened_panels.addClass('visible');
			_panel_cache.addClass('visible');
			
			keyb.bind('esc', module.closeAll);

			_opened_panels.on('click', 'a.close', _closeBtnCallback);
			_opened_panels.on('submit', 'form', _formPostCallback);

			_opened_panels.on('click', '[data-action]', _clickCallback);

			_opened_panels.on('dms:panel-click', options.callbacks.click);

			_panel_cache.on('click', _closeBtnCallback);
            
            _opened_panels.trigger('dms:panel-open', [_opened_panels]);

			return _opened_panels;

		},

		/**
		 * Closes an opened panel by it's id
		 * @param  {string} id the id of the panel to close
		 */
		close: function(id){

			if(module.isOpened(id)){
				_opened_panels.removeClass('visible');
				_opened_panels.off('click', 'a.close', _closeBtnCallback);
				_opened_panels.off('submit', 'form', _formPostCallback);
				_opened_panels.off('click', '[data-action]', _clickCallback);
				_opened_panels.off('dms:panel-click');
				_opened_panels = undefined;
				keyb.unbind('esc');
				_panel_cache.off('click', _closeBtnCallback);

				_opened_panel_id = undefined;
				_panel_cache.removeClass('visible');
			}

		},

		closeAll: function(){

			_opened_panels.removeClass('visible');
			_opened_panels.off('click', 'a.close', _closeBtnCallback);
			_opened_panels.off('submit', 'form', _formPostCallback);
			_opened_panels.off('click', '[data-action]', _clickCallback);
			_opened_panels.off('dms:panel-click');
			_opened_panels = undefined;
			_panel_cache.removeClass('visible');
			_panel_cache.off('click', _closeBtnCallback);

			keyb.unbind('esc');

			_opened_panel_id = undefined;

			// $.each(_opened_panels, function(index, el){
			// 	el.removeClass('visible');
			// 	el.off('click', 'a.close', _closeBtnCallback);
			// 	el.off('submit', 'form', _formPostCallback);
			// 	el.off('click', '[data-action]', _clickCallback);
			// 	// el.off('dms:panel:click');
			// 	delete _opened_panels[index];
			// });

			
			
		},

		/**
		 * Open a dialog and show the page specified by url 
		 * @param  {[type]}   url      [description]
		 * @param  {Function} callback [description]
		 * @return {[type]}            [description]
		 */
		dialogOpen: function(url, params, options){

			var default_options = {
				callbacks: {
					form_submit_success: $.noop(),
					form_submit_error: $.noop(),
					click: $.noop(),
				}
			};

			options = $.extend(default_options, options);


			_opened_dialog = _getDialog();

			console.log('Opening dialog', _opened_dialog);

			_opened_dialog.addClass('visible');

			_opened_dialog.on('click', '.cancel', _closeDialogBtnCallback);

			// _opened_dialog.on('click', ':not(.cancel)', options.callbacks.click);

			_opened_dialog.on('submit', 'form', _formDialogPostCallback);

			_opened_dialog.on('dms:dialog-submitted', options.callbacks.form_submit_success);

			if(!params){
				params = {};
			}

			if(!_opened_dialog_id || (_opened_dialog_id && _opened_dialog_id !== url)){

				_opened_dialog.html( Lang.trans('panels.loading_message') );

				DMS.Ajax.getHtml(url, params, function(ok){
					_opened_dialog_id = url;
					_opened_dialog.html(ok);

				}, function(obj, err, text){

					_opened_dialog.html( Lang.trans('panels.load_error', {error: text}));
				});

			}

		},

		/**
		 * Closes a dialog (only one dialog could be opened at a time)
		 * @return {[type]} [description]
		 */
		dialogClose: function(){

			_opened_dialog_id = undefined;

			_opened_dialog.removeClass('visible');

			_opened_dialog.off('click', '.cancel', _closeDialogBtnCallback);

			_opened_dialog.off('submit', 'form', _formDialogPostCallback);

			_opened_dialog.off('dms:dialog-submitted');
		},

	};

	return module;
});
