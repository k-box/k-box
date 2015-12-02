define("modules/starred", ["jquery", "DMS", "modules/minimalbind", "modules/panels"], function ($, DMS, _rivets, Panels) {
    
	console.log('loading starred-page module...');

	var module = {

		select: function(evt, vm){

			Panels.openAjax('select-' + this.dataset.id, this, DMS.Paths.STARRED + '/' + this.dataset.starId);

			evt.preventDefault();
			return false;
		}

	};

	_rivets.bind($('#starred-list'), module);

	return module;
});
