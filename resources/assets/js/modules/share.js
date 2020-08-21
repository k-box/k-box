define("modules/share", ["jquery", "DMS","lodash", "combokeys", "language", "sweetalert", "modules/panels",], function ($, DMS,_, _combokeys, Lang, _alert, Panels) {
    
	console.log('share module initialization...');

	var module = {

		open: function(items, options){

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

		}

	};

	return module;
});
