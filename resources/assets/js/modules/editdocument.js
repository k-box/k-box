define("modules/editdocument", ["require", "modernizr", "jquery", "DMS", "sweetalert", "modules/minimalbind", "modules/share", "modules/panels" ], function (_require, _modernizr, $, DMS, _alert, _rivets, Share, Panels) {
    
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


    $('.js-open-share-dialog').on('click', function(evt){

        evt.preventDefault();
        evt.stopPropagation();
        var id = $(evt.target).data('id');
        Share.open([{
            id: id,
            type: 'document'
        }]);

    });
    
    $('[data-action="showLicenseHelp"]').on('click', function(evt){

        evt.preventDefault();
        evt.stopPropagation();

        Panels.openAjax('help-licenses', this, DMS.Paths.LICENSE_HELP, {}, {});
        
    });

    $('[data-action=restoreVersion').on('click', function(evt) {

        var data = $(evt.target).data();
        
        DMS.MessageBox.question(
            Lang.trans('documents.restore.restore_dialog_title', {document: data.versionTitle}),
            Lang.trans('documents.restore.restore_version_dialog_text', {document: data.versionTitle}), 
            Lang.trans("documents.restore.restore_dialog_yes_btn"), 
            Lang.trans("documents.restore.restore_dialog_no_btn"), function(restore){

            if(restore){
                
                DMS.MessageBox.wait( Lang.trans('actions.restoring'), '...');


                DMS.Services.Documents.restoreVersion(
                    data.documentId, 
                    data.versionId,
                    function (data) {

                        if(data.status && data.status === 'ok'){

                            DMS.navigateReload();

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
                            DMS.MessageBox.error(Lang.trans('documents.restore.restore_error_title'), Lang.trans('documents.restore.restore_version_error_text_generic'));
                        }

                    }
                );


            }

        });
        
        return false;
    });

});
