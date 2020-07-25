define("modules/star", ["jquery", "DMS", "sweetalert", "language"], function (_$, _DMS, _alert, Lang) {
    
	console.log('Loading star micro-module...');

	

	var module = {

		toggle: function(star_element, evt){

			var that = star_element instanceof jQuery ? star_element : _$(star_element);

			that.addClass('item__star--starring');

			if(that.data('id')){

				_DMS.Services.Starred.remove(that.data('id'), function(data){
					
					that.removeData('id');
					that.removeAttr('data-id');
					that.toggleClass('item__star--starred');

					setTimeout(function(){
							that.removeClass('item__star--starring');
						}, 1000);

					// do some animation to show the user that something has happened correctly

				}, function(jqXHR, textStatus, errorThrown){
					console.error('error on starred-delete response', textStatus, errorThrown);

					_DMS.MessageBox.error( Lang.trans('errors.generic_title'), Lang.trans('errors.generic_text_alt', {error: errorThrown}));
				})

				
			}
			else {

				var doc = that.data('doc');
				var visibility = that.data('visibility') ? that.data('visibility') : 'public';

				that.addClass('item__star--starring');

				_DMS.Services.Starred.add({descriptor: doc, visibility:visibility}, function(data){
					
					if(data.id){
						that.attr('data-id', data.id);
						that.toggleClass('item__star--starred');
						
						setTimeout(function(){
							that.removeClass('item__star--starring');
						}, 1000);
					}
					else if(data.status){
						//already exists
					}
					else{
						console.error('Invalid Starred-add response', data);

						_DMS.MessageBox.error( Lang.trans('errors.generic_title'), Lang.trans('errors.generic_text'));
					}
					

				}, function(jqXHR, textStatus, errorThrown){
					
					console.error('error on starred-add response', textStatus, errorThrown);

					_DMS.MessageBox.error( Lang.trans('errors.generic_title'), Lang.trans('errors.generic_text_alt', {error: errorThrown}));
				})

			}

			return false;
		}

	};


	// find all the star action on the page and then to the best :)
	
	_$('[data-action="star"]').on('click', function(evt){

		evt.preventDefault();
		evt.stopPropagation();

		return module.toggle(this);

	});


	return module;
});

// console.log('Module loaded');