import ErrorResponse from "../utils/errorResponse";
import dispatch from "../utils/dispatch";

export default function(defaults) {

    return {
        url: defaults.url,
        params: defaults.params || {},
        loading: defaults.loading || false,
        errors: null,
        useCache: defaults.useCache || false,
        content: null,

        refresh() {
            if(!this.url){
                return;
            }

            this.loading = true;
            this.useCache = false;

            DMS.Ajax.getHtml(this.url, this.params, function(ok){

                this.content = ok;
                this.loading = false;

            }.bind(this), function(obj, err, text){

                var error = ErrorResponse(obj, err, text);
                this.errors = error.htmlMessage;
                this.loading = false;

            }.bind(this),
            true);
        }
    }
}