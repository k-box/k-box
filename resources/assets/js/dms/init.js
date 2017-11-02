/* global swal */
/* global NProgress */
/**
	Klink DMS Javascript

	Initialize the DMS abstraction

	@author: Alessio Vertemati
 */

// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());


window.DMS = (function(_$, _nprogress, _rivets, _alert){

	var _token = null, _base = '';

	var longRunningTimeout = undefined, longRunningMsgShowed = false, longRunningMsg = undefined;

    var Lang = undefined;
    
	var $document = _$(document);

	_nprogress.configure({ showSpinner: false });
	
	_$.ajaxSetup({ cache: false });

	function _showLongRunningMessage(){
		longRunningMsg.addClass('visible');
		longRunningMsgShowed = true;
	}

	function _hideLongRunningMessage(){
		longRunningMsg.removeClass('visible');
		longRunningMsgShowed = false;
	}

	$document.ajaxStart(function(){
		_nprogress.start();

		longRunningTimeout = setTimeout(_showLongRunningMessage, 600);
	});

	$document.ajaxComplete(function(){
		_nprogress.done();

		if(longRunningTimeout){
			clearTimeout(longRunningTimeout);
		}

		if(longRunningMsgShowed){
			_hideLongRunningMessage();
		}
	});

	function _getHtml (url, params, success, error, full) {
		
		var extParams = _$.extend(params, {_token:_getToken()});

		return $.ajax({
		  url: (full) ? url : _getBase() + url,
		  type: 'get',
		  dataType: 'html',
		  data: extParams,
		  success: success,
		  error: error
		});
	}

	function _postHtml (url, params, success, error, full) {
		
		var extParams = _$.extend(params, {_token:_getToken()});

		return $.ajax({
		  url: (full) ? url : _getBase() + url,
		  type: 'post',
		  dataType: 'html',
		  data: extParams,
		  success: success,
		  error: error
		});
	}

	function _get (url, params, success, error) {
		
		var extParams = _$.extend(params, {_token:_getToken()});

		return $.ajax({
		  url: _getBase() + url,
		  type: 'get',
		  dataType: 'json',
		  data: extParams,
		  success: success,
		  error: error
		});
	}

	function _post (url, params, success, error, full) {
		
		var extParams = _$.extend(params, {_token:_getToken()});

		return $.ajax({
		  url: (full) ? url : _getBase() + url,
		  type: 'post',
		  dataType: 'json',
		  data: extParams,
		  success: success,
		  error: error
		});
	}

	function _put (url, params, success, error) {
		
		var extParams = _$.extend(params, {_token:_getToken()});

		return $.ajax({
		  url: _getBase() + url,
		  type: 'put',
		  dataType: 'json',
		  data: extParams,
		  success: success,
		  error: error
		});
	}

	function _delete (url, success, error) {
		
		var extParams = {_token:_getToken()};

		return $.ajax({
		  url: _getBase() + url,
		  type: 'delete',
		  dataType: 'json',
		  data: extParams,
		  success: success,
		  error: error
		});
	}

	function _navigate(path, getParams, full){

		_nprogress.start();

		if(getParams){
			location.href=((full) ? path : _getBase() + path) +'?' + _$.param(getParams);
		}
		else {
			location.href=((full) ? path : _getBase() + path);
		}

	}

	function _getBase(){
		return (_base) ? _base : _$("meta[name='base']").attr('content');
	}

	function _getToken(){
		return (_token) ? _token : _$("meta[name='token']").attr('content');
	}


	var module = { 
		VERSION: '0.2',

		initialize: function(lang){
			_token = _$("meta[name='token']").attr('content');
			_base  = _$("meta[name='base']").attr('content');
			longRunningMsg = _$('#long-running-message');
            Lang = lang;
			
			window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

			/**
			 * Next we will register the CSRF Token as a common header with Axios so that
			 * all outgoing HTTP requests automatically have it attached. This is just
			 * a simple convenience so we don't have to attach every token manually.
			 */
			if (_token) {
				window.axios.defaults.headers.common['X-CSRF-TOKEN'] = _token;
			} else {
				console.error('CSRF token not found in page.');
			}


			var accept_terms = _$('.js-terms-accept-dialog');
			
			if(accept_terms.length === 1){
				
				if(window.localStorage && !window.localStorage['terms_accepted']){
					accept_terms.removeClass('alert__hidden');
				}
				
				accept_terms.on('click', 'button', function(evt){
					var _that = _$(this);
					var action = _that.data('action');
					
					if(action==='close'){
						accept_terms.addClass('alert__hidden');
						
						if(window.localStorage){
							window.localStorage.setItem('terms_accepted', true);
						}
					}
					else if(action==='accept'){
						module.Services.Options.acceptTerms(function(){
							accept_terms.addClass('alert__hidden');
						});
					} 
				});
				
				
				
			}

			var drawer = $('.js-drawer'), drawerOpened = false;

			if(drawer && drawer.length === 1){
				 var trigger = $(".js-drawer-trigger");

				trigger.on('click', function(evt){

					if(!drawerOpened){

						drawer.addClass('sidebar--opened');
						evt.stopPropagation();
						evt.preventDefault();
						drawerOpened = true;
					}
					else {
						drawer.removeClass('sidebar--opened');
						evt.stopPropagation();
						evt.preventDefault();
						drawerOpened = false;
					}

				});

			}

		},

		/**
		 * Ajax functions commodities
		 * @type {Object}
		 */
		Ajax: {
			getHtml: _getHtml,
			postHtml: _postHtml,
			get: _get,
			post: _post,
			put: _put,
			del: _delete,
			delete: _delete,
		},

		Paths : {
			STARRED: 'documents/starred',
			SEARCH: 'search',
			IMPORT: 'documents/import',
			DOCUMENTS: 'documents',
			PUBLISHED_DOCUMENTS: 'published-documents',
			PROJECTS: 'documents/projects',
			PROJECTS_EDIT: 'projects/{ID}/edit',
			PROJECTS_API: 'projects',
			UPLOAD_FALLBACK: 'documents/create',
			GROUPS: 'documents/groups',
			GROUPS_CREATE: 'documents/groups/create',
			GROUPS_EDIT: 'documents/groups/{ID}/edit',
			SHARES: 'shares',
			PUBLICLINK: 'links',
			SHARE_CREATE: 'shares/create',
			STORAGE_REINDEX_ALL: 'administration/storage/reindexall',
			USER_PROFILE_OPTIONS: 'profile/options',
			MICROSITE: "microsites/{ID}",
			
			PEOPLE: 'people',

			fullUrl: function(path){
				return _getBase() + path;
			}
		},

		/**
		 * Perform a navigation to a page
		 * @type {[type]}
		 */
		navigate: _navigate,

		navigateReload: function(){
			_nprogress.start();

			window.location.reload();
		},

		csrf: function(){
			return _token;
		},

		/**
		 * For showing some messages to the user
		 * @type {Object}
		 */
		MessageBox: {

			/**
			 * Show a success styles message
			 * @param  {[type]} title [description]
			 * @param  {[type]} text  [description]
			 * @return {[type]}       [description]
			 */
			success: function(title, text){
                return _alert({
					title: title,
					text: text,
                    type: "success",
                    confirmButtonText: Lang.trans('actions.dialogs.ok_btn'),
					showCancelButton: false,
					showConfirmButton: true });
			},

			error: function(title, text){
                return _alert({
					title: title,
					text: text,
                    type: "error",
                    confirmButtonText: Lang.trans('actions.dialogs.ok_btn'),
					showCancelButton: false,
					showConfirmButton: true });
			},
			
			warning: function(title, text){
				return _alert({
					title: title,
					text: text,
                    type: "warning",
                    confirmButtonText: Lang.trans('actions.dialogs.ok_btn'),
					showCancelButton: false,
					showConfirmButton: true });
			},

			show: function(title, text){
				return _alert({
					title: title,
					text: text,
                    confirmButtonText: Lang.trans('actions.dialogs.ok_btn'),
					showCancelButton: false,
					showConfirmButton: true });
			},

			/**
			 * Close the currently open MessageBox programatically.
			 * @return {[type]} [description]
			 */
			close: function(){
				_alert.close();
			},

			wait: function(title, text){
				return _alert({
					title: title,
					text: text,
					showCancelButton: false,
					showConfirmButton: false });
			},

			deleteQuestion: function(title, text, options){

				var _options = _$.extend({
					title: title,
					text: text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: Lang.trans('actions.dialogs.remove_btn'),
					cancelButtonText: Lang.trans('actions.dialogs.cancel_btn'),
				    showLoaderOnConfirm: true }, options || {});

				return _alert(_options);

			},

			/**
			 * Show a question to the user with two possible answer (yes or no, true or false)
			 * @param  {[type]}   title    [description]
			 * @param  {[type]}   text     [description]
			 * @param  {Function} callback will be called when the user select an answer with a parameter isConfirm
			 * @return {[type]}            [description]
			 */
			question: function(title, text, cofirmBtnText, cancelBtnText, callback){
				return _alert({
					title: title,
					text: text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#6BB9F0",
					confirmButtonText: cofirmBtnText,
					cancelButtonText: cancelBtnText,
					allowOutsideClick:true }
					).then(callback, function(){});

			},

			prompt: function(title, text, placeholder, options){

				var _options = _$.extend({
					title: title,
					text: text,
					input: "text",
					showCancelButton: true,
					confirmButtonText: Lang.trans('actions.dialogs.ok_btn'),
					cancelButtonText: Lang.trans('actions.dialogs.cancel_btn'),
					inputPlaceholder: placeholder,
				    showLoaderOnConfirm: true,
					preConfirm: function (inputValue) {
						return new Promise(function (resolve, reject) {
							if (inputValue === false || inputValue === "") {
								reject( Lang.trans('actions.dialogs.input_required') );
							} else {
								resolve()
							}
					    })
				   } }, options || {});

				return _alert(_options);
			}

		},

		Utils : {

			countKeys: function (obj){
		        return _$.map(obj, function(n, i) { return i; }).length;
		    },

		    inArray: function (arr, what){
		        return (Array.prototype.indexOf) ? arr.indexOf(what) : _$.inArray(what, arr);
		    }

		},

		Progress: {

			start: function(){
				_nprogress.start();

				longRunningTimeout = setTimeout(_showLongRunningMessage, 1000);


			},

			done: function(){
				_nprogress.done();

				if(longRunningTimeout){
					clearTimeout(longRunningTimeout);
				}

				if(longRunningMsgShowed){
					_hideLongRunningMessage();
				}
			}

		},

		Services: {

			Starred : {

				add: function(data, success, error){
					
					console.log('Calling Star.add');

					// data = {institution: inst, descriptor: doc, visibility:'public'}

					module.Ajax.post(module.Paths.STARRED, data, success, error);

				},

				remove: function(starId, success, error){

					console.log('Calling Star.remove');

					module.Ajax.del(module.Paths.STARRED + '/' + starId, success, error);
				}

			},

			Bulk : {

				remove: function(data, success, error){
					console.log('Calling Bulk.remove', data);

					module.Ajax.post(module.Paths.DOCUMENTS + '/remove', data, success, error);
				},

				emptytrash: function(success, error){
					console.log('Calling Bulk.emptytrash');

					module.Ajax.del(module.Paths.DOCUMENTS + '/trash', success, error);
				},

				copyTo: function(data, success, error){
					console.log('Calling Bulk.copyTo', data);

					module.Ajax.post(module.Paths.DOCUMENTS + '/copy', data, success, error);
				},
				
				restore: function(data, success, error){
					console.log('Calling Documents.remove', data);

					module.Ajax.put(module.Paths.DOCUMENTS + '/restore', data, success, error);
				},
				
				makePublic: function(data, success, error){
					console.log('Calling Bulk.makePublic', data);

					module.Ajax.post(module.Paths.DOCUMENTS + '/makepublic', data, success, error);
				},
				
				makePrivate: function(data, success, error){
					console.log('Calling Bulk.makePrivate', data);

					module.Ajax.post(module.Paths.DOCUMENTS + '/makeprivate', data, success, error);
				},

			},

			Documents : {

				update: function(id, data, success, error){
					console.log('Calling Documents.update', id);

					module.Ajax.put(module.Paths.DOCUMENTS + '/' + id, data, success, error);
				},

				remove: function(id, success, error){
					console.log('Calling Documents.remove', id);

					module.Ajax.del(module.Paths.DOCUMENTS + '/' + id, success, error);
				},

				forceRemove: function(id, success, error){
					console.log('Calling Documents.remove', id);

					module.Ajax.del(module.Paths.DOCUMENTS + '/' + id + "?force=true", success, error);
				},
				
				

				openEditPage: function(id){
					
					module.Progress.start();

					module.navigate(module.Paths.DOCUMENTS + '/' + id + '/edit');
				},

				makePublic: function(data, success, error){
					console.log('Calling Documents.makePublic', data);

					module.Ajax.post(module.Paths.PUBLISHED_DOCUMENTS, data, success, error);
				},
				
				makePrivate: function(id, success, error){
					console.log('Calling Documents.makePrivate', id);

					module.Ajax.delete(module.Paths.PUBLISHED_DOCUMENTS + '/' + id, success, error);
				},

			},


			Groups: {

				create: function(data, success, error){
					console.log('Calling Groups.create', data);

					if(data.ok_template){
						module.Ajax.postHtml(module.Paths.GROUPS, data, success, error);
					}
					else {
						module.Ajax.post(module.Paths.GROUPS, data, success, error);
					}

					
				},

				// edit: function(id) {
				// 	GROUPS_EDIT: 'documents/groups/{ID}/create',

				// 	module.
				// }

				update: function(id, data, success, error){
					console.log('Calling Groups.update', id, data);

					module.Ajax.put(module.Paths.GROUPS + '/' + id, data, success, error);
				},

				remove: function(id, success, error){
					console.log('Calling Groups.remove');

					module.Ajax.del(module.Paths.GROUPS + '/' + id, success, error);
				},

				forceRemove: function(id, success, error){
					console.log('Calling Groups.remove');

					module.Ajax.del(module.Paths.GROUPS + '/' + id + "?force=true", success, error);
				},

				open: function(id){
					module.navigate(module.Paths.GROUPS + '/' + id);
				}

			},
			
			Shared : {
				openGroup: function(id){
					module.navigate(module.Paths.SHARES + '/group/' + id);
				},

				create: function(parameters, success, error){
					module.Ajax.post(module.Paths.SHARES, parameters, success, error);
				},
				
				remove: function(id, success, error){
					if($.isArray(id) && id.length > 1){
						return module.Ajax.put(module.Paths.SHARES + '/deletemultiple', {shares:id}, success, error);
					}
					else {
						return module.Ajax.del(module.Paths.SHARES + '/' + ($.isArray(id) ? id[0] : id), success, error);
					}
				},

				createPublicLink: function(document, success, error){
					return module.Ajax.post(module.Paths.PUBLICLINK, {
						to_id: document,
						to_type: 'document'
					}, success, error);
				},
				
				deletePublicLink: function(id, success, error){
					return module.Ajax.del(module.Paths.PUBLICLINK + '/' + id, success, error);
				},
				
				
			},
			
			PeopleGroup: {
				addUser: function(groupId, userId, success, error){
					module.Ajax.put(module.Paths.PEOPLE + '/' + groupId, {user:userId, action:'add'}, success, error);
				},
				addGroup: function(name, success, error){
					module.Ajax.post(module.Paths.PEOPLE, {name:name}, success, error);
				},
				removeUser: function(groupId, userId, success, error){
					module.Ajax.put(module.Paths.PEOPLE + '/' + groupId, {user:userId, action:'remove'}, success, error);
				},
				removeGroup: function(groupId, success, error){
					module.Ajax.del(module.Paths.PEOPLE + '/' + groupId, success, error);
				},				
				renameGroup: function(groupId, name, success, error){
					module.Ajax.put(module.Paths.PEOPLE + '/' + groupId, {name:name}, success, error);
				},
				makePersonal: function(groupId, success, error){
					module.Ajax.put(module.Paths.PEOPLE + '/' + groupId, {make_personal:0}, success, error);
				},
				makeInstitutional: function(groupId, success, error){
					module.Ajax.put(module.Paths.PEOPLE + '/' + groupId, {make_institutional:1}, success, error);
				}
			},

			Options: {
				saveListStyle: function(style, success, error){
					module.Ajax.post(module.Paths.USER_PROFILE_OPTIONS, {list_style: style}, success, error);
				},
				acceptTerms: function(callback){
					module.Ajax.post(module.Paths.USER_PROFILE_OPTIONS, {terms_accepted: '1'}, callback, function(){
						
						module.MessageBox.error(Lang.trans('errors.preference_not_saved_title'), Lang.trans('errors.preference_not_saved_text'));
						
						callback();
					});
				}
			},
			ProjectAvatar: {
				remove: function(id, success, error){
					console.log('Calling ProjectAvatar.remove');

					module.Ajax.del(module.Paths.PROJECTS_API + '/' + id + "/avatar", success, error);
				},
			},

			Microsite: {
				delete :  function(id, success, error){
					return module.Ajax.del(module.Paths.MICROSITE.replace('{ID}', id), success, error);
				}
			}


		},

		

	};


	return module;

})($, NProgress, undefined, swal);