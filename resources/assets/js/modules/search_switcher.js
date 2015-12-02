define("modules/search_switcher", ["jquery", "DMS"], function ($, DMS) {
    
	console.log('search switcher module initialization');

	var search_form = $('#main-search'),
		text_input = search_form.find(".search-field");
		//switcher = $('#visibility-switcher');

	var visibilityInput = search_form.find("input[name='visibility']"), 
		startVisibility = visibilityInput.val();
	
	
	function changeVisibility(value){
		visibilityInput.val(value);
	}

	var module = {

		currentVisibility: startVisibility,

		public: function(evt, vm){
			
			module.currentVisibility = 'public';

			changeVisibility(module.currentVisibility);

			search_form.submit();

			evt.preventDefault();
		},

		private: function(evt, vm){

			module.currentVisibility = 'private';

			changeVisibility(module.currentVisibility);

			search_form.submit();

			evt.preventDefault();
		},

		isPublic: function(vm){
			return module.currentVisibility=='public';
		},

		isPrivate: function(vm){
			return module.currentVisibility=='private';	
		},

		getCurrentVisibility: function(){
			return module.currentVisibility;
		}

	};

	// var rivets_bind = _rivets.bind(search_form, module);

	search_form.on('click', 'a', function(evt){

		search_form.find('[data-bind="'+module.getCurrentVisibility()+'"]').removeClass("current");

		var that = $(this);

		module[that.data('bind')].call(module, evt, module);

		that.addClass("current");

		return false;
	});

	return module;
});
