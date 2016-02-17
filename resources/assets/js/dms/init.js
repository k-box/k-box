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


	var $document = _$(document);
	
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
		VERSION: '0.1',

		initialize: function(){
			_token = _$("meta[name='token']").attr('content');
			_base  = _$("meta[name='base']").attr('content');
			longRunningMsg = _$('#long-running-message');
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
		},

		Paths : {
			STARRED: 'documents/starred',
			SEARCH: 'search',
			IMPORT: 'documents/import',
			DOCUMENTS: 'documents',
			UPLOAD_FALLBACK: 'documents/create',
			GROUPS: 'documents/groups',
			GROUPS_CREATE: 'documents/groups/create',
			GROUPS_EDIT: 'documents/groups/{ID}/edit',
			SHARES: 'shares',
			SHARE_CREATE: 'shares/create',
			STORAGE_REINDEX_ALL: 'administration/storage/reindex-all',
			USER_PROFILE_OPTIONS: 'profile/options',
			MAP_SEARCH: 'visualizationdata',
			
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
				_alert(title, text, "success");
			},

			error: function(title, text){
				_alert(title, text, "error");
			},
			
			warning: function(title, text){
				_alert(title, text, "warning");
			},

			show: function(title, text){
				_alert(title, text);
			},

			/**
			 * Close the currently open MessageBox programatically.
			 * @return {[type]} [description]
			 */
			close: function(){
				_alert.close();
			},

			wait: function(title, text){
				_alert({
					title: title,
					text: text,
					showCancelButton: false,
					showConfirmButton: false,
					closeOnConfirm: false,
					closeOnCancel: false });
			},

			deleteQuestion: function(title, text, callback, close_on_cancel, close_on_confirm){

				_alert({
					title: title,
					text: text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, delete it!",
					cancelButtonText: "No, cancel!",
					closeOnConfirm: close_on_confirm ? close_on_confirm : false,
					closeOnCancel: close_on_cancel ? close_on_cancel : false }, 
					callback
					);

			},

			/**
			 * Show a question to the user with two possible answer (yes or no, true or false)
			 * @param  {[type]}   title    [description]
			 * @param  {[type]}   text     [description]
			 * @param  {Function} callback will be called when the user select an answer with a parameter isConfirm
			 * @return {[type]}            [description]
			 */
			question: function(title, text, cofirmBtnText, cancelBtnText, callback){

				_alert({
					title: title,
					text: text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#6BB9F0",
					confirmButtonText: cofirmBtnText,
					cancelButtonText: cancelBtnText,
					allowOutsideClick:true,
					closeOnConfirm: false,
					closeOnCancel: false }, 

					callback
					);

			},

			prompt: function(title, text, placeholder, callback){
				_alert({
					title: title,
					text: text,
					type: "input",
					showCancelButton: true,
					closeOnConfirm: false,
//					animation: "slide-from-top",
					inputPlaceholder: placeholder }, 
					function(inputValue){
						if (inputValue === false) return false;
						if (inputValue === "") {
							swal.showInputError("You need to write something!");
							return false;
						}
						callback(inputValue); 
					});
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

				copyTo: function(data, success, error){
					console.log('Calling Bulk.copyTo', data);

					module.Ajax.post(module.Paths.DOCUMENTS + '/copy', data, success, error);
				},
				
				restore: function(data, success, error){
					console.log('Calling Documents.remove', data);

					module.Ajax.put(module.Paths.DOCUMENTS + '/restore', data, success, error);
				},
				
				makePublic: function(data, success, error){
					console.log('Calling Documents.remove', data);

					module.Ajax.post(module.Paths.DOCUMENTS + '/makepublic', data, success, error);
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
				
				

				openEditPage: function(id){
					
					module.Progress.start();

					module.navigate(module.Paths.DOCUMENTS + '/' + id + '/edit');
				},
				
				visualizationSearch: function(arg_obj, success, error){
					module.Ajax.get(module.Paths.MAP_SEARCH, arg_obj, success, error);
				}

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

				open: function(id){
					module.navigate(module.Paths.GROUPS + '/' + id);
				}

			},
			
			Shared : {
				openGroup: function(id){
					module.navigate(module.Paths.SHARES + '/group/' + id);
				},
				
				remove: function(id, success, error){
					if($.isArray(id) && id.length > 1){
						return module.Ajax.put(module.Paths.SHARES + '/deletemultiple', {shares:id}, success, error);
					}
					else {
						return module.Ajax.del(module.Paths.SHARES + '/' + ($.isArray(id) ? id[0] : id), success, error);
					}
				}
				
				
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
				}
			}


		},

		

	};


	return module;

})($, NProgress, undefined, swal);