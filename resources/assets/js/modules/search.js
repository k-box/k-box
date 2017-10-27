define("modules/search", ["jquery", "DMS", "modules/minimalbind", "modules/panels"], function ($, DMS, _rivets, Panels) {

	var _resultList = $('#documents-list');

	var module = {

		select: function(evt, vm){

			var that = $(this);
			var uuid = that.data('uuid');

			Panels.openAjax('select-' + uuid, that, DMS.Paths.DOCUMENTS + '/public/' + uuid);

			evt.preventDefault();
			return false;
		}

	};

	_rivets.bind(_resultList, module);
	
	return module;
});
