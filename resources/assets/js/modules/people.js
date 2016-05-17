define("modules/people", ["require", "modernizr", "jquery", "DMS", "modules/minimalbind", "context", "lodash", "sweetalert", "language" ], function (_require, _modernizr, $, DMS, _rivets, _context, _, _alert, Lang) {


	var _pageArea = $(document),
        _bind = undefined;


    ////////////////////////////
    // For multiple selection //
    ////////////////////////////

//    _Selection.init(_documentArea, {
//        tristateButton: _actionBar.find('.selection-button'),
//        selectionBoundingElement: '.selection',
//        selectionCheckbox: '.checkbox'
//    });

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


    droppables.on('dragover', '.group-box', function(evt){

        var that = $(this);
        that.addClass("dragover");

        if (evt.preventDefault) {
            evt.preventDefault(); 
        }

        evt.originalEvent.dataTransfer.dropEffect = 'copy';

        return false;

    });

    droppables.on('dragenter', '.group-box', function(evt){

        // the only reason is to block the event

        if (evt.preventDefault) {
            evt.preventDefault(); 
        }

        return false;

    });

    droppables.on('dragleave', '.group-box', function(evt){

        $(this).removeClass("dragover");

        if (evt.preventDefault) {
            evt.preventDefault(); 
        }

        // console.log('dragleave', this);

        return false;

    });

    droppables.on('drop', '.group-box', function(evt){

        var $this = $(this),
            groupId = $this.data('id');

        $this.removeClass("dragover");

        // stops the browser from redirecting off to the text.
        if (evt.preventDefault) {
            evt.preventDefault(); 
        }

        var attached_data = evt.originalEvent.dataTransfer.getData('text');

        var user = _getUser(attached_data),
            group = _getGroupById(groupId);
        
        if(!user){
            DMS.MessageBox.error(Lang.trans('groups.people.cannot_add_user_dialog_title'), Lang.trans('groups.people.cannot_add_user_dialog_text'));
            return false;
        }
        
        var already_there = _.where(group.people, user);
        
        if(already_there && already_there.length > 0){
            DMS.MessageBox.warning( Lang.trans('groups.people.user_already_exists', {name: user.name}), '');
            return false;
        }
        
        
        group.people.push(user);
        group.saving = true;
                    
        _updateBinds();
                    
        DMS.Services.PeopleGroup.addUser(group.id, user.id, function(res){
            if(res.status && res.status==='ok'){
                //good
            }
            else {
//                group.name = old;
            }
            group.saving = false;
            _updateBinds();
        }, function(obj, err, errText){
           // real error 
//           group.name = old;
           group.saving = false;
           _updateBinds();
           _outputError(Lang.trans('groups.people.cannot_add_user_dialog_title'), obj);
        });


    });

    dragItems.on('dragstart', function(evt){

         //raises checked of undefined
        evt.originalEvent.dataTransfer.effectAllowed = 'all';
        evt.originalEvent.dataTransfer.setData('text', $(this).data('id'));
        
    });

    dragItems.on('dragend', function(evt){


    });




   function _getGroup(element){
       var el = element.parents('.group-box');
       
       if(el.length > 0){
           var id = el.data('id');
           return _getGroupById(id);           
       }
       else {
           return undefined;
       }
       
   }
   
   function _getGroupById(id){
       var int_id = parseInt(id,10); 
       
       var by_int = _.where(module.details.groups, { 'id': int_id });
        
        var by_string = _.where(module.details.groups, { 'id': ""+id });
       
        var found = _.union(by_int, by_string);
        return _.first(found);
   }
   
   function _groupAlreadyExixtsByName(name){ 
       
       var found = _.first(_.where(module.details.groups, { 'name': name }));
       
       return found && found.id > -1;
   }
   


   function _getUser(id){
        
        var int_id = parseInt(id,10); 
   
        var by_int = _.where(module.details.users, { 'id': int_id });
        
        var by_string = _.where(module.details.users, { 'id': ""+id });
       
        var found = _.union(by_int, by_string);
       
        return _.first(found);
       
   }
   
   function _outputError(title, obj){
       var message = 'There was a problem fullfilling your request';
       
       if(obj.responseJSON && obj.responseJSON.status){
           message = obj.responseJSON.status;
       }
       else {
           if(obj.status==403){
                message = 'You don\'t have permission to create the group';
           }
           else if(obj.status==422){
               message = 'Some parameters have a wrong value';
           }
       }
       
       DMS.MessageBox.error(title, message);
       
   }

	var module = {
        details: {
            groups: [],
            users: [],
        },
        
        
        data: function(groups, users){
            
          
            module.details.groups = groups;
            module.details.users = users;
            
            _updateBinds();
        },
        
        
        createGroup: function(evt, vm){
            
            DMS.MessageBox.prompt(Lang.trans('groups.people.create_group_dialog_title'), Lang.trans('groups.people.create_group_dialog_text'), Lang.trans('groups.people.create_group_dialog_placeholder'), function(inputValue){
                
                console.info(inputValue);
                
                if(_groupAlreadyExixtsByName(inputValue)){
                    _alert.showInputError( Lang.trans('groups.people.group_name_already_exists'));
                    return false;   
                }
                
                var group = {
                    name: inputValue,
                    id:0,
                    people:[],
                    is_institution_group:false,
                    saving:true,
                }
                
                
                    
                module.details.groups.push(group); 
                    
                _updateBinds();
                
                DMS.MessageBox.close();
                    
                DMS.Services.PeopleGroup.addGroup(group.name, function(res){
                    if(res.status && res.status==='ok'){
                        //good
                        group.id = res.group.id;
                        group.saving = false;
                    }
                    else {
                        DMS.MessageBox.error( Lang.trans('groups.people.create_group_error_title'), res.status ? res.status : Lang.trans('groups.people.create_group_generic_error_text'));
                        module.details.groups = _.filter(module.details.groups, function(i){ return i.id !== 0; });
                    }
                    
                    _updateBinds();
                }, function(obj, err, errText){
                   // real error 
                   module.details.groups = _.filter(module.details.groups, function(i){ return i.id !== 0; });
                   _updateBinds();
                   
                   _outputError(Lang.trans('groups.people.create_group_error_title'), obj);
                });
                
            });
            
            evt.preventDefault();
        },
        
        renameGroup: function(evt, vm){
            var that = $(this),
                group = _getGroup(that);
                
            DMS.MessageBox.prompt(Lang.trans('groups.people.rename_dialog_title', {name: group.name}), Lang.trans('groups.people.rename_dialog_text'), group.name, function(inputValue){
                
                // console.info(inputValue);
                
                if(_groupAlreadyExixtsByName(inputValue)){
                    _alert.showInputError(Lang.trans('groups.people.group_name_already_exists'));
                    return false;   
                }
                
                var old = group.name; 
                
                group.name = inputValue;
                group.saving = true;
                
                DMS.MessageBox.close();
//                    module.details.groups = _.filter(module.details.groups, function(i){ return i.id !== group.id; }); 
                    
                    _updateBinds();
                    
                    DMS.Services.PeopleGroup.renameGroup(group.id, group.name, function(res){
                        if(res.status && res.status==='ok'){
                            //good
                        }
                        else {
                            group.name = old;
                            DMS.MessageBox.error(Lang.trans('groups.people.rename_error_title'), res.status ? res.status : Lang.trans('groups.people.rename_generic_error_text'));
                        }
                        group.saving = false;
                        _updateBinds();
                    }, function(obj, err, errText){
                       // real error 
                       group.name = old;
                       group.saving = false;
                       _updateBinds();
                       
                       _outputError(Lang.trans('groups.people.rename_error_title'), obj);
                    });
                
            });
            evt.preventDefault();
        },
        
        makeInstitutional: function(evt, vm){
            var that = $(this),
                group = _getGroup(that);
                
            DMS.MessageBox.question('Make "' + group.name +'" Institutional?', 'The group ' + group.name +' will be visible to other content managers inside the institution.',  'Continue', 'Cancel', function(selection){
                if(selection){
                    
                    // module.details.groups = _.filter(module.details.groups, function(i){ return i.id !== group.id; }); 
                    group.is_institution_group = true;
                    group.saving = true;
                    _updateBinds();
                    
                    DMS.Services.PeopleGroup.makeInstitutional(group.id, function(res){
                        if(res.status && res.status==='ok'){
                            DMS.MessageBox.close();
                        }
                        else {
                            group.is_institution_group = false;
                            DMS.MessageBox.error('The group cannot be converted to institutional', res.status ? res.status : 'The group cannot be converted to an institutional group.');
                        }
                        
                       group.saving = false;
                        _updateBinds();
                    }, function(obj, err, errText){
                       // real error 
                       
                       
                       group.is_institution_group = false;
                       group.saving = false;
                       _updateBinds();
                       
                       _outputError('Make Institutional', obj);
                    });
                }
                else {
                    DMS.MessageBox.close();
                }
            }, true, true);
            evt.preventDefault();
        },
        
        makePersonal: function(evt, vm){
            var that = $(this),
                group = _getGroup(that);
                
            DMS.MessageBox.question('Make "' + group.name +'" Personal?', 'The group ' + group.name +' will be visible only to you, previous share will be removed.', 'Continue', 'Cancel', function(selection){
                if(selection){
                    
                    // module.details.groups = _.filter(module.details.groups, function(i){ return i.id !== group.id; }); 
                    group.is_institution_group = false;
                    group.saving = true;
                    _updateBinds();
                    
                    DMS.Services.PeopleGroup.makePersonal(group.id, function(res){
                        if(res.status && res.status==='ok'){
                            DMS.MessageBox.close();
                        }
                        else {
                            group.is_institution_group = true;
                            DMS.MessageBox.error('The group cannot be make personal', res.status ? res.status : 'The group cannot be make personal.');
                        }
                        
                       group.saving = false;
                        _updateBinds();
                    }, function(obj, err, errText){
                       // real error 
                       
                       
                       group.is_institution_group = true;
                       group.saving = false;
                       _updateBinds();
                       
                       _outputError('Make Institutional', obj);
                    });
                }
                else {
                    DMS.MessageBox.close();
                }
            }, true, true);
            evt.preventDefault();
        },
        
        deleteGroup: function(evt, vm){
            var that = $(this),
                group = _getGroup(that);
                
            DMS.MessageBox.deleteQuestion( Lang.trans('groups.people.delete_dialog_title', {name: group.name}), Lang.trans('groups.people.delete_dialog_text', {name: group.name}), function(selection){
                if(selection){
                    
                    module.details.groups = _.filter(module.details.groups, function(i){ return i.id !== group.id; }); 

                    _updateBinds();
                    
                    DMS.Services.PeopleGroup.removeGroup(group.id, function(res){
                        if(res.status && res.status==='ok'){
                            
                        }
                        else {
                            module.details.groups.push(group);
                            DMS.MessageBox.error( Lang.trans('groups.people.delete_error_title'), res.status ? res.status : Lang.trans('groups.people.delete_generic_error_text'));
                        }
                        
//                        group.saving = false;
                        _updateBinds();
                    }, function(obj, err, errText){
                       // real error 
                       
                       
                       module.details.groups.push(group);
                       _updateBinds();
                       
                       _outputError(Lang.trans('groups.people.delete_error_title'), obj);
                    });
                }
            }, true, true);
            evt.preventDefault();
        },
        
        removeUserFromGroup: function(evt, vm){
            
            var that = $(this),
                uid = that.data('uid'),
                uname =that.data('uname'),
                user = _getUser(uid),
                group = _getGroup(that);
                
            DMS.MessageBox.deleteQuestion(Lang.trans('groups.people.remove_user_dialog_title', {name: uname}), Lang.trans('groups.people.remove_user_dialog_text', {name: uname, group: group.name}), function(selection){
                if(selection){
                    
                    group.people = _.filter(group.people, function(i){ return i.id !== uid; }); 
                    group.saving = true;
                    
                    _updateBinds();
                    
                    DMS.Services.PeopleGroup.removeUser(group.id, uid, function(res){
                        if(res.status && res.status==='ok'){
                            //good
                        }
                        else {
                            group.people.push(user);
                        }
                        group.saving = false;
                        _updateBinds();
                        
                    }, function(obj, err, errText){
                       // real error 
                       group.people.push(user);
                       group.saving = false;
                       _updateBinds();
                       _outputError(Lang.trans('groups.people.remove_user_error_title'), obj);
                    });
                }
            }, true, true);
              
            evt.preventDefault();
        }
	};


	
    _bind = _rivets.bind(_pageArea, module);

    function _updateBinds(){

        if(_bind){
            _bind.sync();
        }
        
    }




	return module;
});
