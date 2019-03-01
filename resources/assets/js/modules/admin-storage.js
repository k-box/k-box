define("modules/admin-storage", ["jquery", "DMS", "modules/minimalbind", "sweetalert"], function ($, _DMS, _rivets, _alert) {
    
	console.info('Admin Storage module initialization...');
        
    //used to adjust the time between the refreshes
    var refresh_download_delay = 1000, //milli
        _refreshTimer = null,
        _rivets_bind = null,
        _action_bind = null,
        _cache = false,
        _reindexEl = $("#reindex"),
        _reindexBtn = $('#storageActions');

        
        //object to be binded to the view
        var _status = {

            status: "Loading status...",
            pending: "0",
            completed: "0",
            total: "0",
            progress_percentage: "0%"

        };


        function _handleFormSubmit(form, vm){
            vm.status.status = "Preparing Reindexing All documents...";
            vm.isReindexing = true;
            vm.cannotReindex = true;

            if(_rivets_bind){
                _rivets_bind.sync();
                _action_bind.sync();
            }

            _DMS.Ajax.post(_DMS.Paths.STORAGE_REINDEX_ALL, {}, function(data){
                
                _applyUpdate(data);

            },function(obj, status, statusText){
                
                if(obj.status === 422){

                    if(obj.responseJSON){                        

                        _alert("Cannot start the reindexing procedure", $.isArray(obj.responseJSON) ? obj.responseJSON.join(',') : obj.responseJSON, "error");

                    }
                    else {

                        _alert("Cannot start the reindexing procedure", obj.responseText, "error");

                    }

                }
                else {
                    debugger;
                    console.error("Reindexing POST error", obj, statusText);

                    _alert("Something went wrong!", obj.responseText, "error");
                }

                vm.isReindexing = false;
                vm.cannotReindex = false;

                if(_rivets_bind){
                    _rivets_bind.sync();
                    _action_bind.sync();
                }
            });
            
            return false;

        }


        function _applyUpdate(data){

            // console.log(data);

            if(data && data.status){

                storage_module.status = data;

                if(data.error){
                    storage_module.status.status = data.error;
                }

                if(_rivets_bind){
                    _rivets_bind.sync();
                    // _action_bind.sync();
                }

                if(data.completed != data.total){
                    _refreshTimer = setTimeout(function(){

                         _checkUpdates();

                     }, refresh_download_delay); //So we can vary the timer if import are slower than tought
                }


                if(data.completed == data.total){
                    // storage_module.isReindexing = false;
                    storage_module.cannotReindex = false;
                    storage_module.status.progress_percentage = 100;

                    if(_rivets_bind){
                        _rivets_bind.sync();
                        _action_bind.sync();
                    }
                }

            }
        }


        function _checkUpdates(){

            _DMS.Ajax.get(_DMS.Paths.STORAGE_REINDEX_ALL,{},function(data){
                
                _applyUpdate(data);

            }, function(err){
                //error

                _refreshTimer = setTimeout(function(){

                     _checkUpdates();

                 }, refresh_download_delay*10);

            });

        }


        var storage_module = {

            status: _status,

            cannotReindex: false,

            isReindexing: false,

            reindexAll: function(evt, vm){

                evt.stopPropagation();
                evt.preventDefault();

                _handleFormSubmit(evt.target, vm);

                return false;
            },



            startUiUpdate: function(message, percentage){

                _cache = true;

                storage_module.cannotReindex = true;

                storage_module.isReindexing = true;

                storage_module.status.status = message;
                storage_module.status.progress_percentage = percentage;

                if(_rivets_bind){
                        _rivets_bind.sync();
                        _action_bind.sync();
                    }

                _refreshTimer = setTimeout(function(){
                     _checkUpdates();
                 }, refresh_download_delay);
            },

            stopUiUpdate: function(){
                if(_refreshTimer){
                    clearTimeout(_refreshTimer);
                }
            },

            
        }


        _rivets_bind = _rivets.bind(_reindexEl, storage_module);

        _action_bind = _rivets.bind(_reindexBtn, storage_module);


	return storage_module;
});