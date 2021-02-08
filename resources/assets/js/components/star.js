/* define("modules/star", ["jquery", "DMS", "sweetalert", "language"], function (_$, _DMS, _alert, Lang) {
    
	console.log('Loading star micro-module...');

	

	var module = {

		toggle: function(star_element, evt){

			var that = star_element instanceof jQuery ? star_element : _$(star_element);
			
			that.addClass('item__star--starring');
			// if local document
			if(that.data('id')){
				// if starID given, remove star
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
				// if the document is public:
				var doc = that.data('doc');
				var visibility = that.data('visibility') ? that.data('visibility') : 'public';

				that.addClass('item__star--starring');
				//add star to the file
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
					

				}, 
				
				function(jqXHR, textStatus, errorThrown){
					
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
}); */

//function exposed to star-button.blade.php through evolution-components.js
export default function(data) {

    return {
        error: false,
        starred: data.starID ? true : false, 
		starID: data.starID || null,
		inProgress: false,
		documentID: data.documentID,
        
        star () {
			//if starred: remove star (and set starred to false), else add it and set starred to true
			if(this.starred) {
				this.inProgress = true; //starring in progress
				DMS.Services.Starred.remove(this.starID, function() {
					this.starred = false;					//set starred to false
					this.starID = null;					//set starID to null
					
					setTimeout(function(){ 
						this.inProgress = false; //starring done
					}, 1000);
				}, 
				function(jqXHR, textStatus, errorThrown){
					this.error = true;
					console.error('error on starred-remove response', textStatus, errorThrown);
				});
				
			}else { //add star to the file
				this.inProgress = true; //starring in progress
				DMS.Services.Starred.add({descriptor: this.documentID, visibility:'private'}, function(data){
					
					this.starred = true;				//set starred to true
					if(data.id) { //if starred successful, assign starID to the value
						this.starID = data.id;		//set starID	
						
						setTimeout(function(){
							this.inProgress = false; //starring done
						}, 1000);
					}
				}, function(jqXHR, textStatus, errorThrown){
					this.error = true;
					console.error('error on starred-add response', textStatus, errorThrown);
				});
			}
        },
        
    }
}
// console.log('Module loaded');