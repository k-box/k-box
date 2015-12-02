define("modules/list-switcher", ["jquery", "DMS"], function ($, DMS) {
    
	console.log('List Switcher initialization...');

	var _switcher_container = $("#list-switcher"), 
		_destination_list = $(_switcher_container.data('list')),
		_current = 'cards';


	function _save(){
		// if(window.localStorage && window.localStorage['dms_list_style']){
		// 	window.localStorage.setItem('dms_list_style', _current);
		// }

		DMS.Services.Options.saveListStyle(_current);
	}


	// if(window.localStorage && window.localStorage['dms_list_style']){
	// 	_current = window.localStorage['dms_list_style'];
	// 	_destination_list.attr('class', 'list');
	// 	_destination_list.addClass(_current);
	// }
	// else {
		_current = _switcher_container.data('current').trim();
	// 	_save();
	// }

	// Tiles, cards, details

	var _current_btn = _switcher_container.find("[data-list='"+_current+"']");

	_current_btn.addClass('current');

	var module = {

		tiles: function(element, evt){
			
			if(_current=='map'){
				_destination_list.parent().trigger('dms:unloadmap');
			}
			
			if(_current=='details'){
				$('#document-area .thumbnail img').trigger('unveil');
			}

			_destination_list.removeClass(_current);
			_current = 'tiles';
			_destination_list.addClass(_current);

			_current_btn.removeClass('current');
			_current_btn = element;
			_current_btn.addClass('current');

			_save();

			evt.preventDefault();
			return false;
		},

		cards: function(element, evt){
			
			if(_current=='map'){
				_destination_list.parent().trigger('dms:unloadmap');
			}
			
			if(_current=='details'){
				$('#document-area .thumbnail img').trigger('unveil');
			}

			_destination_list.removeClass(_current);
			_current = 'cards';
			_destination_list.addClass(_current);

			_current_btn.removeClass('current');
			_current_btn = element;
			_current_btn.addClass('current');

			_save();
			
			

			evt.preventDefault();
			return false;
		},

		details: function(element, evt){

			if(_current=='map'){
				_destination_list.parent().trigger('dms:unloadmap');
			}

			_destination_list.removeClass(_current);
			_current = 'details';
			_destination_list.addClass(_current);

			_current_btn.removeClass('current');
			_current_btn = element;
			_current_btn.addClass('current');

			_save();

			evt.preventDefault();
			return false;
		},
		
		map: function(element, evt) {
			
			_destination_list.removeClass(_current);
			_current = 'map';
			_destination_list.addClass(_current);
			
			_current_btn.removeClass('current');
			_current_btn = element;
			_current_btn.addClass('current');
			
			_destination_list.parent().trigger('dms:loadmap');

			//_save();

			evt.preventDefault();
			return false;
		}

	};

	_switcher_container.on('click', '.switch', function(evt){

		var that = $(this);

		module[that.data('list')].call(module, that, evt);

		evt.preventDefault();
		return false;
	});

	return module;
});
