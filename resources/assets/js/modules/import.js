define("modules/import", ["jquery", "DMS", "modules/minimalbind", "sweetalert"], function ($, _DMS, _rivets, _alert) {

    //used to adjust the time between the refreshes
    var refresh_download_delay = 2000, //milli
        _refreshTimer = null,
        _rivets_bind = null,
        _cache = false,
        _importEl = $("#import"),
        _cleaBtn = $('#action-bar');
    
    // _rivets.binders.width = function(el, value){
    //     el.style.width = value;
    // }

    // _rivets.binders.importclass = function(el, value) {
        
    //     var $el = $(el);

    //     if($el.data("_c")){
    //         $el.removeClass("import-" + $el.data("_c"));
    //     }
        
    //     $el.addClass("import-" + value);

    //     $el.data("_c", value);

    // };

    // _rivets.binders.statusclass = function(el, value) {
    //     var $el = $(el);

    //     if($el.data("_c")){
    //         $el.removeClass("status-" + $el.data("_c"));
    //     }
        
    //     $el.addClass("status-" + value);

    //     $el.data("_c", value);
    // };

    // _rivets.formatters.percentage = function(value,total) {
    //     return value==null ? '0%' : total===0 ? "0%" : Math.round(value*100/total)+"%";
    // };

    // _rivets.formatters.default = function(value, _default) {
    //     return value ? value : _default;
    // };


    
    //object to be binded to the view
    var _status = {

        global: "Loading status...",
        details: "...",
        progress_percentage: "0%"

    };

    //{file: {name: 'ciao', original_uri:'jbfdubfdu', mime_type:'prova'}, status_message: 'queued', created_at:'4555', bytes_expected:10, bytes_received:2, is_remote:false}

    var _import_list = {elements : []};


    function _handleFormSubmit(form, vm){

        var old_status = vm.status.global,
            old_details = vm.status.details,
            old_importing = vm.isImporting;

        vm.status.global = "Preparing import...";
        vm.status.details = "Waiting for response...";
        vm.isImporting = true;
        vm.canImport = false;

        _updateUI();
        
        var from = form.from.value;
        
        if(from == undefined){
            from = $(form.from).val();
        }
        
        _DMS.Ajax.post(_DMS.Paths.IMPORT, {
            from: from,
            remote_import: form.remote_import.value,
            folder_import: form.folder_import.value,
        }, function(data){
            
            //ok the server got the correct request

            vm.canImport = true;

            _applyUpdate(data);

        },function(obj, status, statusText){
            
            if(obj.status === 422){

                if(obj.responseJSON){

                    if(obj.responseJSON.remote_import){

                        _alert("Cannot start remote import", $.isArray(obj.responseJSON.remote_import) ? obj.responseJSON.remote_import[0] : obj.responseJSON.remote_import, "error");

                    }
                    else if(obj.responseJSON.folder_import){
                        _alert("Cannot start folder import", $.isArray(obj.responseJSON.folder_import) ? obj.responseJSON.folder_import[0] : obj.responseJSON.folder_import, "error");
                    }

                }
                else {

                    _alert("Cannot start import", obj.responseText, "error");

                }

            }
            else {
                console.error("Import POST error", obj, statusText);

                _alert("Something went wrong!", obj.responseText, "error");
            }

            vm.canImport = true;

            vm.status.global = old_status;
            vm.status.details = old_details;
            vm.isImporting = old_importing;

            _updateUI();
        });
        
        return false;

    }


    function _applyUpdate(data){
        if(data && data.status.global){
            import_module.status.global = data.status.global;
            import_module.status.details = data.status.details;
            import_module.status.progress_percentage = data.status.progress_percentage;

            import_module.imports.elements = data.imports;

            if(_rivets_bind){
                _rivets_bind.sync();
            }

            if(_cache){
                _importEl.find(".cache").addClass("hidden");
                _cache = false;
            }

            if(data.imports_total != data.imports_completed){
                _refreshTimer = setTimeout(function(){

                     _checkUpdates();

                 }, refresh_download_delay); //So we can vary the timer if import are slower than tought
            }

            if(data.imports_completed > 0){
                import_module.enableClearCompleted();
            }
        }
    }


    function _checkUpdates(){

        _DMS.Ajax.get(_DMS.Paths.IMPORT,{},function(data){

            _applyUpdate(data);

        }, function(err){
            //error

            _refreshTimer = setTimeout(function(){

                 _checkUpdates();

             }, refresh_download_delay*10);

        });

    }

    function _updateUI(){
        _rivets.refreshAll();
    }


    var import_module = {

        status: _status,

        imports: _import_list,

        canImport: true,

        cannotClear: true,

        isImporting: false,

        panel: {
            remote: true,
            folder: false
        },

        showRemotePanel: function(evt, vm){
            vm.panel.remote = true;
            vm.panel.folder = false;
            _updateUI();
        },

        showFolderPanel: function(evt, vm){
            vm.panel.folder = true;
            vm.panel.remote = false;
            _updateUI();
        },

        doImport: function(evt, vm){

            evt.stopPropagation();
            evt.preventDefault();

            _handleFormSubmit(evt.target, vm);

            return false;
        },



        startUiUpdate: function(global_msg, detail_msg, percentage){
            console.warn("told to start check for status updates");

            _cache = true;

            import_module.isImporting = true;

            import_module.status.global = global_msg;
            import_module.status.details = detail_msg;

            if(percentage){
                import_module.status.progress_percentage = percentage;
            }

            _updateUI();

            _refreshTimer = setTimeout(function(){
                 _checkUpdates();
             }, refresh_download_delay);
        },

        stopUiUpdate: function(){
            if(_refreshTimer){
                clearTimeout(_refreshTimer);
            }
        },

        clearCompleted: function(evt, vm){
            if(vm.cannotClear){
                _alert('Nothing to clear');
                evt.preventDefault();
                return ;
            }

            _importEl.find('.import-completed').hide();

            _DMS.Ajax.put(_DMS.Paths.IMPORT + '/clearcompleted', {}, function(data_ok){

                vm.cannotClear = true;

                _updateUI();

            }, function(obj, status, statusText){

                _alert("Oops!", 'Completed list cannot be cleared. ' + statusText, "error");

            });

            evt.preventDefault();
        },

        enableClearCompleted: function(){
            import_module.cannotClear = false;
            _updateUI();
        }
    }


    _rivets_bind = _rivets.bind(_importEl, import_module);

    _rivets.bind(_cleaBtn, import_module);


        // console.log(_rivets_bind);

	return import_module;
});