define("modules/share", ["jquery", "DMS","lodash", "combokeys", "language", "sweetalert", "modules/panels",], function ($, DMS,_, _combokeys, Lang, _alert, Panels) {
    
	console.log('share module initialization...');

	

	var _items = [],
		_panel = null,
		_collapsed = true,
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

			DMS.dispatch(window.document, 'dialog-show', { 
				'url': DMS.Paths.SHARE_CREATE, 
				'params' : {collections:groups, documents:documents}
			});

			// Panels.dialogOpen(
			// 	DMS.Paths.SHARE_CREATE, 
			// 	{collections:groups, documents:documents},
			// 	{force:true,
			// 		callbacks : {
			// 		closing: _closing
			// 	}}
			// ).on('dms:panel-loaded', function(panel_evt, panel){

			// 	initializeShareTargetSelect(panel, {selection: {collections:groups, documents:documents}});

			// 	panel.on('click', '.dialog__section:not(.js-access-section) .dialog__section__title', _sectionTitleClick);

			// 	panel.on('click', '.js-access', _toggleAccess);

			// 	panel.on('click', '.js-unshare', _unshare);

			// 	panel.on('click', '.js-share', _share);
				
			// 	panel.on('change', '.js-link-type', _changeLinkType);

			// 	if(options && options.focus == 'access'){
			// 		_toggleAccess(panel_evt);
			// 	}
			// });

		}

	};

	return module;
});
