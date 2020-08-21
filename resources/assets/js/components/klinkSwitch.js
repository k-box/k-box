import ErrorResponse from "../utils/errorResponse";
import dispatch from "../utils/dispatch";

export default function(defaults) {

    return {
        document: defaults.document,
        published: defaults.published || false,
        publishing: defaults.publishing || false,
        unpublishing: defaults.unpublishing || false,
        error: defaults.error || false,
        
        publish (evt) {

            if(this.publishing || this.unpublishing){
                return;
            }

            this.publishing = true;
            this.errors = null;
            
            DMS.Services.Documents.makePublic({document_id: this.document}, function(data){

                debugger;

                if(data && data.status==='error'){
                    this.error = data.error;
                    this.publishing = false;
                    return ;
                }

                if(data.publication && data.publication.failed_at){

                    this.published = false;
                    this.publishing = false;
                    this.error = true;

                    return;
                }

                if(data.publication && data.publication.pending){

                    this.published = false;
                    this.publishing = true;

                    return;
                }

                this.published = true;
                this.publishing = false;
                    
            }, function(obj, err, errText){

                var errors = ErrorResponse(obj, err, errText);

                this.error = errors.message;
                this.publishing = false;
                    
            });
        
        },

        unpublish(evt) {

            if(this.publishing || this.unpublishing){
                return;
            }

            this.unpublishing = true;
            this.errors = null;

            DMS.Services.Documents.makePrivate(this.document, function(data){

                debugger;
                
                if(data && data.status==='error'){
                    this.error = data.error;
                    this.unpublishing = false;
                    return ;
                }

                this.published = false;
                this.unpublishing = false;
                    
            }, function(obj, err, errText){

                var errors = ErrorResponse(obj, err, errText);

                this.error = errors.message;
                this.unpublishing = false;
                    
            });

        }
    }
}