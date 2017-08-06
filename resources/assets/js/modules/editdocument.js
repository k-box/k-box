define("modules/editdocument", ["require", "modernizr", "jquery", "DMS", "sweetalert", "modules/minimalbind" ], function (_require, _modernizr, $, DMS, _alert, _rivets) {
    
    console.log('loading EDIT document-page module...');
    
    var fileInput = $("#document"),
        uploadNew = $("#upload_new");


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

        $('.save-button').addClass('processing');
    });

    fileInput.on('change', function(evt){

        document.getElementById('edit-form').submit();

        uploadNew.addClass('processing');

        DMS.Progress.start();
    });

});
