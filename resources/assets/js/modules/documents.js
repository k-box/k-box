define("modules/documents", ["require", "modernizr", "jquery", "DMS", "modules/star", "sweetalert", "modules/panels", "combokeys", 
"modules/selection", "modules/minimalbind", "context", "lodash", 'language', "modules/share" ], function (_require, _modernizr, $, DMS, Star, _alert, Panels, _combokeys, _Selection, _rivets, _context, _, Lang, Share) {
    
	console.log('loading documents-page module...');

    var CONTEXT_GROUP = 'group';



    //TODO: prevent page change when uploading

	var _selected = [], //key ID value object info
        _isSelecting = false,
        _lastSelectedItem = undefined,
        _pageArea = $("#page"),
        _documentArea = $("#document-area"),
        _filtersArea = $("#filters-area"),
//        _resultList = $("#documents-list .list"),
        _actionBar = $("#action-bar"),
        _treeView = $("#document-tree"),
        _bindActionBar = undefined,
        _bindPageArea = undefined;


    ////////////////////////////
    // For multiple selection //
    ////////////////////////////

    _Selection.init(_documentArea, {
        tristateButton: _actionBar.find('.js-document-selection-button'),
        selectionBoundingElement: '.js-select-button',
        selectionCheckbox: '.js-selection-checkbox'
    });

    ///////////////////////
    // For drag and drop //
    ///////////////////////

    var dragItems = $('[draggable=true]'),
        droppables = $('[data-drop=true]'),
        dragOfInternalElement = false;

    // http://caniuse.com/#feat=dragndrop
    //
    // IE9 and 10 (I suspect also IE8)
    // http://stackoverflow.com/questions/5500615/internet-explorer-9-drag-and-drop-dnd
    //
    // I've found a workarround to make the native dnd api also work in IE with elements
    // other than links and images. Add a onmousemove handler to the draggable container 
    // and call the native IE function element.dragDrop(), when the button is pressed:

    // function handleDragMouseMove(e) {
    //     var target = e.target;
    //     if (window.event.button === 1) {
    //         target.dragDrop();
    //     }
    // }

    // var container = document.getElementById('widget');
    // if (container.dragDrop) {
    //     $(container).bind('mousemove', handleDragMouseMove);
    // }


    droppables.on('dragover', function(evt){

        // console.log('dragover', this);
        var that = $(this);
        that.addClass("dragover");

        if (evt.preventDefault) {
            evt.preventDefault(); 
        }

        evt.originalEvent.dataTransfer.dropEffect = that.data('groupId') ? 'copy' : 'link'; //over the trash could be a move

        return false;

    });

    droppables.on('dragenter', function(evt){

        // the only reason is to block the event

        if (evt.preventDefault) {
            evt.preventDefault(); 
        }
        
        return false;

    });

    droppables.on('dragleave', function(evt){

        $(this).removeClass("dragover");

        if (evt.preventDefault) {
            evt.preventDefault(); 
        }

        return false;

    });

    droppables.on('drop', function(evt){

        var $this = $(this);

        $this.removeClass("dragover");

        // stops the browser from redirecting off to the text.
        if (evt.preventDefault) {
            evt.preventDefault(); 
        }

        var dragText = evt.originalEvent.dataTransfer.getData('text');
        evt.originalEvent.dataTransfer.dropEffect = dragText !== 'dms_drag_collection' ? 'copy' : 'none';

        var files = evt.originalEvent.dataTransfer.files;
        
        // If the dragText is not empty, this means that the file was not
        // dragged in from outside the browser, therefore it is possibly a
        // document or collection.
        if (dragText !== undefined && dragText.length == 0 && files.length > 0) {
            return;
        }
        
        if(dragText!=='dms_drag_action'){
            try{
                dragText = JSON.parse(dragText);
            }catch(e){
                //deserialization error, now is not a problem, maybe in future use only JSON for DMS actions
            }
        }
        
        if(dragText.toString() !== "[object Object]" && /^https?:\/\/.*$/.test(dragText)){
            
            console.info('URL dropped', dragText, /^https?:\/\/.*$/.test(dragText));
            
            DMS.MessageBox.error( Lang.trans('errors.dragdrop.link_not_permitted_title'), Lang.trans('errors.dragdrop.link_not_permitted_text'));
            
            return false;
        }

        var dropAction = $this.data('dropAction');

        if(dropAction){
            module['menu'][dropAction].call(module, evt, $this.data(), dragText);
        }
        
        return false;
    });

    dragItems.on('dragstart', function(evt){

        evt.originalEvent.dataTransfer.effectAllowed = 'all';

        var $this = $(this),
            _data = $this.data(); 

        if(!_Selection.isAnySelected() && !_data.dragEl){
            evt.originalEvent.dataTransfer.setData('text', 'dms_drag_action');
            _Selection.select($(this), true);
            dragOfInternalElement = true;
        }
        else if(_data.dragEl && _data.dragEl === 'group'){
            // drag a collection from the tree view
            evt.originalEvent.dataTransfer.setData('text', JSON.stringify(_data));
            dragOfInternalElement = true;
        }
        else {
            evt.originalEvent.dataTransfer.setData('text', 'dms_drag_action');
            dragOfInternalElement = true;
        }
    });

    dragItems.on('dragend', function(evt){

        if(_Selection.selectionCount() == 1){
            _Selection.deselect($(this), true);
        }

        dragOfInternalElement = false;
    });



    function _countKeys(obj){
        return $.map(obj, function(n, i) { return i; }).length;
    }

    function _inArray(arr, what){
        return (Array.prototype.indexOf) ? arr.indexOf(what) : $.inArray(what, arr);
    }


    function _bulkCopyTo(evt, vm){

        if(_Selection.isAnySelected()){

                    var groups = [],
                        documents = [];

                    var count = _Selection.selectionCount();
                    var currentSelection = _Selection.selection();


                    var selectedGroup = vm.groupId;

                    $.each(currentSelection, function(index, sel){

                        if(sel.type === 'group'){
                            groups.push(sel.id);
                        }
                        else{
                            documents.push(sel.id);
                        }

                    });
                    
                    DMS.MessageBox.wait( Lang.trans('documents.bulk.adding_title'), Lang.trans('documents.bulk.adding_message') );

                    DMS.Services.Bulk.copyTo({
                        documents: documents, 
                        // groups:groups, 
                        context:module.context.filter, 
                        current_group: module.context.group ? module.context.group : null,
                        destination_group:selectedGroup}, function(data){

                        if(data.status && data.status === 'ok'){

                            DMS.MessageBox.success( data.title , data.message);

                        }
                        else if(data.status && data.status === 'partial'){

                            DMS.MessageBox.warning( data.title, data.message);

                        }
                        else if(data.message) {
                            DMS.MessageBox.error( Lang.trans('documents.bulk.add_to_error'), data.message);
                        }

                    }, function(obj, err, errText){

                        if(obj.responseJSON && obj.responseJSON.status === 'error'){
                            DMS.MessageBox.error( Lang.trans('documents.bulk.add_to_error'), obj.responseJSON.message);
                        }
                        else if(obj.responseJSON && obj.responseJSON.error){
                            DMS.MessageBox.error( Lang.trans('documents.bulk.add_to_error'), responseJSON.error);
                        }
                        else {
                            DMS.MessageBox.error( Lang.trans('documents.bulk.add_to_error'), errText);
                        }

                    });

                }
                else{
                    DMS.MessageBox.error( Lang.trans('actions.selection.at_least_one_document'), '');
                }   


    }
    
    /**
     * Move source under target
     */
    function _collectionMove(source, target){
        
        var updatedata = { 
            parent: target.groupId,
            dry_run: 0,
            action:'move'};
            
        if(source.isprivate && !target.isprivate){
            updatedata.public = 1; //true
        }
        else if(!source.isprivate && target.isprivate){
            updatedata.private = 1; //true
        }
        
        DMS.Services.Groups.update(source.groupId, updatedata, function(data){

            if(data.id){

                DMS.MessageBox.wait(Lang.trans('groups.move.moved_alt'), Lang.trans('groups.move.moved_text'));
                DMS.navigateReload();

            }
            else if(data.message) {
                DMS.MessageBox.error(Lang.trans('groups.move.error_title_alt'), data.message);
            }

        }, function(obj, err, errText){

            if(obj.responseJSON && obj.responseJSON.status === 'error'){
                DMS.MessageBox.error(Lang.trans('groups.move.error_title_alt'), obj.responseJSON.message);
            }
            else if(obj.responseJSON && obj.responseJSON.error){
                DMS.MessageBox.error(Lang.trans('groups.move.error_title_alt'), obj.responseJSON.error);
            }
            else {
                DMS.MessageBox.error(Lang.trans('groups.move.error_title_alt'), Lang.trans('groups.move.error_text_generic'));
            }

        });
    }

    function _panelClickEventHandler(evt, data){

        // 1. only document panel
        // 2. only click on data-action elements
        
        console.warn('_panelClickEventHandler', this, evt, data);

        var panel = this;

        if(data.action && (data.action === 'openShareDialog' || data.action === 'openShareDialogWithAccess') ){
            evt.preventDefault();
            
            Share.open([{
                id: data.id,
                type: data.group ? 'group' : 'document',
                title: data.title
            }]);
        }
        else if(data.action && data.action === 'removeGroup'){

            DMS.Services.Documents.update(data.documentId, {remove_group: data.groupId}, function(data_back){

                if(data_back.id){

                    $(panel).find('.badge[data-group-id='+data.groupId+']').hide();

                }
                else {
                    DMS.MessageBox.error( Lang.trans('documents.update.cannot_remove_from_title'), data_back);
                }

            }, function(obj, err, errorText){

                if(obj.status === 422){

                    var html = '';

                    $.each(obj.responseJSON, function(index, el){

                        html += $.isArray(el) ? el[0]: el;

                    });

                    DMS.MessageBox.error(Lang.trans('documents.update.cannot_remove_from_title'), html);


                }
                else if(obj.responseJSON && obj.responseJSON.status === 'error'){
                    DMS.MessageBox.error( Lang.trans('documents.update.cannot_remove_from_title'), obj.responseJSON.message);
                }
                else if(obj.responseJSON && obj.responseJSON.error){
                    DMS.MessageBox.error( Lang.trans('documents.update.cannot_remove_from_title'), obj.responseJSON.error);
                }
                else {
                    DMS.MessageBox.error(Lang.trans('documents.update.cannot_remove_from_title'), Lang.trans('documents.update.cannot_remove_from_general_error'));
                }

            });

        }
        else if(data.action && data.action === 'restore'){

                DMS.MessageBox.question(
                    Lang.trans('documents.restore.restore_dialog_title', { document: data.title}), 
                    Lang.trans('documents.restore.restore_dialog_text', { document: data.title}), 
                    Lang.trans('documents.restore.restore_dialog_yes_btn'), 
                    Lang.trans('documents.restore.restore_dialog_no_btn'), function(isConfirmed){

                    if(isConfirmed){

                        DMS.MessageBox.wait(Lang.trans('documents.restore.restoring'), '...');


                        DMS.Services.Bulk.restore({documents: [data.id], context:'trash'}, function(resdata){

                            if(resdata.status && resdata.status === 'ok'){

                                DMS.MessageBox.success( Lang.trans('documents.restore.restore_success_title'), resdata.message);

                                // Reload panel
                                Panels.openAjax('document'+data.id, this, DMS.Paths.DOCUMENTS + '/' + data.id, {}, {
                                    callbacks: {
                                        click: _panelClickEventHandler
                                    }
                                });

                            }
                            else if(resdata.message) {
                                DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), resdata.message);
                            }
                            
                        }, function(obj, err, errText){

                            if(obj.status === 422){
            
                                var html = '';
            
                                $.each(obj.responseJSON, function(index, el){
            
                                    html += $.isArray(el) ? el[0]: el;
            
                                });
            
                                DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), html);
            
            
                            }
                            else if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), obj.responseJSON.message);
                            }
                            else {
                                DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), Lang.trans('documents.restore.restore_error_text_generic'));
                            }

                        });

                    }
                    else {
                        DMS.MessageBox.close();
                    }

                });
            
            
        }
        else if(data.action && data.action==='micrositeDelete'){
            DMS.MessageBox.deleteQuestion(data.ask, '').then(function(){

                DMS.MessageBox.wait( Lang.trans('actions.deleting'), '...');

                DMS.Services.Microsite.delete(data.microsite, function(resdata){

                        if(resdata.status && resdata.status === 'ok'){

                            DMS.MessageBox.close();

                            // Reload panel
                            var url_path = data.project;
                            var panelUrl = DMS.Paths.PROJECTS + '/' + url_path;
                            
                            var pnl = Panels.openAjax('project' + data.project, this, panelUrl, {}, {
                                callbacks: {
                                    click: _panelClickEventHandler
                                }
                            }).on('dms:panel-loaded', function(panel_evt, panel){

                                var h = new holmes({
                                    input: '.js-search-user',
                                    find: '.userlist .userlist__user',
                                    placeholder: Lang.trans('projects.labels.search_member_not_found'),
                                    mark: true,
                                    class: {
                                        visible: 'visible',
                                        hidden: 'hidden'
                                    }
                                });

                                h.start();

                            });

                        }
                        else if(resdata.error) {
                            DMS.MessageBox.error(resdata.error);
                        }
                            
                    }, function(obj, err, errText){

                        if(obj.status === 422){
        
                            var html = '';
        
                            $.each(obj.responseJSON, function(index, el){
        
                                html += $.isArray(el) ? el[0]: el;
        
                            });
        
                            DMS.MessageBox.error('', html);
        
        
                        }
                        else if(obj.responseJSON && obj.responseJSON.status === 'error'){
                            DMS.MessageBox.error('', obj.responseJSON.message);
                        }

                    });

            }, function(){
                // dismiss
                DMS.MessageBox.close();
            });
        }
        else if(data.action && data.action==='showCopyrightUsageDescription'){

            handleLicenseDetailsShowHide();

            
        }
 
    }

    function handleLicenseDetailsShowHide(innerEvent)
    {
        var licenseDetails = $('.js-license-details');
        var close = false;

        if(innerEvent){

            if($(innerEvent.target).parents('.js-license-details').length === 0){

                innerEvent.stopPropagation();

                close = true;

                $(document).off('click', ':not(.js-license-details)', handleLicenseDetailsShowHide);
            }
        }

        if(licenseDetails.hasClass('license__details--opened') && close){
            licenseDetails.removeClass('license__details--opened');
        }
        else {
            licenseDetails.addClass('license__details--opened');

            $(document).on('click', ':not(.js-license-details)', handleLicenseDetailsShowHide);
            
        }

    }
    
    function _doMakePublic(params, changeTitles){
        
        if(changeTitles){
            
            DMS.MessageBox.warning(Lang.trans('actions.not_available'), Lang.trans('networks.make_public_change_title_not_available'));
        }
        else {
            
            DMS.MessageBox.wait(Lang.trans('networks.making_public_title', {network: module.context.network_name}), Lang.trans('networks.making_public_title', {network: module.context.network_name}));

            DMS.Services.Bulk.makePublic(params, function(data){
                
                if(data.status && data.status==='ok'){
                                            
                    DMS.MessageBox.success(Lang.trans('networks.make_public_success_title'), (data && data.message) ? data.message : Lang.trans('networks.make_public_success_text_alt', {network: module.context.network_name}));
                    
                    DMS.navigateReload();
                }
                else if(data.message) {

                    DMS.MessageBox.error(Lang.trans('networks.make_public_error_title',{network: module.context.network_name}), data.message);

                }

            }, function(obj, err, errText){
                
                if(obj.responseJSON && obj.responseJSON.status === 'error'){
                    DMS.MessageBox.error(Lang.trans('networks.make_public_error_title',{network: module.context.network_name}), obj.responseJSON.message);
                }
                else if(obj.responseJSON && obj.responseJSON.error){
                    DMS.MessageBox.error(Lang.trans('networks.make_public_error_title',{network: module.context.network_name}), obj.responseJSON.error);
                }
                else if(obj.status == 422){
                    DMS.MessageBox.error(Lang.trans('networks.make_public_error_title',{network: module.context.network_name}), Lang.trans('make_public_error', {error: (obj.responseText ? obj.responseText : errText) } ));
                }
                else {
                    DMS.MessageBox.error(Lang.trans('networks.make_public_error_title',{network: module.context.network_name}), Lang.trans('make_public_error', {error: errText} ));
                }
                
            });
            
            
            
        }
        
        
        
        



    }

    function _handleSuccessPermanentDeleteResponse(data){

        if(data.status && data.status === 'ok'){

            DMS.MessageBox.success( Lang.trans('documents.permanent_delete.deleted_dialog_title_alt'), data.message);

            _Selection.clearAndDestroy();

        }
        else if(data.message) {
            DMS.MessageBox.error(Lang.trans('documents.permanent_delete.cannot_delete_dialog_title_alt'), data.message);
        }

    }

    function _handleFailedPermanentDeleteResponse(obj, err, errText){

        if(obj.responseJSON && obj.responseJSON.status === 'error'){
            DMS.MessageBox.error(Lang.trans('documents.permanent_delete.cannot_delete_dialog_title_alt'), obj.responseJSON.message);
        }
        else if(obj.responseJSON && obj.responseJSON.error){
            DMS.MessageBox.error(Lang.trans('documents.permanent_delete.cannot_delete_dialog_title_alt'), obj.responseJSON.error);
        }
        else {
            DMS.MessageBox.error(Lang.trans('documents.permanent_delete.cannot_delete_dialog_title_alt'), Lang.trans('documents.permanent_delete.cannot_delete_general_error'));
        }

    }

    function getParameterByName(name, url) {
        if (!url) url = window.location.search || window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }


	var module = {



		openProject: function(evt, vm){

            var link = $(this).find('.item__link');
                
            if(link){
                DMS.navigate(link.attr('href'), null, true);
            }
        },
        getProjectDetails:function(evt, vm){
            evt.preventDefault();
            evt.stopPropagation();
    
            if(_Selection.selectionCount() > 1){

                DMS.MessageBox.error('Multiple Selection', 'The details view currently don\'t support multiple selection');
                return false;
            }

            var $this = $(this).parents('.item--project');

            module.select.call($this[0], evt, $this[0]);
            // console.log($this);
        },
		select: function(evt, vm){



            var $this = $(this),
                // target = $(evt.target),
                // isStarAction = target.data('action') && target.data('action') === 'star',
                model = $this.data('class'),
                // checkbox = $this.find('.checkbox'),
                type = $this.data('type'),
                id = $this.data('id'),
                selection_id = model + id;

            // console.log('documents.js select', this, evt);

            if(evt.target.nodeName === 'INPUT' || evt.target.className.trim() === 'selection-tab' || evt.target.className.trim() === 'selection' ){
                // do nothing, not my job
                return;
            }


            if(type && type==='group' && module.context.filter !== 'sharing' && evt.target.nodeName !== 'INPUT'){
                DMS.Services.Groups.open(id);
            }
            else if(type && type==='group' && module.context.filter === 'sharing' && evt.target.nodeName !== 'INPUT'){
                DMS.Services.Shared.openGroup(id);
            }
            else if(model && model==='document' && module.context.filter === 'public' && evt.target.nodeName !== 'INPUT'){
                console.log('opening details for public document', $this.data());

                var uuid = $this.data('uuid');

                if(!uuid){
                    console.log('id not set');
                    DMS.MessageBox.error('Selection', 'The selection cannot be processed. An error happend on our end.');
                    return false;
                }

                var pnl = Panels.openAjax(uuid, this, DMS.Paths.DOCUMENTS + '/public/' + uuid, {}, {
                    callbacks: {
                        click: _panelClickEventHandler
                    }
                }).on('dms:panel-loaded', function(panel_evt, panel){

                });
                
            }
            else if(evt.target.nodeName !== 'INPUT') {

                if(!id){
                    console.log('id not set');
                    DMS.MessageBox.error('Selection', 'The selection cannot be processed. An error happend on our end.');
                    return false;
                }

                var url_path = id;
                var panelUrl = (model && model==='project' ? DMS.Paths.PROJECTS : DMS.Paths.DOCUMENTS) + '/' + url_path;
                
                var pnl = Panels.openAjax(selection_id, this, panelUrl, {}, {
                    callbacks: {
                        click: _panelClickEventHandler
                    }
                }).on('dms:panel-loaded', function(panel_evt, panel){

                    if(model==='project'){

                        var h = new holmes({
                            input: '.js-search-user',
                            find: '.userlist .userlist__user',
                            placeholder: Lang.trans('projects.labels.search_member_not_found'),
                            mark: true,
                            class: {
                                visible: 'visible',
                                hidden: 'hidden'
                            }
                        });

                        try {
                            h.start();
                        } catch (error) {
                            
                        }

                    }
                });
                
                   

                _updateBinds();
            }

            if(evt.target.nodeName === 'A'){
                evt.preventDefault();
                return false;
            }

			return false;
		},


		uploads: {
			percentage: 0,
			isUploading: false,
			totalFiles: 0,
			status: "ready",
            targetGroup: null
		},

		context: {
			visibility: 'private',
            filter: undefined,
			group: undefined,
            search: undefined,
            isSearchRequest: false,
            canPublish: false,
            userIsProjectManager: false,
            filters: [],
            facets: [],
            maxUploadSize: 202800,
            
			//used for saving information about the page, like visibility and groups to pass to the uploader
		},


        setContext: function(args, search_term, visibility, filter, group){
            $.extend(module.context, args);
            if(args.filter ==='public' || args.filter ==='private'){
                module.context.visibility = args.filter; 
            }

            // filters array, ok

            if($.isPlainObject(module.context.filters) && Object.keys(module.context.filters).length > 0){
                
                module._filtersVisible = module.context.filters.geo_location ? false : true;

                if(module.context.filters.geo_location){
                    module._mapFiltersVisible = !module._mapFiltersVisible;
                }
            }
            else {
                module._filtersVisible = false;
            }

            _updateBinds();

            // If the highlight parameter is present, 
            // let's highlight the doc for a couple of seconds

            var highlight_doc = $("[data-id=" + getParameterByName('highlight') + "]");
            highlight_doc.addClass('newly-created');

            if(typeof highlight_doc.scrollIntoViewIfNeeded === "function"){
                highlight_doc.scrollIntoViewIfNeeded();
            }
            else if(typeof highlight_doc.scrollIntoView === "function"){
                highlight_doc.scrollIntoView();
            }
                        
            setTimeout(function(){
                highlight_doc.removeClass('newly-created');
            }, 2500);
            
            if(module.context.filter !== 'sharing') {
                attachContextMenu();
            }
        },

        // Action available on selection

        menu: {

            somethingIsSelected: false,
            
            nothingIsSelected: true,

            share: function(evt, vm){


                if(_Selection.isAnySelected()){

                    Share.open(_Selection.selection());

                }
                else{
                    _alert( Lang.trans('actions.selection.at_least_one') );
                }
                
                evt.preventDefault();

                return false;
                
            },

            createGroup: function(evt, vm, groupId, isPrivate){
                var params = undefined,
                    isPrivateRequest = isPrivate !== undefined || (evt.currentTarget && $(evt.currentTarget).data('isprivate') !== undefined);
                
                if(isPrivateRequest){
                    params = params || {};
                    params.isPrivate = isPrivate !== undefined ? isPrivate : $(evt.currentTarget).data('isprivate');
                }
                
                if(groupId || (module.context.filter === "group" && module.context.group && isPrivateRequest && params.isPrivate === false)){
                    
                    params = params || {};
                    params.group_context = groupId ? groupId : module.context.group;

                }

                DMS.dispatch(evt.currentTarget, 'dialog-show', { 
                    'url': DMS.Paths.GROUPS_CREATE, 
                    'params' : params
                });

                evt.preventDefault();
                return false;
            },

            shareGroup: function(evt, groupId){

                Share.open([{id: groupId, type: "group"}]);
                
                evt.preventDefault();

                return false;

            },
            
            unshare: function(evt){
                
                if(_Selection.isAnySelected()){

                    var documents = _Selection.selection(),
                        usable_documents = _.filter(documents, {'isShareWith': true});

                        var count = usable_documents.length;
                        
                        if(count > 0){
                        
                            var count_msg = count==1 ? _Selection.first().title : _Selection.first().title + " and " + (count-1) + " other";
                            
                            DMS.MessageBox.question('Remove share', 'Remove the sharing from ' + count_msg + '?', 'Unshare!', 'Cancel', function(choice){
                                
                                if(choice){
                                    DMS.Services.Shared.remove(_.map(usable_documents, 'share'), function(data){
                                        //success
                                        
                                        if(data.status && data.status==='ok'){
                                            
                                            DMS.MessageBox.success('Share removed', data.message);
                                            
                                        }
                                        else if(data.message) {

                                            DMS.MessageBox.error('Cannot remove shares', data.message);
        
                                        }
                                        
                                        DMS.navigateReload();
                                        
                                        
                                    }, function(obj, err, errText){
                                        //error
                                        if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                            DMS.MessageBox.error('Cannot remove share', obj.responseJSON.message);
                                        }
                                        else if(obj.responseJSON && obj.responseJSON.error){
                                            DMS.MessageBox.error('Cannot remove share', obj.responseJSON.error);
                                        }
                                        else {
                                            DMS.MessageBox.error('Cannot remove share', 'Cannot delete the specified shares.');
                                        }
                                    });

                                }
                                else {
                                    DMS.MessageBox.close();
                                }
                            });
                        
                        }
                        else {
                            _alert('Select at least 1 element in the "Shared by me" group');
                        }

                }
                else{
                    _alert('Select at least 1 element in the "Shared by me" group');
                }
                
                evt.preventDefault();
                
                return false;
            },
            
            makePublic: function(evt, docSelection){
                // if nothing is selected && in group -> make all files public?
                // if something is selected => make public the selection
                evt.preventDefault();
                
                if(docSelection.group) {
                    // group selection by context menu
                    
                    var grp_message = Lang.trans('networks.make_public_all_collection_dialog_text', {network: module.context.network_name});
                    
                    var grp_title = docSelection.name ? '"' + docSelection.name + '"' : 'Collection'; 
                    
                    if(docSelection.name){
                        grp_message = Lang.trans('networks.make_public_inside_collection_dialog_text', {item: docSelection.name, network: module.context.network_name});
                    }
                    
                    var grp_id = docSelection.group ? docSelection.group : module.context.group; 
                    
                    DMS.MessageBox.question( Lang.trans('networks.make_public_dialog_title', {item: grp_title, network: module.context.network_name}) , grp_message, Lang.trans('networks.publish_btn'), Lang.trans('actions.cancel'), function(choice){
                        
                        if(choice){
                            _doMakePublic({group:grp_id});
                        }
                        else {
                            // _doMakePublic({group:grp_id}, true);
                            DMS.MessageBox.close();
                        }
                    });
                    
                    
                }
                else if(_Selection.isAnySelected()){
                    //something is selected
                    
                    var count = _Selection.selectionCount(),
                        q_message = Lang.trans('networks.make_public_dialog_text_count', {count: count, network: module.context.network_name}),
                        q_btn = Lang.trans('actions.cancel'); 
                    
                    if(count==1){
                        q_message = Lang.trans('networks.make_public_dialog_text', {item: _Selection.first().title, network: module.context.network_name});
                    }
                    
                    DMS.MessageBox.question(Lang.trans('networks.make_public_dialog_title_alt', {network: module.context.network_name}), q_message, Lang.trans('networks.publish_btn'), q_btn, function(choice){
                        if(choice){
                            var toPublic = _Selection.selectionByType(_Selection.Types.DOCUMENT, 'id');
                        
                            _doMakePublic({documents:toPublic});
                        }
                        else {
                            DMS.MessageBox.close();
                            
                        }
                    }); 
                }
                else {
                    DMS.MessageBox.warning(Lang.trans('networks.make_public_dialog_title_alt', {network: module.context.network_name}), Lang.trans('networks.make_public_empty_selection', {network: module.context.network_name}));
                }
                
                return false;
            },

            deleteGroup: function(evt, groupId, groupname){
                
                var deleteTitle, deleteMessage;

                if(groupname){
                    deleteTitle = Lang.trans('groups.delete.dialog_title', {collection: groupname.trim() });
                    deleteMessage = Lang.trans('groups.delete.dialog_text', {collection: groupname.trim() });
                }
                else {
                    deleteTitle = Lang.trans('groups.delete.dialog_title_alt');
                    deleteMessage = Lang.trans('groups.delete.dialog_text_alt');
                }

                

                DMS.MessageBox.deleteQuestion(deleteTitle, deleteMessage).then(function(){

                        DMS.MessageBox.wait( Lang.trans('actions.deleting'), '...');


                        DMS.Services.Bulk.remove({groups:groupId, context:module.context.filter}, function(data){

                            if(data.status && data.status === 'ok'){

                                DMS.MessageBox.success( Lang.alternate('groups.delete.deleted_dialog_title', 'groups.delete.deleted_dialog_title_alt', 'collection', {collection: groupname ? groupname.trim() : undefined }) , data.message);
                                
                                if(module.context.filter===CONTEXT_GROUP && groupId==module.context.group){
                                    DMS.navigate(DMS.Paths.DOCUMENTS);
                                }
                                else {
                                    DMS.navigateReload();
                                }

                                

                            }
                            else if(data.message) {
                                DMS.MessageBox.error(Lang.alternate('groups.delete.cannot_delete_dialog_title', 'groups.delete.cannot_delete_dialog_title_alt', 'collection', {collection: groupname ? groupname.trim() : undefined }), data.message);
                            }

                        }, function(obj, err, errText){

                            if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                DMS.MessageBox.error(Lang.alternate('groups.delete.cannot_delete_dialog_title', 'groups.delete.cannot_delete_dialog_title_alt', 'collection', {collection: groupname ? groupname.trim() : undefined }), obj.responseJSON.message);
                            }
                            else if(obj.responseJSON && obj.responseJSON.error){
                                DMS.MessageBox.error(Lang.alternate('groups.delete.cannot_delete_dialog_title', 'groups.delete.cannot_delete_dialog_title_alt', 'collection', {collection: groupname ? groupname.trim() : undefined }), obj.responseJSON.error);
                            }
                            else {
                                DMS.MessageBox.error(Lang.alternate('groups.delete.cannot_delete_dialog_title', 'groups.delete.cannot_delete_dialog_title_alt', 'collection', {collection: groupname ? groupname.trim() : undefined }), Lang.trans('groups.delete.cannot_delete_general_error'));
                            }

                        });

                }, function(dismiss){
                    DMS.MessageBox.close();
                });

                evt.preventDefault();
                return false;

            },

            copyTo: function(evt, vm, dragTextData){


                if(vm.shared){

                    console.info('Copy to Shared');

                    DMS.MessageBox.question('Copy to Shared collection', 'You are about to copy to a shared Collection, all the elements will be shared.', function(isConfirmed){

                        if(isConfirmed){
                            _bulkCopyTo(evt, vm);
                        }
                        else {
                            DMS.MessageBox.close();
                        }


                    });

                    
                }
                else if(vm.groupId && dragTextData.dragEl && dragTextData.dragEl === 'group'){
                    //dropping a collection over a collection
                    // console.log('Dropped - collection ', evt, dragTextData, "over collection", vm);
                    module.menu.moveTo(evt, dragTextData, vm);
                }
                else {
                    _bulkCopyTo(evt, vm);
                }
                
                evt.preventDefault();
                return false;

            },

            /**
             * Move source under target. Applies only to collections (one at time)
             */
            moveTo: function(evt, source, target){
                console.warn('Move collection ', source, "under", target);
                
                if(!source.groupId && !target.groupId){
                    DMS.MessageBox.error(Lang.trans('groups.move.error_title_alt'), Lang.trans('groups.move.error_not_collection'));
                    return false;
                }
                
                if(source.groupId === target.groupId){
                    DMS.MessageBox.error(Lang.trans('groups.move.error_title_alt'), Lang.trans('groups.move.error_same_collection'));
                    return false;
                }
                
                if(source.isprivate && !target.isprivate){
                    // from private to public
                    var title = target.groupName ? Lang.trans('groups.move.move_to_title', {collection: target.groupName}) : Lang.trans('groups.move.move_to_project_title_alt'); 
                    DMS.MessageBox.question(title, Lang.trans('groups.move.move_to_project_text', {collection: source.groupName}), Lang.trans('actions.dialogs.move_btn'), Lang.trans('actions.dialogs.cancel_btn'), function(isConfirmed){
                        if(isConfirmed){
                            _collectionMove(source, target);
                        }
                        else {
                            DMS.MessageBox.close();
                        }
                    });
                }
                else if(!source.isprivate && target.isprivate){
                    // from public to private 
                    var title = target.groupName ? Lang.trans('groups.move.move_to_title', {collection: target.groupName}) : Lang.trans('groups.move.move_to_personal_title');
                    DMS.MessageBox.question(title, Lang.trans('groups.move.move_to_personal_text', {collection: source.groupName}), Lang.trans('actions.dialogs.move_btn'), Lang.trans('actions.dialogs.cancel_btn'), function(isConfirmed){
                        if(isConfirmed){
                            _collectionMove(source, target);
                        }
                        else {
                            DMS.MessageBox.close();
                        }
                    });
                }
                else {
                    _collectionMove(source, target);
                }
                
                
                
                
                
                evt.preventDefault();
                return false;
            },
            
            restore: function(evt, vm){
                if(_Selection.isAnySelected()){

                    var groups = [],
                        documents = [],
                        deleteTitle = 'Restore?',
                        deleteMessage = 'You are restoring ',
                        elementTitle = "";

                    var count = _Selection.selectionCount();
                    var currentSelection = _Selection.selection();

                    $.each(currentSelection, function(index, sel){

                        if(sel.type === 'group'){
                            groups.push(sel.id);
                            elementTitle = sel.title;
                        }
                        else{
                            documents.push(sel.id);
                            elementTitle = sel.title;
                        }

                    });

                    elementTitle = currentSelection[0].title;

                    if(count == 1){
                        deleteTitle = Lang.trans('documents.restore.restore_dialog_title', {document: elementTitle});
                        deleteMessage = Lang.trans('documents.restore.restore_dialog_text', {document: elementTitle});
                    }
                    else {
                        deleteTitle = Lang.trans('documents.restore.restore_dialog_title_count', {count: count});
                        deleteMessage = Lang.trans('documents.restore.restore_dialog_text_count', {count: count});
                    }

                    

                    DMS.MessageBox.question(deleteTitle, deleteMessage, 
                        Lang.trans('documents.restore.restore_dialog_yes_btn'), 
                        Lang.trans('documents.restore.restore_dialog_no_btn'), function(isConfirmed){

                        if(isConfirmed){

                            console.log(groups, documents);

                            DMS.MessageBox.wait( Lang.trans('actions.restoring'), '...');


                            DMS.Services.Bulk.restore({documents: documents, groups:groups, context:module.context.filter}, function(data){

                                if(data.status && data.status === 'ok'){

                                    DMS.MessageBox.success( Lang.trans('documents.restore.restore_success_title'), data.message);

                                    _Selection.clearAndDestroy();

                                }
                                else if(data.message) {
                                    DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), data.message);
                                }

                            }, function(obj, err, errText){

                                if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                    DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), obj.responseJSON.message);
                                }
                                else if(obj.responseJSON && obj.responseJSON.error){
                                    DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), obj.responseJSON.error);
                                }
                                else {
                                    DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), Lang.trans('documents.restore.restore_error_text_generic'));
                                }

                            });

                        }
                        else {
                            DMS.MessageBox.close();
                        }

                    });

                }
                else{
                    _alert(Lang.trans('actions.selection.at_least_one_document'));
                }   

                evt.preventDefault();
                return false;
            
            },
            
            emptytrash: function(evt, vm){

                evt.preventDefault();
                
                DMS.MessageBox.question( Lang.trans('documents.trash.clean_title'), Lang.trans('documents.trash.empty_all_text'), 
                    Lang.trans('documents.trash.yes_btn'), 
                    Lang.trans('documents.trash.no_btn'), function(isConfirmed){
                        
                    if(isConfirmed){ 
                        
                        DMS.MessageBox.wait( Lang.trans('actions.cleaning_trash'), Lang.trans('actions.cleaning_trash_wait'));

                        DMS.Services.Bulk.emptytrash( function(data){

                            if(data.status && data.status === 'ok'){

                                DMS.MessageBox.success( Lang.trans('documents.trash.cleaned'), data.message);

                                DMS.navigateReload();

                            }
                            else if(data.message) {
                                DMS.MessageBox.error(Lang.trans('documents.trash.cannot_clean'), data.message);
                            }

                        }, function(obj, err, errText){

                            if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                DMS.MessageBox.error(Lang.trans('documents.trash.cannot_clean'), obj.responseJSON.message);
                            }
                            else if(obj.responseJSON && obj.responseJSON.error){
                                DMS.MessageBox.error(Lang.trans('documents.trash.cannot_clean'), obj.responseJSON.error);
                            }
                            else {
                                DMS.MessageBox.error(Lang.trans('documents.trash.cannot_clean'), Lang.trans('documents.trash.cannot_clean_general_error'));
                            }

                        });
                    }
                    else {
                        DMS.MessageBox.close();
                    }
                });
                
                return false;
            },

            del: function(evt, vm){

                if(_Selection.isAnySelected()){

                    var groups = [],
                        documents = [],
                        deleteTitle = 'Delete?',
                        deleteMessage = 'You are deleting ',
                        elementTitle = "";

                    var count = _Selection.selectionCount();
                    var currentSelection = _Selection.selection();

                    $.each(currentSelection, function(index, sel){

                        if(sel.type === 'group'){
                            groups.push(sel.id);
                            elementTitle = sel.title;
                        }
                        else{
                            documents.push(sel.id);
                            elementTitle = sel.title;
                        }

                    });

                    elementTitle = currentSelection[0].title;

                    if(count == 1){
                        deleteTitle = Lang.trans('documents.delete.dialog_title', {document: elementTitle});
                        deleteMessage = Lang.trans('documents.delete.dialog_text', {document: elementTitle});
                    }
                    else {
                        deleteTitle = Lang.trans('documents.delete.dialog_title_count', {count: count});
                        deleteMessage = Lang.trans('documents.delete.dialog_text_count', {count: count});
                    }

                    

                    DMS.MessageBox.deleteQuestion(deleteTitle, deleteMessage, {
                        confirmButtonText: Lang.trans('actions.dialogs.trash_btn')
                    }).then(function(){

                        DMS.MessageBox.wait( Lang.trans('actions.deleting'), '...');

                        DMS.Services.Bulk.remove({documents: documents, groups:groups, context:module.context.filter}, function(data){

                            if(data.status && data.status === 'ok'){

                                DMS.MessageBox.success( Lang.trans('documents.delete.deleted_dialog_title_alt'), data.message);

                                _Selection.clearAndDestroy();

                            }
                            else if(data.message) {
                                DMS.MessageBox.error(Lang.trans('documents.delete.cannot_delete_dialog_title_alt'), data.message);
                            }

                        }, function(obj, err, errText){

                            if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                DMS.MessageBox.error(Lang.trans('documents.delete.cannot_delete_dialog_title_alt'), obj.responseJSON.message);
                            }
                            else if(obj.responseJSON && obj.responseJSON.error){
                                DMS.MessageBox.error(Lang.trans('documents.delete.cannot_delete_dialog_title_alt'), obj.responseJSON.error);
                            }
                            else {
                                DMS.MessageBox.error(Lang.trans('documents.delete.cannot_delete_dialog_title_alt'), Lang.trans('documents.delete.cannot_delete_general_error'));
                            }

                        });

                    }, function(dismiss){
                        DMS.MessageBox.close();
                    });

                }
                else{
                    _alert( Lang.trans('actions.selection.at_least_one_document') );
                }   

                evt.preventDefault();
                return false;
            },

            forcedel: function(evt, vm){

                if(_Selection.selectionCount() == 1){ //max 1 element selected

                    var currentSelection = _Selection.first();

                    console.log("current selection", currentSelection);

                    var groups = null,
                        documents = null,
                        deleteTitle = Lang.trans('documents.permanent_delete.dialog_title', {document: currentSelection.title});
                        deleteMessage = Lang.trans('documents.permanent_delete.dialog_text', {document: currentSelection.title});

                    DMS.MessageBox.deleteQuestion(deleteTitle, deleteMessage, {confirmButtonText: Lang.trans('actions.dialogs.delete_btn')}).then(function(){
                        DMS.MessageBox.wait( Lang.trans('actions.deleting'), '...');

                        if(currentSelection.type === "document"){
                            DMS.Services.Documents.forceRemove(currentSelection.id, _handleSuccessPermanentDeleteResponse, _handleFailedPermanentDeleteResponse);
                        }
                        else {
                            DMS.Services.Groups.forceRemove(currentSelection.id, _handleSuccessPermanentDeleteResponse, _handleFailedPermanentDeleteResponse);
                        }
                    }, function(dismiss){
                        DMS.MessageBox.close();
                    });

                }
                else{
                    _alert( Lang.trans('actions.selection.only_one') );
                }

                evt.preventDefault();
                return false;
            }

        },


        initUploadService: function(){
            console.log('Initializing upload service...');
            _initUploadService();
        },
        groupSubmit: function(evt, vm){
            // when creating a group from the tree view
            console.log("Group submit", evt, vm);

            var that = $(this);

            DMS.Services.Groups.create({name:this.name.value, color:this.color.value, ok_template:true}, function(data){

                console.info("Group created");

                document.getElementById('document-tree').innerHTML = data;

            }, function(obj, status, statusText){

                console.error('error on create group');

                _alert(Lang.trans('errors.generic_title'), obj.responseText, "error");

            });

            evt.preventDefault();

            return false;
        },

        groups: {

            isExpandedAll: false,

            expandOrCollapse: function(evt, vm){

                var $this = $(this),
                    isExpanded = $this.data('expanded');

                if(isExpanded){
                    //collapse

                    $this.data('expanded', false);
                    $this.parent().siblings().addClass('navigation__expandable--collapsed').removeClass('navigation__expandable--expanded');
                    $this.addClass('navigation__expander--collapsed').removeClass('navigation__expander--expanded');
                }
                else {
                    //expand

                    $this.data('expanded', true);
                    $this.parent().siblings().addClass('navigation__expandable--expanded').removeClass('navigation__expandable--collapsed');
                    $this.addClass('navigation__expander--expanded').removeClass('navigation__expander--collapsed');
                }

                //only first child

                if(evt){
                    evt.preventDefault();
                }

                return false;
            },
            
            ensureCurrentVisibility: function(){
                var current = _treeView.find('.js-tree-current');
                
                var tree_item_parents = current.parents('.js-tree-item');
                
                if(current.length > 0){
                
                    var first = current[0];
                    
                    if(tree_item_parents.length > 0){
                        
                        tree_item_parents.each(function(k, v){
                            
                            var chev = $(v).find('.js-tree-chevron');
                            if(chev.length > 0){
                                var func = module.groups.expandOrCollapse.bind(chev[0]);
                                func(undefined, this);
                            }
                            
                        }.bind(this));
                        
                    }
                    
                    if(typeof first.scrollIntoViewIfNeeded === "function"){
                        first.scrollIntoViewIfNeeded();
                    }
                    else if(typeof first.scrollIntoView === "function"){
                        first.scrollIntoView();
                    }
                    
                }
                
                if(window.sessionStorage && window.sessionStorage['collections-created']){
                    var collection_id = window.sessionStorage.getItem('collections-created');
                    
                    var new_collection = _treeView.find('.js-tree-item-inner[data-group-id^='+collection_id+']');
                    console.warn(new_collection);
                
                    if(new_collection.length > 0){
                        var new_collection_parents = new_collection.parents('.js-tree-item');
                        var first = new_collection[0];
                        
                        if(new_collection_parents.length > 0){
                            
                            new_collection_parents.each(function(k, v){
                            
                                var chev = $(v).find('.js-tree-chevron.navigation__expander--collapsed');
                                if(chev.length > 0){
                                    var func = module.groups.expandOrCollapse.bind(chev[0]);
                                    func(undefined, this);
                                }
                                
                            }.bind(this));
                            
                        }
                        
                        if(typeof first.scrollIntoViewIfNeeded === "function"){
                            first.scrollIntoViewIfNeeded();
                        }
                        else if(typeof first.scrollIntoView === "function"){
                            first.scrollIntoView();
                        }
                        
                        new_collection.addClass('newly-created');
                        
                        setTimeout(function(){
                            new_collection.removeClass('newly-created');
                            window.sessionStorage.removeItem('collections-created');
                        }, 900);
                        
                    }
                    
                    
                }
                
                
            },

            showEdit: function(evt, vm){

                var that = $(this),
                    data = that.data(),
                    id = data.groupId,
                    project = data.project;

                if(project && module.context.userIsProjectManager){
                    DMS.navigate(DMS.Paths.PROJECTS_EDIT.replace('{ID}', project));
                }
                else if(project && !module.context.userIsProjectManager){
                    DMS.MessageBox.error('You cannot edit a project you don\'t manage');
                }
                else {
                    DMS.dispatch(evt.currentTarget, 'dialog-show', { 
                        'url': DMS.Paths.GROUPS_EDIT.replace('{ID}', id)
                    });
                }

                evt.preventDefault();

                return false;
            },
            showPanel: function(evt, vm){

                var that = $(this),
                    data = that.data(),
                    id = data.groupId;

                var panelUrl = DMS.Paths.GROUPS_ONLY + '/' + id + '/details';
                
                var pnl = Panels.openAjax('grp' + id, this, panelUrl, {}, {
                    callbacks: {
                        click: _panelClickEventHandler
                    }
                }).on('dms:panel-loaded', function(panel_evt, panel){

          
                });

                evt.preventDefault();

                return false;
            }

        },
        
        
        _filtersVisible: false,
        
        
        isVisible: function(){            
            return module._filtersVisible;
        },
        
        openClose: function(evt, vm){
            module._filtersVisible = !module._filtersVisible;

            if(module._filtersVisible && module._mapFiltersVisible){
                module._mapFiltersVisible = false;
            }
            
            _bindPageArea.sync();
            
            evt.preventDefault();
            
        },
        
        _mapFiltersVisible: false,

        isMapVisible: function(){            
            return module._mapFiltersVisible;
        },
        
        openCloseMap: function(evt, vm){
            module._mapFiltersVisible = !module._mapFiltersVisible;

            if(module._filtersVisible && module._mapFiltersVisible){
                module._filtersVisible = false;
            }
            
            _bindPageArea.sync();

            _pageArea.trigger('spatialfilters:' + (module._mapFiltersVisible ? 'open' : 'close') );
            
            evt.preventDefault();
            
        },
        

	};

	
    _bindPageArea = _rivets.bind(_pageArea, module);
    _bindActionBar = _rivets.bind(_actionBar, module.menu);
    

    function _updateBinds(){
        module.menu.somethingIsSelected = _Selection.isAnySelected();
        module.menu.nothingIsSelected = !module.menu.somethingIsSelected;

        if(_bindActionBar){
            _bindActionBar.sync();
        }
        if(_bindPageArea){
            _bindPageArea.sync();
        }
        
    }

    _documentArea.on('dms:selection-changed', function(evt){

        _updateBinds();

    });

    _documentArea.on('dms:update', function(evt){

        _updateBinds();

    });

    function _contextNoop(e){
        e.preventDefault();
        console.log('Context menu click', this, e);
    }

    function attachContextMenu(){

        var _menu_items = [];

        if(module.context.filter ==='projectspage' && !module.context.isSearchRequest){
            _menu_items.push(
            {
                text: Lang.trans('actions.open'),
                action: function(e){
                    e.preventDefault();


                    var link = $(this).find('.item__link');
                    
                    if(link){
                        DMS.navigate(link.attr('href'), null, true);
                    }
    
    
                }
            });

            _menu_items.push({
                divider: true,
            });
        }

        _menu_items.push(
            {
                text: Lang.trans('actions.details'),
                icon: 'icon-action-black icon-action-black-ic_info_outline_black_24dp',
                action: function(e){
                    e.preventDefault();
    
                    if(_Selection.selectionCount() > 1){
    
                        DMS.MessageBox.error('Multiple Selection', 'The details view currently don\'t support multiple selection');
                        return false;
                    }
    
                    module.select.call(this, e, this);
    
    
                }
            });

        if(module.context.filter !== 'trash' && module.context.filter !== 'public' && (module.context.filter !== 'projectspage' || 
            (module.context.filter ==='projectspage' && module.context.isSearchRequest)) ){
            if(module.context.canShare){
                _menu_items.push({
                text: Lang.trans('share.share_btn'),
                action: function(e){
                    if(!_Selection.isSelect(this, true)){
                            _Selection.select(this, true);
                        }
                        module.menu.share(e, this);
                    }
                });
            }
            if(module.context.canPublish){

                _menu_items.push({
                    text: Lang.trans('networks.publish_to_short'),
                    action: function(e){ 
                        if(!_Selection.isSelect(this, true)){
                            _Selection.select(this, true);
                        }
                        module.menu.makePublic(e, this);
                    }
                });
            }
        }

        if((module.context.filter !=='projectspage' && module.context.filter !== 'public') || 
            (module.context.filter ==='projectspage' && module.context.isSearchRequest)){
            _menu_items.push({
                divider: true,
            });
        }
        if(module.context.filter !== 'public'){
            _menu_items.push({
                text: Lang.trans('actions.edit'),
                action: function(e){
    
                    e.preventDefault();
    
                    if(_Selection.selectionCount() > 1){
    
                        DMS.MessageBox.error('Multiple Selection', 'The edit action is not available on multiple selection');
                        return false;
                    }
    
                    var id = this.data('id');
                        project = this.data('project');
    
                    if(project && module.context.userIsProjectManager){
                        DMS.navigate(DMS.Paths.PROJECTS_EDIT.replace('{ID}', project));
                    }
                    else if(project && !module.context.userIsProjectManager){
                        DMS.MessageBox.error('You cannot edit a project you don\'t manage');
                    }
                    else {
                        DMS.Services.Documents.openEditPage(id);
                    }
                }
            });
        }

            if(module.context.filter === 'trash'){
                _menu_items.push({
                    text: Lang.trans('actions.forcedelete_btn_alt'),
                    action: function(e){
                        if(!_Selection.isSelect(this, true)){
                            _Selection.select(this, true);
                        }
                        module.menu.forcedel(e, this);
                    }
                });
            }
            
            else if((module.context.filter !=='projectspage' && module.context.filter !== 'public') || 
            (module.context.filter ==='projectspage' && module.context.isSearchRequest)){


                _menu_items.push({
                    text: Lang.trans('actions.trash_btn_alt'),
                    action: function(e){
                        if(!_Selection.isSelect(this, true)){
                            _Selection.select(this, true);
                        }
                        module.menu.del(e, this);
                    }
                });
            }

            

        _context.attach(_documentArea, '.document-item', _menu_items);

        var _groupsMenuItems = [
            {
                text: Lang.trans('actions.details'),
                action: function(e){
                    e.preventDefault();
    
                    if(_Selection.selectionCount() > 1){
                        DMS.MessageBox.error('Multiple Selection', 'The details view currently don\'t support multiple selection');
                        return false;
                    }

                    var $this = $(this),
                        model = $this.data('class');
    
                    if(model!=='group'){
                        module.select.call(this, e, this);
                    }
                    else {
                        module.groups.showPanel.call(this, e, this);
                    }

    
                },
            },
            {
                text: Lang.trans('actions.edit'),
                action: module.groups.showEdit,
            },
            {
                text: Lang.trans('actions.trash_btn_alt'),
                action: function(e){ 
                    var id = this.data('groupId'),
                        anchor = this.hasClass('js-tree-item-inner') ? this : this.find('.js-tree-item-inner');
                    module.menu.deleteGroup(e, id, anchor ? anchor[0].innerText || anchor[0].textContent : undefined);

                },
            },
            {
                divider: true,
            },
            {
                text: Lang.trans('actions.create_collection_btn'),
                action: function(e){ 
                    var id = this.data('groupId'),
                        isPrivate = this.data('isprivate');
                    module.menu.createGroup(e, id, id, isPrivate);
                }
            },
            {
                divider: true,
            },
            {
                text: Lang.trans('share.share_btn'),
                action: function(e){ 
                    var id = this.data('groupId');
                    module.menu.shareGroup(e, id);
                }
            }
        ];

        if(module.context.canPublish && module.context.filter !== 'trash'){
            _groupsMenuItems.push({
                text: Lang.trans('actions.publish_documents'),
                action: function(e){ 
                    var id = this.data('groupId'),
                        name = this.find('.js-tree-item-inner').first().text().trim();
    
                    module.menu.makePublic(e, {group: id, name: name});
                }
            });
        }

        var _trashGroupMenu = [{
            text: Lang.trans('actions.forcedelete_btn_alt'),
            action: function(e){
                if(!_Selection.isSelect(this, true)){
                    _Selection.select(this, true);
                }
                module.menu.forcedel(e, this);
            }
        }];
        
    
        _context.attach(_treeView, '.js-groups-menu', _groupsMenuItems);
        _context.attach(_documentArea, '.group-item', (module.context.filter === 'trash') ? _trashGroupMenu : _groupsMenuItems);

    }
    

    function _initUploadService(){

        if(_modernizr.filereader && _modernizr.draganddrop){

            _require(["modules/dropzone"], function(_dropzone){

                _dropzone.autoDiscover = false;
                
            	var dropzone = new _dropzone( '#js-drop-area', { // Make the whole body a dropzone
            	    url: DMS.Paths.fullUrl(DMS.Paths.DOCUMENTS), // Set the url
            	    paramName: "document",
                    createImageThumbnails: false,
                    filesizeBase:1024,
            	    previewsContainer: "#previews", // Define the container to display the previews
            	    clickable: module.context.filter==='projectspage' || module.context.filter==='starred' || module.context.filter==='trash' || module.context.filter==='shared' ? null : "#upload_trigger", // Define the element that should be used as click trigger to select files.
                    // acceptedFiles: 'image/*,application/pdf,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.wordprocessingml.document',

                    addRemoveLinks:true,
                    
                    previewTemplate: '<div class="dz-preview dz-file-preview"><div class="dz-details"><div class="dz-filename"><span data-dz-name></span></div><div class="dz-size" data-dz-size></div><img data-dz-thumbnail /></div><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-success-mark"><span>✔</span></div><div class="dz-error-mark"><span>✘</span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div>',

            	    // uploadMultiple: true,
            		parallelUploads: 1,
                    maxFilesize: module.context.maxUploadSize,
            		maxFiles: 10000,
                    
                    dictDefaultMessage: Lang.trans('documents.messages.drag_hint'),
                    dictFallbackMessage: Lang.trans('documents.upload.dragdrop_not_supported'),
                    dictFallbackText: Lang.trans('documents.upload.dragdrop_not_supported_text'),
                    dictFileTooBig: Lang.trans('validation.custom.document.required_alt', {size: module.context.maxUploadSize, unit: 'MB'}), //"File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.",
                    dictInvalidFileType: "You can't upload files of this type.",
                    dictResponseError: "Server responded with {{statusCode}} code.",
                    dictCancelUpload: '', //old: "Cancel upload"
                    dictCancelUploadConfirmation: Lang.trans('documents.upload.cancel_question'),
                    dictRemoveFile: Lang.trans('documents.upload.remove_btn'),
                    dictRemoveFileConfirmation: null,
                    dictMaxFilesExceeded: Lang.trans('documents.upload.max_uploads_reached_text'),
                    
                    headers: {
                      "X-CSRF-TOKEN" : DMS.csrf()
                    },
                    
                   accept: function(file, done) {
                        if( !file.type && file.name.indexOf(".") === -1 && file.size == 4096){
                            // Firefox way is so different than the others that I don't support it
                            done( {error: Lang.trans('documents.upload.folders_dragdrop_not_supported')});
                        }
                        else if(file.type && file.size === 0){
                            done( {error: Lang.trans('documents.upload.empty_file_error')});
                        }
                        else if(this.uploadContext && this.uploadContext==='projectspage' && !this.targetGroup){
                            done({error: Lang.trans('documents.upload.outside_project_target_area')});
                        }
                        else {
                            done();
                        }

                   },

                    error: function(file, message) {
                        debugger;
                        var node, _i, _len, _ref, _results;
                        if (file.previewElement) {
                          file.previewElement.classList.add("dz-error");
                          if (typeof message !== "String" && message.error) {
                            message = message.error;
                          }
                          else if (typeof message !== "String" && message.document) {
                            message = _.isArray(message.document) ? message.document.join(",") : message.document;
                          }
                          else if(file.xhr.status === 413){
                              message = this.options.dictFileTooBig;
                          }
                          else if(message.length > 0) {
                            message = message.indexOf('KB') ? message : Lang.trans('errors.generic_text');
                          }
                          else {
                            message = Lang.trans('errors.generic_text');
                          }
                          _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                          _results = [];
                          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            node = _ref[_i];
                            _results.push(node.innerHTML = message);
                          }
                          return _results;
                        }
                      },
                    
                    dragover: function dragover(e) {
                        if(dragOfInternalElement){
                            return true;
                        }
                        return this.element.classList.add("dz-drag-hover");
                    },
                    dragenter: function dragenter(e) {
                        if(dragOfInternalElement){
                            return true;
                        }                 
                        return this.element.classList.add("dz-drag-hover");
                    },

            	    init: function () {

                        this.uploadContext = module.context.filter;

                            this.on("error", function (file, response, xhr) {

                                console.error("File upload error", file, response, xhr);

                                module.uploads.status = "error";

                            });

                            this.on("success", function (file, response) {

                                console.log("File upload success", file, response);

                            });

                            this.on('drop', function(evt){

                                console.log('File drop, upload', evt);
                                
                                if(evt.target){
                                    
                                    var target = $(evt.target);
                                    var targetInfo = target.data();

                                    // drop target might not be the item that has
                                    // attached the data that I need 

                                    if(!targetInfo.groupId){
                                        targetInfo = target.parents('.item--project').data() || targetInfo;
                                    }
                                    if(!targetInfo.groupId){
                                        targetInfo = target.parents('.js-tree-item-inner').data() || targetInfo;
                                    }

                                    console.info(targetInfo);

                                    if(targetInfo){
                                        // storing the groupId for later
                                        module.uploads.targetGroup = targetInfo.groupId || null;  
                                        this.targetGroup = targetInfo.groupId || null;  
                                    }
                                    else {
                                        module.uploads.targetGroup = null;
                                        this.targetGroup = null;
                                    }

                                }

                                // return true;
                            });

                            this.on("addedfile", function(file){

                                $('#upload-status').addClass('visible');

                            	module.uploads.isUploading = true;
                            	module.uploads.totalFiles = this.files.length;

                            	module.uploads.status = "uploading";

                                if(module.uploads.targetGroup){
                                    // if we saw an upload over a group, add this information
                                    file.targetGroup = module.uploads.targetGroup; 
                                    // module.uploads.targetGroup = null;
                                    // this.targetGroup = null;
                                }

                            });

                            this.on("removedfile", function(file){

                                module.uploads.totalFiles = this.files.length;

                                if(module.uploads.totalFiles==0){

                                    $('#upload-status').removeClass('visible');
                                }

                            });

                            this.on("queuecomplete", function(){

                                module.uploads.targetGroup = null;
                                this.targetGroup = null;

                                if(module.uploads.status === 'error'){
                                	module.uploads.isUploading = false;
                                }
                                else {
                                    module.uploads.isUploading = false;
                                    module.uploads.status = "completed";
                                    $('#upload-status').removeClass('visible');
                                    DMS.navigateReload();
                                    _alert( Lang.trans('documents.upload.all_uploaded'), "", "success");
                                }

                            });

                            this.on("totaluploadprogress", function(uploadProgress, totalBytes, totalBytesSent){
                            	module.uploads.percentage = uploadProgress;

                            });

                            this.on("maxfilesreached", function(){
                            	_alert(Lang.trans('documents.upload.max_uploads_reached_title'), Lang.trans('documents.upload.max_uploads_reached_text'), "error");
                            });
                        }
            	  });

            	dropzone.on("sending", function(file, xhr, formData) {
console.info('File sending', file, formData, module.uploads);
                    //file.name //contiene solo il nome del file
                    //file.fullPath //contiene il nome della cartella se disponibile
// console.info(file);
                    var folder = file.fullPath ? file.fullPath.replace(file.name, '').trim() : '';

                    formData.append("document_fullpath", file.fullPath);
                    formData.append("document_name", file.name);
                    if(folder.length > 0){
                        formData.append("folder_path", folder);
                    }

              		formData.append("_token", DMS.csrf());

                    if(file.targetGroup){ 
                        // if we saw an upload over a group, add this information 
 
                        formData.append("group", file.targetGroup); 
 
                    } 

                    if(!file.targetGroup && module.context.filter && module.context.filter === CONTEXT_GROUP ){

                        formData.append("group", module.context.group);
                    }
            	});

            });
        }
        else {
            console.warn('Loading upload fallback support');

            $("#upload_trigger").on('click', function(evt){

                DMS.navigate(DMS.Paths.UPLOAD_FALLBACK);

                evt.preventDefault();
                return false;
            });
        }

    }
    
	return module;
});
