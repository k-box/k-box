define("modules/search", ["jquery", "DMS", "modules/minimalbind", "modules/panels", 'elasticlist', "modules/search_switcher"], function ($, DMS, _rivets, Panels, Elastic, Switcher) {
    
	console.log('search module initialization');

	var _current_search_terms = '',
		_current_visibility = 'private',
		_current_facets = undefined,
		_resultList = $('#documents-list');

	function _elasticListCallback(){

		var values = this.selection();

//		var fs = [], realvalues = {};
//
//		$.each(values, function(key, value){
//
//			if(value.length > 0){
//
//				fs.push(key);
//
//	      		realvalues[key] = values[key].join();
//	      	}
//
//		});
//
//
//      	if(fs.length>0){
//	      	realvalues.fs = fs.join();
//	    }

		var realvalues = _facetsToParameters(values);

      	var getParams = $.extend({s: _current_search_terms, visibility:_current_visibility}, realvalues);

      	_resultList.addClass('loading');

		DMS.navigate(DMS.Paths.SEARCH, getParams);

		return true;
	}
	
	function _facetsToParameters(values){
		var fs = [], realvalues = {};
		
		if(!values){
			return {};
		}

		$.each(values, function(key, value){

			if(value.length > 0){

				fs.push(key);

	      		realvalues[key] = values[key].join();
	      	}

		});


      	if(fs.length>0){
	      	realvalues.fs = fs.join();
	    }
		
		return realvalues;
	}


	var module = {

		updateElasticList: function(container, query, visibility, _data, current_filters, facets, translations){

			_current_search_terms = query;
			_current_visibility = visibility;
			_current_facets = _facetsToParameters(current_filters);

//			console.log("Calling elastic list construction...", container, current_filters);

			Elastic.create(container, {
                data: _data,
                columns : facets,
                selected: current_filters,
                callbacks : {
                	filterAdded: _elasticListCallback, 
					filterRemoved: _elasticListCallback, 
                }
            });
			   
		},

		select: function(evt, vm){

			var that = $(this);

			Panels.openAjax('select-' + that.data('inst')+ '-' + that.data('doc'), that, DMS.Paths.DOCUMENTS + '/' + that.data('inst')+ '/' + that.data('doc'));

			evt.preventDefault();
			return false;
		}

		// star: function(evt, vm){
		// 	//this ha l'oggetto su cui è stato fatto click
		// 	// console.log(this);

		// 	var that = $(this);

		// 	if(that.data('id')){
		// 		// console.log('remove data');


		// 		DMS.Ajax.del(DMS.Paths.STARRED + '/' + that.data('id'), function(data){
					
		// 			that.removeData('id');
		// 			that.removeAttr('data-id');
		// 			that.toggleClass('active');

		// 		}, function(){
		// 			debugger;
		// 			console.error('error on starred-delete response');
		// 		})

				
		// 	}
		// 	else {

		// 		var inst = that.data('inst');
		// 		var doc = that.data('doc');

		// 		DMS.Ajax.post(DMS.Paths.STARRED, {institution: inst, descriptor: doc, visibility:'public'}, function(data){
					
		// 			if(data.id){
		// 				that.attr('data-id', data.id);
		// 				that.toggleClass('active');
		// 			}
		// 			else{
		// 				console.error('Invalid Starred-add response', data);
		// 			}
					

		// 		}, function(){
		// 			debugger;
		// 			console.error('error on starred-add response');
		// 		})

				
		// 	}

		// 	evt.preventDefault();
		// }
	


	};

	_rivets.bind(_resultList, module);
	
	var map = undefined, 
	    _mapInstance = undefined,
		_mapTemplateBinding = undefined,
		_map_vm = {
			map: {
				elements : []
			},
			
			mapListClick: function(evt, vm){
				console.log("Map List Click", this, evt, vm);

				if(evt.target.nodeName === 'A'){
					var that = $(evt.target);

					Panels.openAjax('select-' + that.data('inst')+ '-' + that.data('doc'), that, DMS.Paths.DOCUMENTS + '/' + that.data('inst')+ '/' + that.data('doc'));
					
					evt.preventDefault();
					evt.stopPropagation();
					return false;	
				}				
				
				
			}
		};
		
	_mapTemplateBinding = _rivets.bind( $("#map"), _map_vm);
	
	
	/** Used for getting something from the map callbacks */
    function callback_filter(filter_values)
    { 
	  _map_vm.map.elements = filter_values;
	  
	  _mapTemplateBinding.sync();
    }
	
	function _initializeMap(_map){
		map = _map;
	
		
			
		_updateMapData();
	}
	
	
	function _updateMapData(){
		if(_mapInstance){
			map.remove();
		}
		
		$("#map").addClass('visible');
		
		var getParams = $.extend({s: _current_search_terms, visibility:_current_visibility}, _current_facets);
		
		DMS.Services.Documents.visualizationSearch(getParams, function (data) {
			_mapInstance = map.create(data, 'map-area', callback_filter, callback_filter);
		  
		  	if(map.getLocationsCount() > 0){
				  DMS.MessageBox.close();
			}
			else {
				DMS.MessageBox.show('No Locations found', 'Seems that the documents doesn\'t contain any location information. The map is empty.');
			}
		  		
		}, function(obj, err, errText){
			DMS.MessageBox.error('Map loading error', 'Unfortunately the map visualization cannot be loaded.');
		});
		
//		$.getJSON("http://localhost/dms/visualizationdata?s="+_current_search_terms+"&visibility=" + _current_visibility, function(data) {
//	      
//	    })
//	    .fail(function() {
//	      
//	    });
	}
	
	_resultList.on('dms:unloadmap', function(evt){
		
		
		//TODO: destroy the map and the handlers
		if(_mapInstance){
			map.remove();
			map = undefined;
			$("#map").removeClass('visible');
		}
	});
	
	_resultList.on('dms:loadmap', function(evt){
		
		DMS.MessageBox.wait('Loading map', 'standby, I\'m loading the map visualization...');
		
		if(!map){
			
			
			
			require(['map'], function(_map){
				console.log('Map is required', _map);
				
				_initializeMap(_map);
			});
		}
		else {
			_updateMapData();
		}
		
		
		
		
	});


	return module;
});

// console.log('Module loaded');