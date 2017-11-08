define("modules/uploadjobs", ["jquery", "DMS", "modules/minimalbind", "sweetalert", 'language', 'Handlebars', "lodash"], function ($, _DMS, _rivets, _alert, Lang, Handlebars, _) {

    var Uploader = new window.TusUploader({autoUpload: false, chunkSize:50000});

    Handlebars.registerHelper('status', function(status) {
        switch (status) {
            case Uploader.Status.STARTED:
                return Lang.trans("upload.status.started");
                break;
            case Uploader.Status.PENDING:
                return Lang.trans("upload.status.queued");
                break;
            case Uploader.Status.UPLOADING:
                return Lang.trans("upload.status.uploading");
                break;
            case Uploader.Status.COMPLETED:
                return Lang.trans("upload.status.completed");
                break;
            case Uploader.Status.CANCELLED:
                return Lang.trans("upload.status.cancelled");
                break;
            case Uploader.Status.FAILED:
                return Lang.trans("upload.status.failed");
                break;
        
            default:
                return '';
                break;
        }
    });

    Handlebars.registerHelper('if_eq', function(a, b, opts) {
        
        if(a === b) 
            return opts.fn(this);
        else
            return opts.inverse(this);
    });
    
    Handlebars.registerHelper('if_geq', function(a, b, opts) {
        if(a >= b) 
            return opts.fn(this);
        else
            return opts.inverse(this);
    });
    
    Handlebars.registerHelper('if_leq', function(a, b, opts) {
        if(a <= b) 
            return opts.fn(this);
        else
            return opts.inverse(this);
    });

    var templateSource = $("#upload-template");
    var compiledTemplate = Handlebars.compile(templateSource.html());
    var bindArea = $("#upload");
    var options = {
        collection: null,
    }

    templateSource.hide();

    /**
     * Render a template on an element with the given data
     * @param {HTMLElement} el the element in which the template should be rendered
     * @param {string} template the template string
     * @param {object} data the data object to be used for placeholder substitution
     * @return {void}
     */
    function render(data) {
        var output = compiledTemplate(data);

        bindArea.html(output);
    }

    function handleTemplateClick(evt){

        if(this.dataset.action){

            var current = _.find(upload_jobs_module.uploadjobs, {id: this.dataset.id});

            upload_jobs_module[this.dataset.action].call(current);
        }


        evt.preventDefault();
        evt.stopPropagation();
    }

    bindArea.on('click', 'button', handleTemplateClick);

    var upload_jobs_module = {

        uploadjobs: [],


        start: function(){

            console.log('start called', this);

            this.start();
        },
        
        cancel: function(){

            console.log('cancel called', this);

            this.stop();

        },

        remove: function(){

            console.log('remove called', this);

            Uploader.remove(this.id);

        },
        
        open: function(){

            console.log('open called', this);

            var uploading = Uploader.uploads({status: Uploader.Status.UPLOADING});
            var started = Uploader.uploads({status: Uploader.Status.STARTED});

            if(uploading && uploading.length > 0 || started && started.length > 0){

                var url = DMS.Paths.fullUrl('uploadjobs/' + this.id);

                var otherWindow = window.open();
                otherWindow.opener = null;
                otherWindow.location = url;
            }
            else {
                DMS.navigate('uploadjobs/' + this.id, null, true);
            }


        },

        setTargetCollection: function(collection_id){
            options.collection = collection_id;
        }
        
    };
    
    function updateUI(){
        // todo: introduce a small delay to enable the user to click on the cancel button while upload is in progress
        render(upload_jobs_module);
    }

    updateUI();
    


    function genericEventHandler(upl){

        console.log(upl);

        upload_jobs_module.uploadjobs = Uploader.uploads();
        
        updateUI();
    }
    
    function nextUploadEventHandler(upl){

        console.log(upl);

        upload_jobs_module.uploadjobs = Uploader.uploads();
        
        updateUI();

        
        pickNextUpload();
    }

    function pickNextUpload(){
        var queue = Uploader.uploads();
        var pending = _.filter(queue, {status: Uploader.Status.QUEUED});
        var uploading = _.filter(queue, {status: Uploader.Status.UPLOADING});

        if(uploading.length===0 && pending.length > 0){
            _.head(pending).start();
        }
    }

    Uploader.on('upload.queued',genericEventHandler);
    Uploader.on('upload.started',genericEventHandler);
    Uploader.on('upload.completed',nextUploadEventHandler);
    Uploader.on('upload.cancelled',nextUploadEventHandler);
    Uploader.on('upload.progress',genericEventHandler);
    Uploader.on('upload.failed',nextUploadEventHandler);
    Uploader.on('upload.removed',nextUploadEventHandler);


    function addFiles(files){

        var queue_length = Uploader.uploads().length;

        for (var i = 0, f, added; i < files.length ;  i++) {
            f = files[i];
            added = Uploader.add(f, {collection: options.collection});
        }


        pickNextUpload();
    }


    // Handle input change

    var hiddenFileInput = null;
    var setupHiddenFileInput = function setupHiddenFileInput() {
        
        if (hiddenFileInput) {
          hiddenFileInput.parentNode.removeChild(hiddenFileInput);
        }
        hiddenFileInput = document.createElement("input");
        hiddenFileInput.setAttribute("type", "file");
        
        hiddenFileInput.setAttribute("multiple", "multiple");
        
        hiddenFileInput.className = "upload-field";

        document.querySelector('.js-upload-fallback').appendChild(hiddenFileInput);
        return hiddenFileInput.addEventListener("change", function (e) {
        
          addFiles(e.target.files);

          return setupHiddenFileInput();
        });
      };
    setupHiddenFileInput();


    // Handle drag and drop

    bindArea.on('dragover', function(evt){
        
        evt.stopPropagation();
        evt.preventDefault();
        
        evt.originalEvent.dataTransfer.dropEffect = 'copy';

        bindArea.find('.upload-trigger')[0].classList.add('upload-trigger--dragover');

    });
    
    bindArea.on('dragleave', function(evt){

        bindArea.find('.upload-trigger')[0].classList.remove('upload-trigger--dragover');

    });

    bindArea.on('drop', function(evt){

        evt.stopPropagation();
        evt.preventDefault();

        console.log(evt.originalEvent.dataTransfer);
    
        var files = evt.originalEvent.dataTransfer.files; // FileList object.
    
        addFiles(files);
        

    });

    bindArea.on('click', '.js-upload-fallback', function(evt){

        hiddenFileInput.click();
    });

    return upload_jobs_module;

});