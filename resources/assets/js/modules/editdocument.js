define("modules/editdocument", ["require", "modernizr", "jquery", "DMS", "sweetalert", "modules/minimalbind" ], function (_require, _modernizr, $, DMS, _alert, _rivets) {
    
	console.log('loading EDIT document-page module...');

	// var _selected = [], //key ID value object info
 //        _isSelecting = false,
 //        _lastSelectedItem = undefined,
 //        _pageArea = $("#page"),
 //        _documentArea = $("#document-area"),
 //        _actionBar = $("#action-bar"),
 //        _treeView = $("#document-tree"),
 //        _bindActionBar = undefined,
 //        _bindPageArea = undefined;


    $("#visibility").on('change', function(evt){
        if(this.checked){
            $(this).parent().find('.description').removeClass('hidden');
        }
        else {
            $(this).parent().find('.description').addClass('hidden');
        }
    });

    $("#edit-form").on('submit', function(evt){
        DMS.Progress.start();

        $('.ladda-button').addClass('processing');
    });

    $("#document").on('change', function(evt){


        // $("#document").trigger('click');

        document.getElementById('edit-form').submit();

        $('#button').addClass('processing');

        DMS.Progress.start();

        // evt.preventDefault();
        // return false;
    });

});
