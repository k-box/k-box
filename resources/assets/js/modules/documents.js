define("modules/documents", ["require", "modernizr", "jquery", "DMS", "modules/star", "sweetalert", "modules/panels", "combokeys", "modules/selection", "modules/minimalbind", "context", "lodash", 'elasticlist' ], function (_require, _modernizr, $, DMS, Star, _alert, Panels, _combokeys, _Selection, _rivets, _context, _, Elastic) {
    
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
        tristateButton: _actionBar.find('.selection-button'),
        selectionBoundingElement: '.selection',
        selectionCheckbox: '.checkbox'
    });

    ///////////////////////
    // For drag and drop //
    ///////////////////////

    var dragItems = $('[draggable=true]'),
        droppables = $('[data-drop=true]');

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

        // console.log('dragleave', this);

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
        evt.originalEvent.dataTransfer.dropEffect = dragText !== 'dms_drag_collection' ? 'copy' : 'link';

        // console.log('DROP', dragText, this);
        
        if(dragText!=='dms_drag_action'){
            dragText = JSON.parse(dragText);
        }

        var dropAction = $this.data('dropAction');

        if(dropAction){
            module['menu'][dropAction].call(module, evt, $this.data(), dragText);
        }

    });

    dragItems.on('dragstart', function(evt){

         //raises checked of undefined

        // console.log('dragstart', this);

        evt.originalEvent.dataTransfer.effectAllowed = 'all';
        

        var $this = $(this),
            _data = $this.data(); 

        if(!_Selection.isAnySelected() && !_data.dragEl){
            evt.originalEvent.dataTransfer.setData('text', 'dms_drag_action');
            _Selection.select($(this), true);
        }
        else if(_data.dragEl && _data.dragEl === 'group'){
            evt.originalEvent.dataTransfer.setData('text', JSON.stringify(_data));
            console.log('Dragging a collection from the tree');
        }
        else {
            evt.originalEvent.dataTransfer.setData('text', 'dms_drag_action');
        }

        

    });

    dragItems.on('dragend', function(evt){

        // console.log('dragend', this);

        if(_Selection.selectionCount() == 1){
            _Selection.deselect($(this), true);
        }

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

                    DMS.Services.Bulk.copyTo({
                        documents: documents, 
                        // groups:groups, 
                        context:module.context.filter, 
                        current_group: module.context.group ? module.context.group : null,
                        destination_group:selectedGroup}, function(data){

                        if(data.status && data.status === 'ok'){

                            DMS.MessageBox.success('Copied', data.message);

                        }
                        else if(data.message) {
                            DMS.MessageBox.error('Cannot copy to', data.message);
                        }

                    }, function(obj, err, errText){

                        if(obj.responseJSON && obj.responseJSON.status === 'error'){
                            DMS.MessageBox.error('Cannot copy to', obj.responseJSON.message);
                        }
                        else if(obj.responseJSON && obj.responseJSON.error){
                            DMS.MessageBox.error('Cannot copy', obj.responseJSON.error);
                        }
                        else {
                            DMS.MessageBox.error('Cannot copy to', 'Cannot copy to.');
                        }

                    });

                }
                else{
                    _alert('Select at least 1 element');
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

                DMS.MessageBox.wait('Moved', 'The collection has been moved, we are refreshing your visualization...');
                DMS.navigateReload();

            }
            else if(data.message) {
                DMS.MessageBox.error('Cannot move', data.message);
            }

        }, function(obj, err, errText){

            if(obj.responseJSON && obj.responseJSON.status === 'error'){
                DMS.MessageBox.error('Cannot move', obj.responseJSON.message);
            }
            else if(obj.responseJSON && obj.responseJSON.error){
                DMS.MessageBox.error('Cannot move', obj.responseJSON.error);
            }
            else {
                DMS.MessageBox.error('Cannot move', 'The move operation cannot be performed due to an error, please contact your DMS Administrator.');
            }

        });
    }

    function _panelClickEventHandler(evt, data){

        // 1. only document panel
        // 2. only click on data-action elements
        
        console.warn('_panelClickEventHandler', this, evt, data);

        if(data.action && data.action === 'removeGroup'){

            DMS.Services.Documents.update(data.documentId, {remove_group: data.groupId}, function(data_back){

                if(data_back.id){

                    DMS.MessageBox.success('Removed from collection', 'The document has been removed from the collection');

                    // Reload panel
                    Panels.openAjax('document'+data_back.id, this, DMS.Paths.DOCUMENTS + '/' + data_back.id, {}, {
                        callbacks: {
                            click: _panelClickEventHandler
                        }
                    });

                }
                else {
                    DMS.MessageBox.error('Cannot remove from collection', data_back);
                }

            }, function(obj, err, errorText){

                if(obj.status === 422){

                    console.log(obj.responseJSON);

                    var html = '';

                    $.each(obj.responseJSON, function(index, el){

                        html += $.isArray(el) ? el[0]: el;

                    });

                    DMS.MessageBox.error('Cannot Update document', html);


                }
                else if(obj.responseJSON && obj.responseJSON.status === 'error'){
                    DMS.MessageBox.error('Cannot remove from collection', obj.responseJSON.message);
                }
                else if(obj.responseJSON && obj.responseJSON.error){
                    DMS.MessageBox.error('Cannot remove from collection', obj.responseJSON.error);
                }
                else {
                    DMS.MessageBox.error('Update remove from collection', 'Cannot remove document from collection');
                }

            });

        }
        else if(data.action && data.action === 'restore'){
            
                DMS.MessageBox.question("Restore document?", "Restore the document", 'Yes, Restore!', 'No, Cancel', function(isConfirmed){

                    if(isConfirmed){

                        DMS.MessageBox.wait('restoring...', '...');


                        DMS.Services.Bulk.restore({documents: [data.id], context:'trash'}, function(data){

                            if(data.status && data.status === 'ok'){

                                DMS.MessageBox.success('Restored', data.message);

                                // Reload panel
                                Panels.openAjax('document'+data.id, this, DMS.Paths.DOCUMENTS + '/' + data.id, {}, {
                                    callbacks: {
                                        click: _panelClickEventHandler
                                    }
                                });

                            }
                            else if(data.message) {
                                DMS.MessageBox.error('Cannot restore', data.message);
                            }
                            
                        }, function(obj, err, errText){

                            if(obj.status === 422){

                                console.log(obj.responseJSON);
            
                                var html = '';
            
                                $.each(obj.responseJSON, function(index, el){
            
                                    html += $.isArray(el) ? el[0]: el;
            
                                });
            
                                DMS.MessageBox.error('Cannot restore document', html);
            
            
                            }
                            else if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                DMS.MessageBox.error('Cannot Restore document', obj.responseJSON.message);
                            }
                            else {
                                DMS.MessageBox.error('Restore Document', 'The document restore procedure was not completed succesfully');
                            }

                        });

                    }
                    else {
                        DMS.MessageBox.close();
                    }

                });
            
            
        }

    }
    
    function _doMakePublic(params, changeTitles){
        
        if(changeTitles){
            
            DMS.MessageBox.warning('Change Name not currently available', 'Change Name not currently available');
//            Panels.dialogOpen(DMS.Paths.SHARE_CREATE, {groups:groupId}, {callbacks: { form_submit_success: function(evt, data){
//
//                Panels.dialogClose();
//
//                DMS.MessageBox.success('Share created', 'The document has been shared');
//
//            } }});
        }
        else {
            
            DMS.MessageBox.wait('Publishing...', 'Please wait while the documents will be made publicly available in the K-Link Network.');
            DMS.Services.Bulk.makePublic(params, function(data){
                
                DMS.MessageBox.success('Publish completed', (data && data.message) ? data.message : 'The documents has been shared');
                
                
            }, function(obj, err, errText){
                
                if(obj.responseJSON && obj.responseJSON.status === 'error'){
                    DMS.MessageBox.error('Publish Error', obj.responseJSON.message);
                }
                else if(obj.responseJSON && obj.responseJSON.error){
                    DMS.MessageBox.error('Publish Error', obj.responseJSON.error);
                }
                else {
                    DMS.MessageBox.error('Publish Error', 'Cannot perform the publish operation. ' + errText);
                }
                
            });
            
            
            
        }
        
        
        
        



    }


	var module = {



		select: function(evt, vm){



            var $this = $(this),
                // target = $(evt.target),
                // isStarAction = target.data('action') && target.data('action') === 'star',
                model = $this.data('class'),
                // checkbox = $this.find('.checkbox'),
                type = $this.data('type'),
                id = $this.data('id'),
                selection_id = model + id + $this.data('institution');

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
            else if(evt.target.nodeName !== 'INPUT') {

                var url_path = id ? id : $this.data('inst')+ '/' + $this.data('doc');
                
                Panels.openAjax(selection_id, this, DMS.Paths.DOCUMENTS + '/' + url_path, {}, {
                    callbacks: {
                        click: _panelClickEventHandler
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
			status: "ready"
		},

		context: {
			visibility: 'private',
            filter: undefined,
			group: undefined,
            search: undefined,
            filters: [],
            facets: [],
            
			//used for saving information about the page, like visibility and groups to pass to the uploader
		},


        setContext: function(args, search_term, visibility, filter, group){
            $.extend(module.context, args);
            if(args.filter ==='public' || args.filter ==='private'){
                module.context.visibility = args.filter; 
            }
            module._filtersVisible = !$.isArray(module.context.filters);
            _updateBinds();
            
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

                    var groups = [],
                        documents = [];

                        $.each(_Selection.selection(), function(index, sel){

                            if(sel.type === 'group'){

                                groups.push(sel.id);
                            }
                            else{

                                documents.push(sel.id);
                            }

                        });

                    Panels.dialogOpen(DMS.Paths.SHARE_CREATE, {groups:groups, documents:documents.join(',')}, {callbacks: { form_submit_success: function(evt, data){

                        console.log('Form submitted success', data);

                        // DMS.navigateReload();
                        Panels.dialogClose();

                        DMS.MessageBox.success('Share created', 'The document has been shared');

                    } }});
                }
                else{
                    _alert('Select at least 1 element');
                }
                
                evt.preventDefault();

                return false;
                
            },

            createGroup: function(evt, vm, groupId, isPrivate){
                
                var params = undefined;

                if(groupId){
                    params = params || {};
                    params.group_context = groupId;
                }
                
                if(isPrivate !== undefined || (evt.currentTarget && $(evt.currentTarget).data('isprivate') !== undefined)){
                    params = params || {};
                    params.isPrivate = isPrivate !== undefined ? isPrivate : $(evt.currentTarget).data('isprivate');
                }

                Panels.dialogOpen(DMS.Paths.GROUPS_CREATE, params, {callbacks: { form_submit_success: function(evt, data){

                    DMS.navigateReload();

                } }});


                evt.preventDefault();
                return false;
            },

            shareGroup: function(evt, groupId){

                Panels.dialogOpen(DMS.Paths.SHARE_CREATE, {groups:groupId}, {callbacks: { form_submit_success: function(evt, data){

                    Panels.dialogClose();

                    DMS.MessageBox.success('Share created', 'The document has been shared');

                } }});
                
                
                evt.preventDefault();

                return false;

            },
            
            unshare: function(evt){
                
                if(_Selection.isAnySelected()){

                    var documents = _Selection.selection(),
                        usable_documents = _.where(documents, {'isShareWith': true});

                        var count = usable_documents.length;
                        
                        if(count > 0){
                        
                            var count_msg = count==1 ? _Selection.first().title : _Selection.first().title + " and " + (count-1) + " other";
                            
                            DMS.MessageBox.question('Remove share', 'Remove the sharing from ' + count_msg + '?', 'Unshare!', 'Cancel', function(choice){
                                
                                if(choice){
                                    DMS.Services.Shared.remove(_.pluck(usable_documents, 'share'), function(data){
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
                
                if(module.context.filter === 'group' || docSelection.group) {
                    // no selection
                    // let's check the context
                    
                    var grp_message = 'You will make all the documents in this collection publicly available on the K-Link Network. (click outside to undo)';
                    
                    var grp_title = docSelection.name ? '"' + docSelection.name + '"' : 'Collection'; 
                    
                    if(docSelection.name){
                        grp_message = 'You will make all the documents inside "'+ docSelection.name +'" publicly available on the K-Link Network. (click outside to undo)';
                    }
                    
                    var grp_id = docSelection.group ? docSelection.group : module.context.group; 
                    
                    DMS.MessageBox.question('Publish '+ grp_title +' on K-Link Network', grp_message, 'Publish!', 'Cancel', function(choice){
                        
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
                        q_message = 'You will make '+ count +' documents publicly available on the K-Link Network. (click outside to undo)',
                        q_btn = 'Cancel'; 
                    
                    if(count==1){
                        q_message = 'You will make "'+ _Selection.first().title +'" publicly available on the K-Link Network. (click outside to undo)';
                        q_btn = 'Cancel';
                    }
                    
                    DMS.MessageBox.question('Publish on K-Link Network', q_message, 'Publish!', q_btn, function(choice){
                        if(choice){
                            var toPublic = _Selection.selectionByType(_Selection.Types.DOCUMENT, 'id');
                        
                            _doMakePublic({documents:toPublic});
                        }
                        else {
                            // _doMakePublic({documents:toPublic}, true);
                            DMS.MessageBox.close();
                            
                        }
                    }); 
                }
                
                else {
                    DMS.MessageBox.warning('Publish on K-Link Network', 'You cannot publish the whole list of documents, please select the documents to make available in the K-Link Netwrok.');
                }
                
                return false;
            },

            deleteGroup: function(evt, groupId, groupname){
                
                var deleteTitle, deleteMessage;

                if(groupname){
                    deleteTitle = 'Delete "'+ groupname.trim()+ '"?';
                    deleteMessage = 'You\'re about to delete "'+ groupname.trim()+ '". This will delete only the collection and will remove it from the documents. The documents will not be deleted.';
                }
                else {
                    deleteTitle = 'Delete Collection?';
                    deleteMessage = 'You\'re about to delete the selected Collection. This will delete only the collection and will remove it from the documents. The documents will not be deleted.';
                }

                

                DMS.MessageBox.deleteQuestion(deleteTitle, deleteMessage, function(isConfirmed){

                    if(isConfirmed){

                        DMS.MessageBox.wait('deleting...', '...');


                        DMS.Services.Bulk.remove({groups:groupId, context:module.context.filter}, function(data){

                            if(data.status && data.status === 'ok'){

                                DMS.MessageBox.success('Deleted', data.message);
                                
                                if(module.context.filter===CONTEXT_GROUP && groupId==module.context.group){
                                    DMS.navigate(DMS.Paths.DOCUMENTS);
                                }
                                else {
                                    DMS.navigateReload();
                                }

                                

                            }
                            else if(data.message) {
                                DMS.MessageBox.error('Cannot delete collection', data.message);
                            }

                        }, function(obj, err, errText){

                            if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                DMS.MessageBox.error('Cannot delete collection', obj.responseJSON.message);
                            }
                            else if(obj.responseJSON && obj.responseJSON.error){
                                DMS.MessageBox.error('Cannot remove collection', obj.responseJSON.error);
                            }
                            else {
                                DMS.MessageBox.error('Cannot delete collection', 'Cannot delete the specified elements. Nothing has been deleted.');
                            }

                        });

                    }
                    else {
                        DMS.MessageBox.close();
                    }

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
                    DMS.MessageBox.error('Wrong move', 'The move action applies only to collections.');
                    return false;
                }
                
                if(source.groupId === target.groupId){
                    DMS.MessageBox.error('Wrong move', 'You cannot move a collection under itself.');
                    return false;
                }
                
                if(source.isprivate && !target.isprivate){
                    // from private to public
                    DMS.MessageBox.question("Make Collection visible?", "You are about to move a personal collection under an institutional collection. This will make your collection visible to all", 'Yes, Move!', 'No, Cancel', function(isConfirmed){
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
                    DMS.MessageBox.question("Make Collection personal?", "You are about to move an institutional collection under to your personal collections. This collection will not be seen by the other users", 'Yes, Move!', 'No, Cancel', function(isConfirmed){
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

                    // debugger;

                    if(count == 1){
                        deleteTitle = 'Restore "'+ elementTitle+ '"?';
                        deleteMessage = 'You\'re about to restore "'+ elementTitle+ '".';
                    }
                    else {
                        deleteTitle = 'Restore '+ count +' elements?';
                        deleteMessage = 'You\'re about to restore '+ count +' elements.';
                    }

                    

                    DMS.MessageBox.question(deleteTitle, deleteMessage, 'Yes, Restore!', 'No, Cancel', function(isConfirmed){

                        if(isConfirmed){

                            console.log(groups, documents);

                            DMS.MessageBox.wait('restoring...', '...');


                            DMS.Services.Bulk.restore({documents: documents, groups:groups, context:module.context.filter}, function(data){

                                if(data.status && data.status === 'ok'){

                                    DMS.MessageBox.success('Restored', data.message);

                                    _Selection.clearAndDestroy();

                                }
                                else if(data.message) {
                                    DMS.MessageBox.error('Cannot restore', data.message);
                                }

                            }, function(obj, err, errText){

                                if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                    DMS.MessageBox.error('Cannot restore', obj.responseJSON.message);
                                }
                                else if(obj.responseJSON && obj.responseJSON.error){
                                    DMS.MessageBox.error('Cannot restore', obj.responseJSON.error);
                                }
                                else {
                                    DMS.MessageBox.error('Cannot restore', 'Cannot restore the specified elements.');
                                }

                            });

                        }
                        else {
                            DMS.MessageBox.close();
                        }

                    });

                }
                else{
                    _alert('Select at least 1 element');
                }   

                evt.preventDefault();
                return false;
            
            },
            
            emptytrash: function(evt, vm){

                evt.preventDefault();

                var question_msg = "All the documents in the trash will be permanently deleted. This action will remove files and revision, starred, collections and shares. This action cannot be undo.",
                    documents = [],
                    groups = [],
                    data_args = {force: "1", context:module.context.filter};

                if(_Selection.isAnySelected()){
                    question_msg = "You are about to permanently delete "+ _Selection.selectionCount() +" documents. This action will remove files and revision, starred, collections and shares. This action cannot be undo.";
                                        
                }
                else {
                    _Selection.all();
                }


                var currentSelection = _Selection.selection();

                    $.each(currentSelection, function(index, sel){

                        if(sel.type === 'group'){
                            groups.push(sel.id);
                        }
                        else{
                            documents.push(sel.id);
                        }

                    });
                    
                    data_args.documents = documents;
                    data_args.groups = groups;
                
                DMS.MessageBox.question('Clean trash?', question_msg, 'Yes, Clean!', 'No, Cancel', function(isConfirmed){
                        
                    if(isConfirmed){ 
                        
                        DMS.MessageBox.wait('Emptying...', 'Please wait while the trash is being cleaned...');

                        DMS.Services.Bulk.remove(data_args, function(data){

                            if(data.status && data.status === 'ok'){

                                DMS.MessageBox.success('Deleted', data.message);

                                if(_Selection.isAnySelected()){
                                    _Selection.clearAndDestroy();
                                }
                                else {
                                    DMS.navigateReload();
                                }
                                

                            }
                            else if(data.message) {
                                DMS.MessageBox.error('Cannot delete', data.message);
                            }

                        }, function(obj, err, errText){

                            if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                DMS.MessageBox.error('Cannot delete', obj.responseJSON.message);
                            }
                            else if(obj.responseJSON && obj.responseJSON.error){
                                DMS.MessageBox.error('Cannot delete', obj.responseJSON.error);
                            }
                            else {
                                DMS.MessageBox.error('Cannot delete', 'Cannot delete the specified elements. Nothing has been deleted.');
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

                    // debugger;

                    if(count == 1){
                        deleteTitle = 'Delete "'+ elementTitle+ '"?';
                        deleteMessage = 'You\'re about to delete "'+ elementTitle+ '".';
                    }
                    else {
                        deleteTitle = 'Delete '+ count +' elements?';
                        deleteMessage = 'You\'re about to delete '+ count +' elements.';
                    }

                    

                    DMS.MessageBox.deleteQuestion(deleteTitle, deleteMessage, function(isConfirmed){

                        if(isConfirmed){

                            console.log(groups, documents);

                            DMS.MessageBox.wait('deleting...', '...');


                            DMS.Services.Bulk.remove({documents: documents, groups:groups, context:module.context.filter}, function(data){

                                if(data.status && data.status === 'ok'){

                                    DMS.MessageBox.success('Deleted', data.message);

                                    _Selection.clearAndDestroy();

                                }
                                else if(data.message) {
                                    DMS.MessageBox.error('Cannot delete', data.message);
                                }

                            }, function(obj, err, errText){

                                if(obj.responseJSON && obj.responseJSON.status === 'error'){
                                    DMS.MessageBox.error('Cannot delete', obj.responseJSON.message);
                                }
                                else if(obj.responseJSON && obj.responseJSON.error){
                                    DMS.MessageBox.error('Cannot delete', obj.responseJSON.error);
                                }
                                else {
                                    DMS.MessageBox.error('Cannot delete', 'There was a problem deleting the document, please contact an Administrator.');
                                }

                            });

                        }
                        else {
                            DMS.MessageBox.close();
                        }

                    });

                }
                else{
                    _alert('Select at least 1 element');
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

                _alert("Oops, Something went wrong!", obj.responseText, "error");

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
                    $this.parent().siblings().addClass('collapsed').removeClass('expanded');
                    $this.addClass('collapsed').removeClass('expanded');
                }
                else {
                    //expand

                    $this.data('expanded', true);
                    $this.parent().siblings().addClass('expanded').removeClass('collapsed');
                    $this.addClass('expanded').removeClass('collapsed');
                }

                //only first child

                evt.preventDefault();

                return false;
            },

            expandOrCollapseAll: function(evt, vm){
                
                if(module.groups.isExpandedAll){
                    // collapse all
                    module.groups.isExpandedAll = false;

                    this.innerText = $(this).data('collapsed');

                    _treeView.find('.expanded').addClass('collapsed').removeClass('expanded');
                    _treeView.find('.tree-chevron').data('expanded', false);

                }
                else {
                    // expandAll

                    module.groups.isExpandedAll = true;
                    this.innerText = $(this).data('expanded');
                    _treeView.find('.collapsed').addClass('expanded').removeClass('collapsed');
                    _treeView.find('.tree-chevron').data('expanded', true);
                }

                evt.preventDefault();

                return false;
            },
            
            ensureCurrentVisibility: function(){
                var parents = _treeView.find('.current').parents('.tree-childs');
                parents.addClass('expanded').removeClass('collapsed');
                parents.find('.tree-chevron').data('expanded', true);
            },

            showEdit: function(evt, vm){

                var that = $(this),
                    data = that.data(),
                    id = data.groupId;

                Panels.dialogOpen(DMS.Paths.GROUPS_EDIT.replace('{ID}', id), {}, {
                    callbacks: { 
                        form_submit_success: function(evt, data){

                                console.info("Group updated", data);

                                // document.getElementById('document-tree').innerHTML = data;

                                //TODO: update the data somehow

                                Panels.dialogClose();

                                DMS.navigateReload();

                            }}});

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
            
            _bindPageArea.sync();
            
            evt.preventDefault();
            
        },
        
        
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


	
    _bindPageArea = _rivets.bind(_pageArea, module);
    _bindActionBar = _rivets.bind(_actionBar, module.menu);
    
//    _filterBind = _rivets.bind(_filtersArea, filters_module);

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

        _context.attach(_documentArea, '.item', [
            {
                text: "Details",
                action: function(e){
                    e.preventDefault();
    
                    if(_Selection.selectionCount() > 1){
    
                        DMS.MessageBox.error('Multiple Selection', 'The details view currently don\'t support multiple selection');
                        return false;
                    }
    
                    module.select.call(this, e, this);
    
    
                }
            },
            {
                text: "Share",
                action: function(e){
                    if(!_Selection.isSelect(this, true)){
                        _Selection.select(this, true);
                    }
                    module.menu.share(e, this);
                },
                icon: 'icon-action-black icon-action-black-ic_exit_to_app_black_24dp'
            },
            {
                text: "Make Public",
                action: function(e){ 
                    if(!_Selection.isSelect(this, true)){
                        _Selection.select(this, true);
                    }
                    module.menu.makePublic(e, this);
                },
                icon: 'icon-social-black icon-social-black-ic_public_black_24dp'
            },
            {
                divider: true,
            },
            {
                text: "Edit",
                action: function(e){
    
                    e.preventDefault();
    
                    if(_Selection.selectionCount() > 1){
    
                        DMS.MessageBox.error('Multiple Selection', 'The edit action is not available on multiple selection');
                        return false;
                    }
    
                    var id = this.data('id');
    
                    DMS.Services.Documents.openEditPage(id);
                },
                icon: 'icon-content-black icon-content-black-ic_create_black_24dp'
            },
            {
                text: "Delete",
                action: function(e){
                    if(!_Selection.isSelect(this, true)){
                        _Selection.select(this, true);
                    }
                    module.menu.del(e, this);
                }
            }
        ]);
    
    
    
        _context.attach(_treeView, '.groups-menu', [
            {
                text: "Edit",
                action: module.groups.showEdit,
                icon: 'icon-content-black icon-content-black-ic_create_black_24dp'
            },
            {
                text: "Delete",
                action: function(e){ 
                    var id = this.data('groupId'),
                        anchor = this.hasClass('tree-item-inner') ? this : this.find('.tree-item-inner');
                    module.menu.deleteGroup(e, id, anchor ? anchor[0].innerText || anchor[0].textContent : undefined);

                },
            },
            {
                divider: true,
            },
            {
                text: "Create sub-Collection",
                action: function(e){ 
                    var id = this.data('groupId'),
                        isPrivate = this.data('isprivate');
                    module.menu.createGroup(e, id, id, isPrivate);
                },
                icon: 'icon-content-black icon-content-black-ic_add_black_24dp'
            },
            {
                divider: true,
            },
            {
                text: "Share",
                action: function(e){ 
                    var id = this.data('groupId');
                    module.menu.shareGroup(e, id);
                },
                icon: 'icon-action-black icon-action-black-ic_exit_to_app_black_24dp'
            },
            {
                text: "Publish Documents",
                action: function(e){ 
                    var id = this.data('groupId'),
                        name = this.find('.tree-item-inner').first().text().trim();
    
                    module.menu.makePublic(e, {group: id, name: name});
                },
                icon: 'icon-social-black icon-social-black-ic_public_black_24dp'
            }
        ]);

    }
    

    function _initUploadService(){

        if(_modernizr.filereader && _modernizr.draganddrop){

            _require(["modules/dropzone"], function(_dropzone){

                _dropzone.autoDiscover = false;
                
            	var dropzone = new _dropzone('#documents-list', { // Make the whole body a dropzone
            	    url: DMS.Paths.fullUrl(DMS.Paths.DOCUMENTS), // Set the url
            	    paramName: "document",
                    createImageThumbnails: false,
                    filesizeBase:1024,
            	    previewsContainer: "#previews", // Define the container to display the previews
            	    clickable: "#upload_trigger", // Define the element that should be used as click trigger to select files.
                    // acceptedFiles: 'image/*,application/pdf,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.wordprocessingml.document',

                    addRemoveLinks:true,
                    dictCancelUpload:'',
                    previewTemplate: '<div class="dz-preview dz-file-preview"><div class="dz-details"><div class="dz-filename"><span data-dz-name></span></div><div class="dz-size" data-dz-size></div><img data-dz-thumbnail /></div><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-success-mark"><span>✔</span></div><div class="dz-error-mark"><span>✘</span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div>',

            	    // uploadMultiple: true,
            		parallelUploads: 1,
                    maxFilesize:100000,
            		maxFiles: 10000,
                    
                    headers: {
                      "X-CSRF-TOKEN" : DMS.csrf()
                    },
                    
                   accept: function(file, done) {
                       
                       if(!file.type && file.size%4096 == 0){
                           // Firefox way is so different than the others that I don't support it
                           done("Your browser don't support folder drag and drop.");
                       }
                       else {
                           done();
                       }
                   },

                    error: function(file, message) {
                        var node, _i, _len, _ref, _results;
                        if (file.previewElement) {
                          file.previewElement.classList.add("dz-error");
                          if (typeof message !== "String" && message.error) {
                            message = message.error;
                          }
                          if (typeof message !== "String" && message.document) {
                            message = _.isArray(message.document) ? message.document.join(",") : message.document;
                          }
                          _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                          _results = [];
                          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                            node = _ref[_i];
                            _results.push(node.textContent = message);
                          }
                          return _results;
                        }
                      },

            	    init: function () {
                            this.on("error", function (file, response, xhr) {

                                console.error("File upload error", file, response, xhr);

                                module.uploads.status = "error";

                                var msg = "";
                                if(response.error){
                                    msg = response.error;
                                }
                                else if(response.document){
                                    msg = response.document;
                                }
                                else {
                                    msg = xhr.responseText;
                                }

                                DMS.MessageBox.error('File Upload error', msg);
                            });

                            this.on("success", function (file, response) {

                                console.log("File upload success", file, response);

                            });

                            this.on("addedfile", function(file){

                                $('#upload-status').addClass('visible');

                            	module.uploads.isUploading = true;
                            	module.uploads.totalFiles = this.files.length;

                            	module.uploads.status = "uploading";

                            });

                            this.on("removedfile", function(file){

                                module.uploads.totalFiles = this.files.length;

                                if(module.uploads.totalFiles==0){

                                    $('#upload-status').removeClass('visible');
                                }

                            });

                            this.on("queuecomplete", function(){

                                if(module.uploads.status === 'error'){
                                	module.uploads.isUploading = false;
                                }
                                else {
                                    module.uploads.isUploading = false;
                                    module.uploads.status = "completed";
                                    $('#upload-status').removeClass('visible');
                                    DMS.navigateReload();
                                    _alert("All the files have been successfully uploaded.", "Have a nice search.", "success");
                                }

                            });

                            this.on("totaluploadprogress", function(uploadProgress, totalBytes, totalBytesSent){
                            	module.uploads.percentage = uploadProgress;

                            });

                            this.on("maxfilesreached", function(){
                            	_alert("Sorry, but you have to wait a little", "We can process only a little amount of file, so please have a little patience before adding another file.", "error");
                            });
                        }
            	  });

            	dropzone.on("sending", function(file, xhr, formData) {

                    //file.name //contiene solo il nome del file
                    //file.fullPath //contiene il nome della cartella se disponibile

                    var folder = file.fullPath ? file.fullPath.replace(file.name, '').trim() : '';

                    formData.append("document_fullpath", file.fullPath);
                    formData.append("document_name", file.name);
                    if(folder.length > 0){
                        formData.append("folder_path", folder);
                    }

              		formData.append("_token", DMS.csrf());

                    if(module.context.filter && module.context.filter === CONTEXT_GROUP ){

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
    
    
    
    
    /// For the map visualization
    
    
	var map = undefined, 
	    _mapInstance = undefined;
//		_mapTemplateBinding = undefined;
		
		
//	_mapTemplateBinding = _rivets.bind( $("#map"), _map_vm);
	
	
	/** Used for getting something from the map callbacks */
    function callback_filter(filter_values)
    { 
	  module.map.elements = filter_values;
	  
	  _bindPageArea.sync();
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
		
		var getParams = $.extend({s: module.context.search, filter: module.context.filter, visibility:module.context.visibility}, module.context.facets);
		
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
		
	}
	
	_documentArea.on('dms:unloadmap', function(evt){
		
		if(_mapInstance){
			map.remove();
			map = undefined;
			$("#map").removeClass('visible');
		}
	});
	
	_documentArea.on('dms:loadmap', function(evt){
        
        if(module.context.filter === 'starred' || module.context.filter === 'trash' || module.context.filter ==='shared'){
            DMS.MessageBox.warning('Map not ready yet for "' + module.context.filter + '"', 'The map cannot be currently showed on the current page.');
            return false;
        }
		
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
