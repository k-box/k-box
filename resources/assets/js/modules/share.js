define("modules/share", ["jquery", "DMS","lodash", "combokeys", "language", "sweetalert", "modules/panels",], function ($, DMS,_, _combokeys, Lang, _alert, Panels) {
    
	console.log('share module initialization...');

	

	var _items = [],
		_switch = null,
		_switchIsPublic = false,
		_switchNetwork = '',
		_switchLabel = null,
		_panel = null,
		_collapsed = true,
		_clipboard = false,
		_publicLinkID = null;
		_originalLinks = null;
		/**
		 * To track if there are actions that might be fail if the dialog is closed
		 */
		_actionIsRunning = false;


	function _getMessageFromErrorResponse(obj, err, errText){
		var message = errText;

		if(obj.responseJSON && obj.responseJSON.status === 'error'){
			message = obj.responseJSON.message;
		}
		else if(obj.responseJSON && obj.responseJSON.error){
			message = obj.responseJSON.error;
		}
		else if(obj.status == 422){

			if(obj.responseJSON){
				var html = '';
				$.each(obj.responseJSON, function(index, el){

					html += $.isArray(el) ? el[0]: el;

				});
				message = html;
			}
			else {
				message = obj.responseText ? obj.responseText : errText;
			}
		}

		return message;
	}

	function _publishSwitchClick(evt){

		var $this = $(this);

		_panel.find('.error-container--visible').removeClass('error-container--visible');

		if(_switchIsPublic && $this.data('action') === 'make_private'){
			_switchMakePrivate($this);
		}
		else if(!_switchIsPublic && $this.data('action') === 'make_public'){
			_switchMakePublic($this);
		}
	}

	function _switchMakePrivate(el){
		
		_switch.addClass('c-switch--processing');
		_switchLabel.text(Lang.trans('share.dialog.unpublishing'));

		_switch.find('.c-switch__button--selected').removeClass('c-switch__button--selected');
		el.addClass('c-switch__button--selected');
		_actionIsRunning = true;
		var filtered = _.map(_.filter(_items, {'type' : 'document'}), 'id');

		var id = filtered[0];

		DMS.Services.Documents.makePrivate(id, function(data){

			_switchActionComplete(data);
                
		}, function(obj, err, errText){
                
			_switchActionComplete(null, _getMessageFromErrorResponse(obj, err, errText));
			
		});
	}

	function _share(evt){
		var groups = [],
		    documents = [];

		_panel.find('.js-error-container').removeClass('error-container--visible');

		$.each(_items, function(index, sel){

			if(sel.type === 'group'){

				groups.push(sel.id);
			}
			else{

				documents.push(sel.id);
			}

		});

		var button = $(this);

		button.attr('disabled', true);


		var params = {
			"with_users" : _panel.find(".js-select-users").select2().val(),
			"groups" : groups,
			"documents" : documents,
		};

		DMS.Services.Shared.create(params, function(data){

			button.attr('disabled', false);
			_actionIsRunning = false;
			_reloadDialog();
                
		}, function(obj, err, errText){
            
			button.attr('disabled', false);

			var message = _getMessageFromErrorResponse(obj, err, errText);

			_panel.find('.js-error-container-top').html(message).addClass('error-container--visible');
			
			_actionIsRunning = false;
		});

		return false;
	}

	function _unshare(evt){

		_actionIsRunning = true;

		var button = $(this),
		    id = button.data('id');

		_panel.find('.js-error-container').removeClass('error-container--visible');

		button.text(Lang.trans('share.removing'));
		button.attr('disabled', true);
		button.parents('.shared-list__item').addClass('shared-list__item--removing');

		if(!id){
			return false;
		}

		DMS.Services.Shared.remove(id, function(data){

			button.text(Lang.trans('share.removed'));
			button.attr('disabled', false);

			button.parents('.shared-list__item').hide();

			_actionIsRunning = false;
                
		}, function(obj, err, errText){
            
			var message = errText;

			if(obj.responseJSON && obj.responseJSON.status === 'error'){
				message = obj.responseJSON.message;
			}
			else if(obj.responseJSON && obj.responseJSON.error){
				message = obj.responseJSON.error;
			}
			else if(obj.status == 422){
				message = obj.responseText ? obj.responseText : errText;
			}

			_panel.find('.js-error-container').html(message).addClass('error-container--visible');
			
			_actionIsRunning = false;
		});
		return false;
	}

	function _toggleAccess(evt){
		if(!_collapsed){
			_panel.find('.js-access-list').removeClass('dialog__section__inner--collapsed');
			// _panel.find('.dialog__section:not(.js-access-section)').addClass('dialog__section--collapsed');
			_collapsed = true;
		}
		else {
			_panel.find('.js-access-list').addClass('dialog__section__inner--collapsed');
			// _panel.find('.dialog__section--collapsed').removeClass('dialog__section--collapsed');
			_collapsed = false;
		}

		evt.stopPropagation();
		return false;
	}

	function _sectionTitleClick(evt){
		if(_collapsed){
			_panel.find('.js-access-list').addClass('dialog__section__inner--collapsed');
			_collapsed = false;
		}

		evt.stopPropagation();

		return false;
	}
	
	function _switchMakePublic(el){
		_switch.addClass('c-switch--processing');
		_switchLabel.text(Lang.trans('share.dialog.publishing'));
		_switch.find('.c-switch__button--selected').removeClass('c-switch__button--selected');
		el.addClass('c-switch__button--selected');
		_actionIsRunning = true;

		
		var filtered = _.map(_.filter(_items, {'type' : 'document'}), 'id');

		var params = {document_id:filtered[0]};

		DMS.Services.Documents.makePublic(params, function(data){

			_switchActionComplete(data);
                
		}, function(obj, err, errText){
                
			if(obj.responseJSON && obj.responseJSON.status === 'error'){
				_switchActionComplete(null, obj.responseJSON.message);
			}
			else if(obj.responseJSON && obj.responseJSON.error){
				_switchActionComplete(null, obj.responseJSON.error);
			}
			else if(obj.status == 422){
				_switchActionComplete(null, (obj.responseText ? obj.responseText : errText));
			}
			else {
				_switchActionComplete(null, errText);
			}
			
		});

		
	}

	function _switchActionComplete(data, error){
		_switch.removeClass('c-switch--processing');

		if(error || data && data.status==='error'){
			_switch.addClass('c-switch--error');
			_panel.find('.js-publish-section .js-error-container').html(error || data.error).addClass('error-container--visible');
		}
		else {
			_switch.addClass('c-switch--success');
			_switchIsPublic = !_switchIsPublic;

			if(data.descriptor && data.publication && data.publication.pending){

				_switchLabel.text(Lang.trans(data.descriptor.is_public ? 'share.dialog.publishing' : 'share.dialog.unpublishing', {network: _switchNetwork}));
			}
			else {

				_switchLabel.text(Lang.trans(_switchIsPublic ? 'share.dialog.published' : 'share.dialog.not_published', {network: _switchNetwork}));
			}

			setTimeout(function(){
				_switch.removeClass('c-switch--success');
			}, 2500);
		}

		_actionIsRunning = false;

	}

	function _closing(){

		var willClose = _actionIsRunning ? false : true;

		if(willClose && _panel){
			_panel.off('click', '.js-unshare', _unshare);
			_panel.off('click', '.dialog__section:not(.js-access-section) .dialog__section__title', _sectionTitleClick);
			_panel.off('click', '.js-access', _toggleAccess);
			_panel.off('click', '.js-share', _share);
			_panel.off('click','.js-publish-switch-button', _publishSwitchClick);
			_panel.off('change', '.js-link-type', _changeLinkType);
		}
		
		return willClose;
	
	}

	function _changeLinkType(evt){

		var $this = $(this),
		    value = $this.val();

		evt.stopPropagation();

		console.log(value);

		if(value==='public'){
			// generate a public link

			var id = _items[0].id; //todo: check for single selection and not collection

			_actionIsRunning = true;

			DMS.Services.Shared.createPublicLink(id, function(data){

				console.log(data);

				if(data.id && data.url){
					// _originalLinks = _panel.find("#document_link").val();
					// _panel.find("#document_link").val(data.url);
					_publicLinkID = data.id;
				}

				_actionIsRunning = false;
					
			}, function(obj, err, errText){
				
				var message = errText;

				if(obj.responseJSON && obj.responseJSON.status === 'error'){
					message = obj.responseJSON.message;
				}
				else if(obj.responseJSON && obj.responseJSON.error){
					message = obj.responseJSON.error;
				}
				else if(obj.status == 422){
					message = obj.responseText ? obj.responseText : errText;
				}

				_panel.find('.js-error-container').html(message).addClass('error-container--visible');
				
				_actionIsRunning = false;
			});
		}
		else if(value==='internal'){
			// remove the public link if any

			_publicLinkID = _publicLinkID ? _publicLinkID : _panel.find("#document_link").data('link');
			// _originalLinks = _originalLinks ? _originalLinks : _panel.find("#document_link").data('links');
			if(_publicLinkID){

				DMS.Services.Shared.deletePublicLink(_publicLinkID, function(data){

					console.log(data);

					// _panel.find("#document_link").val(_originalLinks);
					_publicLinkID = null;

					_actionIsRunning = false;
						
				}, function(obj, err, errText){
					
					var message = errText;

					if(obj.responseJSON && obj.responseJSON.status === 'error'){
						message = obj.responseJSON.message;
					}
					else if(obj.responseJSON && obj.responseJSON.error){
						message = obj.responseJSON.error;
					}
					else if(obj.status == 422){
						message = obj.responseText ? obj.responseText : errText;
					}

					_panel.find('.js-error-container').html(message).addClass('error-container--visible');
					
					_actionIsRunning = false;
				});

			}
		}

		return false;
	}

	function _reloadDialog(){

		var groups = [],
			documents = [];

			$.each(_items, function(index, sel){

				if(sel.type === 'group'){

					groups.push(sel.id);
				}
				else{

					documents.push(sel.id);
				}

			});

			var dialogContent = _panel.find('.js-dialog-content');

			DMS.Ajax.getHtml(DMS.Paths.SHARE_CREATE, 
			{collections:groups, documents:documents}, function(ok){

				dialogContent.html(ok);

				setTimeout(function(){
					initializeShareTargetSelect(_panel, {selection: {collections:groups, documents:documents}, ref: 1});
				})

			}, function(obj, err, text){

				dialogContent.html( Lang.trans('panels.load_error', {error: text}));

			});

	}

	function formatShareTargetResult (result) {
		if (result.loading) {
			return result.text;
		}
		
		return result.name;
	}
	  
	function formatShareTargetSelection (repo) {
		return repo.name || repo.text;
	}


	function initializeShareTargetSelect(panel, options)
	{
		panel.find(".js-select-users").select2({
			placeholder: Lang.trans('share.dialog.select_users'),
			tokenSeparators: [',', ' '],
			minimumInputLength: 2,
			templateResult: formatShareTargetResult,
			  templateSelection: formatShareTargetSelection,
			ajax: {
				url: DMS.Paths.fullUrl(DMS.Paths.SHARES_TARGET_FIND),
				method: 'POST',
				dataType: 'json',
				data: function (params) {

					var selection = options.selection || {collections:[], documents:[]};
					
					var queryParameters = {
					  s: params.term,
					  documents: selection.documents,
					  collections: selection.collections,
					  _token: DMS.csrf()
					}
				
					return queryParameters;
				},
				processResults: function (data) {
					return {
						results: data.data
					};
				}
			  }
		}, options.ref || undefined);
	}


	var module = {

		open: function(items, options){

			_items = items;
			_collapsed = false;
			_actionIsRunning = false;

			var groups = [],
			documents = [];

			$.each(items, function(index, sel){

				if(sel.type === 'group'){

					groups.push(sel.id);
				}
				else{

					documents.push(sel.id);
				}

			});

			Panels.dialogOpen(
				DMS.Paths.SHARE_CREATE, 
				{collections:groups, documents:documents},
				{force:true,
					callbacks : {
					closing: _closing
				}}
			).on('dms:panel-loaded', function(panel_evt, panel){

				_panel = panel;
				_switch = panel.find('.js-publish-switch');
				if(_switch){
					_switchLabel = _switch.find('.js-publish-switch-label');
					_switchIsPublic = _switch.data('isPublic');
					_switchNetwork = _switch.data('network');
					panel.on('click', '.js-publish-switch-button', _publishSwitchClick);
				}

				_clipboard = new Clipboard('.js-clipboard-btn');

				_clipboard.on('success', function(e) {

					var trigger = $(e.trigger);
				
					trigger.addClass('button--success');

					setTimeout(function(){
						trigger.removeClass('button--success');
					}, 2500);
					
					e.clearSelection();
				});

				_clipboard.on('error', function(e) {
					
					var trigger = $(e.trigger);

					trigger.addClass('button--error');

					trigger.parent().find('.js-copy-message-error').addClass('copy-link__message--visible');

					setTimeout(function(){
						trigger.removeClass('button--error');
					}, 2500);
					
				});

				initializeShareTargetSelect(panel, {selection: {collections:groups, documents:documents}});

				panel.on('click', '.dialog__section:not(.js-access-section) .dialog__section__title', _sectionTitleClick);

				panel.on('click', '.js-access', _toggleAccess);

				panel.on('click', '.js-unshare', _unshare);

				panel.on('click', '.js-share', _share);
				
				panel.on('change', '.js-link-type', _changeLinkType);

				if(options && options.focus == 'access'){
					_toggleAccess(panel_evt);
				}
			});

		}

	};

	return module;
});
