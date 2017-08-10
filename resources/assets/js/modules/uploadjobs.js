define("modules/uploadjobs", ["jquery", "DMS", "modules/minimalbind", "sweetalert", 'language', 'Handlebars', "lodash"], function ($, _DMS, _rivets, _alert, Lang, Handlebars, _) {

    var Uploader = new window.TusUploader({autoUpload: false, chunkSize:50000});

    Handlebars.registerHelper('status', function(status) {
        switch (status) {
            case Uploader.Status.STARTED:
                return "started";
                break;
            case Uploader.Status.PENDING:
                return "queued";
                break;
            case Uploader.Status.UPLOADING:
                return "uploading";
                break;
            case Uploader.Status.COMPLETED:
                return "completed";
                break;
            case Uploader.Status.CANCELLED:
                return "cancelled";
                break;
            case Uploader.Status.FAILED:
                return "failed";
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

    var templateSource = $("#upload-template");
    var compiledTemplate = Handlebars.compile(templateSource.html());
    var bindArea = $("#upload");

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

        }
        
    };
    
    function updateUI(){
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
        debugger;
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
            added = Uploader.add(f);
        }


        pickNextUpload();
    }


    // Handle input change
    var input = document.getElementById('file');

    input.addEventListener("change", function(e) {
        // Get the selected file from the input element
        // var file = e.target.files[0]
        addFiles(e.target.files);
        // var added = Uploader.add(file);

    });


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

});