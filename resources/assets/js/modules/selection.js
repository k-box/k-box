/// <reference path="../../../../typings/jquery/jquery.d.ts"/>
define("modules/selection", ["jquery", "DMS", "combokeys", "lodash"], function (_$, _DMS, _combokeys, _) {
    
	/**
	 * Handle checkbox selection and all the selection stuff inside one or more list
	 */

	console.log('loading selection module...');

	var _area = undefined, // the selection area, as inserted into the options
		_keys = undefined,
		_selected = [], // the currently selected elements
		_selectedCount = 0, // the number of selected elements
		_maxSelectable = 12,
		_options = undefined, // the selection module options
		_lastSelectedItem = undefined; //the last item selected, for performing the shift selection



	function _showSelectableElements(type){

		// if(type){

		// 	_area.find('.js-selectable[data-class="'+type+'"]');

		// }
		// else {
		// 	_area.find('.js-selectable');
		// }

        

    }

    function _clearSelectableElements(){

        // _area.find('.js-selectable');

    }

    function _clearSelection(){

        var currently_selected = _area.find('.js-selectable').removeClass('item--selected');

        _lastSelectedItem = undefined;

        currently_selected.find(_options.selectionCheckbox).attr('checked', false);
		currently_selected.removeData('selected');

        _selectedCount = 0;
        _lastSelectedItem = undefined;

        _removeSavedSelection(currently_selected);

        _area.trigger('dms:selection-changed');

        // _selected = [];

        return module;

    }

    function _clearAndDestroySelection(){

    	var really_selected = _area.find('.js-selectable.item--selected');

        _clearSelection();

        really_selected.remove();

        return module;

    }

    function _invertSelection(){

    	var currently_not_selected = _area.find('.js-selectable:not(.item--selected)');

    	var currently_selected = _area.find('.item--selected');

		currently_not_selected.addClass('item--selected');

    	currently_selected.removeClass('item--selected');

    	currently_selected.find(_options.selectionCheckbox).attr('checked', false);
    	currently_not_selected.find(_options.selectionCheckbox).attr('checked', true);

        currently_selected.removeData('selected');
        currently_not_selected.data('selected', true);

        _selectedCount = currently_not_selected.length;

        _lastSelectedItem = undefined;

        _saveSelection(currently_not_selected);

        _removeSavedSelection(currently_selected);

        _area.trigger('dms:selection-changed');

        return module;

    }

    function _allSelection(){

        var selectable = _area.find('.js-selectable:not(.item--selected)').addClass('item--selected');

        if(selectable.length > 0){

	        selectable.find(_options.selectionCheckbox).attr('checked', true);
	        selectable.data('selected', true);

	        _selectedCount = _selectedCount + selectable.length;

	        _lastSelectedItem = undefined;

	        _area.trigger('dms:selection-changed');

	        _saveSelection(selectable);

	    }

	    return module;

    }

    function _shiftSelectTo(elem){

        var selectable = _area.find('.js-selectable');

        var start = selectable.index(_lastSelectedItem),
            end = selectable.index(elem.parent());
        
        var slice = selectable.slice(Math.min(start, end), Math.max(start, end) + 1);
		
		$.each(slice, function(index, el){
			
			el = $(el);
			module.select(el, true);
			
		});

        return module;
    }



    function _selectionHandler(evt){

    	var element = $(this);

    	if(!module.isSelect(element)){
	    	
			if(evt.shiftKey){
				_shiftSelectTo(element);
			}
			else {
				module.select(element);
			}
			
	    }
	    else {
	    	module.deselect(element);
	    }

    	// evt.preventDefault();
    	evt.stopPropagation();

    	// return false;
    }

    /**
     * Update the tristate checkbox when the selection status changes
     * @return {[type]} [description]
     */
    function _updateTristateStatus(){

    	if(_options.tristateButton){
			
			var chk = _options.tristateButton.find(':checkbox')[0];

			if(_selectedCount === 0){
				chk.indeterminate = false;
				chk.checked = false;
				_options.tristateButton.removeClass('tristate-button--partial');
				_options.tristateButton.removeClass('tristate-button--all');
				_options.tristateButton.addClass('tristate-button--none');
			}
			else if(_selectedCount === _maxSelectable){
				
				chk.checked = true;
				_options.tristateButton.removeClass('tristate-button--partial');
				_options.tristateButton.addClass('tristate-button--all');
				_options.tristateButton.removeClass('tristate-button--none');
			}
			else {
				chk.indeterminate = true;
				_options.tristateButton.addClass('tristate-button--partial');
				_options.tristateButton.removeClass('tristate-button--all');
				_options.tristateButton.removeClass('tristate-button--none');
			}

		}

    }

    /**
     * Handle the click on the tristate checkbox
     * @param  {[type]} evt [description]
     * @return {[type]}     [description]
     */
    function _tristateHandler(evt){

    	if(_selectedCount > 0){
    		module.clear();
    	}
    	else {
    		module.all();
    	}

    }

    /**
     * Construct a selection object for saving the selected element
     * @param {jQuery} el the .js-selectable element to store
     */
    function SelectionObject(el){

    	el = el.data ? el : $(el);

    	this.id = el.data('id');
    	this.type = el.data('type') !== 'group' ? 'document' : 'group';
    	this.raw = el.data();
    	this.title = el.find('.item__link').attr('title');
		this.share = el.data('shareid');
		this.isShareWith = !!el.data('sharewith');

//    	return this;
    }

    /**
     * Save a selected element or a list of selected elements (must be .js-selectable)
     * @param  {[type]} selection [description]
     * @return {[type]}           [description]
     */
	function _saveSelection(selection){

		if($.isArray(selection) || selection instanceof jQuery){

			$.each(selection, function(index, element){
				_selected.push(new SelectionObject(element));
				_lastSelectedItem = element;
			});

			_selected = _.uniq(_selected);

		}
		else {
			_selected.push(new SelectionObject(selection));
			_selected = _.uniq(_selected);
			_lastSelectedItem = selection;
		}

	}

	function _removeSavedSelection(selection){

		if($.isArray(selection) || selection instanceof jQuery){

			$.each(selection, function(index, element){
				var obj = new SelectionObject(element);

				_selected = $.grep(_selected, function(el){
					return obj.id === el.id && obj.type === el.type;
				}, true);
			});

		}
		else {
			var obj = new SelectionObject(selection);

			_selected = $.grep(_selected, function(el){
				return obj.id === el.id && obj.type === el.type;
			}, true);

			
		}

		_lastSelectedItem = undefined;
	}


	var module = {
		
		Types: {
			GROUP: 'group',
			DOCUMENT: 'document',	
		},

		init: function(selectionArea, options){

			_keys = new _combokeys(document);

			var defaults = {
				// tristateButton: undefined, // the button that have a tristate checkbox for control the selection
				// selectionBoundingElement, // the elements the will trigger the check and will serve as an error area
				selectionCheckbox: '.js-selection-checkbox', //the selector of the checkbox
				containerBoundingElement: '.js-selectable'
			};

			_options = options = $.extend(defaults, options);

			//bind on the selectionArea for click inside the selectionBoundingElement
			selectionArea.on('click', _options.selectionBoundingElement, _selectionHandler);

			// selectionArea.on('mouseover', '.js-selectable', function(evt){

			// 	_lastKnownMousePosition = {x: evt.pageX, y:evt.pageY};

			// });



			if(_options.tristateButton){

				_maxSelectable = $(_options.selectionBoundingElement).length;

				_options.tristateButton.on('click', _tristateHandler);
				selectionArea.on('dms:selection-changed', _updateTristateStatus);
			}

			// -- Keyboard shortcut

			_keys.bind('s a', function(evt){
				module.all();
			});

			_keys.bind('s c', function(evt){
				module.clear();
			});

			_keys.bind('s i', function(evt){
				module.inverse();
			});

			_area = selectionArea;

			return module;
		},

		/**
		 * select an element
		 * @param  {[type]} element [description]
		 * @return {[type]}         [description]
		 */
		select: function(element, short){

			var parent = short ? element : element.parents(_options.containerBoundingElement);

			// var checkbox = element.is(_options.selectionCheckbox) ? element : parent.find(_options.selectionCheckbox);

			parent.addClass('item--selected');

			// parent.data('selected', true);
			parent.data('selected', true);

			
			// checkbox[0].checked = true;

			_selectedCount++;
			
			_area.trigger('dms:selection-changed');

            _showSelectableElements();

            _saveSelection(parent);

                // _selected[selection_id] = this;
                // _lastSelectedItem = this;

			return module;
		},

		isSelect: function(element, short){

			// console.log('Is selected', element, "?", element.data('selected'));

			element = element.is(_options.containerBoundingElement) ? element : element.parents(_options.containerBoundingElement);

			return element.data('selected') && element.data('selected') == true;
		},

		/**
		 * Deselect a single element
		 * @param  {[type]} element [description]
		 * @return {[type]}         [description]
		 */
		deselect: function(element, short){

			var parent = element.is(_options.containerBoundingElement) ? element : element.parents(_options.containerBoundingElement);
			
			parent.removeClass('item--selected');

			// parent.data('selected', true);
			parent.removeData('selected');

			// parent.find(_options.selectionCheckbox).first()[0].checked = false;

			_selectedCount--;

			_removeSavedSelection(parent);

			if(_selectedCount==0){
				_clearSelectableElements();
			}
			
			_area.trigger('dms:selection-changed');

			return module;
		},

		/**
		 * Select all elements
		 * @return {[type]} [description]
		 */
		all: _allSelection,

		/**
		 * Perform an inverse selection
		 * @return {[type]} [description]
		 */
		inverse: _invertSelection,

		/**
		 * clear the current selection
		 * @return {[type]} [description]
		 */
		clear: _clearSelection,

		/**
		 * Clear the selection and remove the corresponding DOMElements 
		 */
		clearAndDestroy: _clearAndDestroySelection,

		/**
		 * Select by type
		 * @param  {[type]} type [description]
		 * @return {[type]}      [description]
		 */
		selectionByType: function(selectedType, property){
			var filtered = _.filter(_selected, {'type' : selectedType});
			
			if(property){
				return _.map(filtered, property);
			}
			
			return filtered;
		},

		/**
		 * Get the currently selected elements
		 * @return {[type]} [description]
		 */
		selection: function(property){
			
			if(property){
				return _.map(_selected, property);
			}
			
			return _selected;
		},

		selectionCount: function(){
			return _selectedCount;
		},
		
		first: function(){
			return _.first(_selected);
		},

		/**
		 * Tells if something is selected
		 * @return {Boolean} [description]
		 */
		isAnySelected: function(){
			return _selectedCount > 0;
		}

	};

	return module;
});
