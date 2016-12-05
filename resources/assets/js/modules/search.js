define("modules/search", ["jquery", "DMS", "modules/minimalbind", "modules/panels"], function ($, DMS, _rivets, Panels) {

	var _resultList = $('#documents-list');

	var module = {

		select: function(evt, vm){

			var that = $(this);

			Panels.openAjax('select-' + that.data('inst')+ '-' + that.data('doc'), that, DMS.Paths.DOCUMENTS + '/' + that.data('inst')+ '/' + that.data('doc'));

			evt.preventDefault();
			return false;
		}

	};

	_rivets.bind(_resultList, module);
	
	return module;
});
