define("modules/list-switcher", ["jquery", "DMS"], function ($, DMS) {
    
	console.log('List Switcher initialization...');

	var _switcher_container = $("#list-switcher"), 
		_destination_list = $(_switcher_container.data('list')),
		_current = 'cards';


	function _save(){
		DMS.Services.Options.saveListStyle(_current);
	}

		_current = _switcher_container.data('current').trim();

	// Tiles, cards, details

	var _current_btn = _switcher_container.find("[data-list='"+_current+"']");

	_current_btn.addClass('button--selected');

	var module = {

		tiles: function(element, evt){
			
			if(_current=='details'){
				$('#document-area .item__thumbnail img').trigger('unveil');
			}

			_destination_list.removeClass(_current);
			_current = 'tiles';
			_destination_list.addClass(_current);

			_current_btn.removeClass('button--selected');
			_current_btn = element;
			_current_btn.addClass('button--selected');

			_save();

			evt.preventDefault();
			return false;
		},

		cards: function(element, evt){
			
			if(_current=='details'){
				$('#document-area .item__thumbnail img').trigger('unveil');
			}

			_destination_list.removeClass(_current);
			_current = 'cards';
			_destination_list.addClass(_current);

			_current_btn.removeClass('button--selected');
			_current_btn = element;
			_current_btn.addClass('button--selected');

			_save();
			
			

			evt.preventDefault();
			return false;
		},

		details: function(element, evt){

			_destination_list.removeClass(_current);
			_current = 'details';
			_destination_list.addClass(_current);

			_current_btn.removeClass('button--selected');
			_current_btn = element;
			_current_btn.addClass('button--selected');

			_save();

			evt.preventDefault();
			return false;
		}

	};

	_switcher_container.on('click', '.button', function(evt){

		var that = $(this);

		module[that.data('list')].call(module, that, evt);

		evt.preventDefault();
		return false;
	});

	return module;
});
