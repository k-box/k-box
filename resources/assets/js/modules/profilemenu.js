define("modules/profilemenu", ["jquery", "DMS"], function (_$, _DMS,) {

	var dialog = null;
	var arrow = null;

	var module = {

		toggle: function(element, evt){

			if(!dialog){
				dialog = _$(".js-profile");
			}
			if(!arrow){
				arrow = _$(".js-profile-arrow");
			}

			if(dialog.hasClass('hidden')){

				dialog.removeClass('hidden');
				arrow.addClass("rotate-180");
			}
			else {
				dialog.addClass('hidden');
				arrow.removeClass("rotate-180");
			}

			return false;
		}

	};


	// find all the star action on the page and then to the best :)
	
	_$('.js-profile-link').on('click', function(evt){

		evt.preventDefault();
		evt.stopPropagation();

		return module.toggle(".js-profile");

	});


	return module;
});