define("modules/editdocument", ["require", "modernizr", "jquery", "DMS", "sweetalert", "modules/minimalbind", "modules/share", "modules/panels", 'language' ], function (_require, _modernizr, $, DMS, _alert, _rivets, Share, Panels, Lang) {
    
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

        evt.preventDefault();
        evt.stopPropagation();

        var data = $(evt.target).data();

        DMS.MessageBox.deleteQuestion(
            Lang.trans('documents.restore.restore_dialog_title', {document: data.versionTitle}),
            Lang.trans('documents.restore.restore_version_dialog_text', {document: data.versionTitle}), 
            {
                confirmButtonText: Lang.trans("documents.restore.restore_dialog_yes_btn"), 
                cancelButtonText: Lang.trans("documents.restore.restore_dialog_no_btn"),
                showLoaderOnConfirm:false
            }).then(function(restore){
                
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



            }, function(dismiss){
                DMS.MessageBox.close();
            });
    });

    $('[data-action=resolveDuplicate').on('click', function(evt) {

        evt.preventDefault();
        evt.stopPropagation();

        var details = $(evt.target).data();

        DMS.MessageBox.wait( Lang.trans('documents.duplicates.processing'), '...');

        DMS.Services.Documents.resolveDuplicate(
            details.duplicateId, 
            function (data, other) {

                console.log("Duplicate resolution response", data, other);

                if(data.status && data.status === 'ok'){

                    DMS.navigateReload();
                    
                }
                else if(data.responseJSON && data.responseJSON.status === 'ok'){
                    
                    DMS.navigateReload();

                }
                else if(data.responseJSON && data.responseJSON.status === 'error'){
                    
                    DMS.MessageBox.error(Lang.trans('documents.duplicates.errors.title'), data.responseJSON.message);

                }
                else if(data.message) {
                    DMS.MessageBox.error(Lang.trans('documents.duplicates.errors.title'), data.message);
                }

            }, function(obj, err, errText){

                console.log('error', obj, err, errText);

                if(obj.responseJSON && obj.responseJSON.status === 'error'){
                    DMS.MessageBox.error(Lang.trans('documents.duplicates.errors.title'), obj.responseJSON.message);
                }
                else if(obj.responseJSON && obj.responseJSON.error){
                    DMS.MessageBox.error(Lang.trans('documents.duplicates.errors.title'), obj.responseJSON.error);
                }
                else {
                    DMS.MessageBox.error(Lang.trans('documents.duplicates.errors.title'), Lang.trans('documents.duplicates.errors.generic'));
                }

            }
        );
        

    });

});
