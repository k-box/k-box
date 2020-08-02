import ErrorResponse from "../utils/errorResponse";

export default function() {

    return {
        open: false,
        url: null,
        params: {},
        message: null,
        errorMessage: null,
        loading: false,
        
        show () {
            this.open = true;
        },
        
        hide () {
            this.open = false;
            this.url = null;
            this.params = {};
            this.message = null;
            this.errorMessage = null;
            this.loading = true;
        },

        showDialog ($event) {
            this.params = $event.detail.params || {};
            // url set last as watcher is called
            // synchronously when the value changes
            this.url = $event.detail.url;
        },

        init () {

            this.$watch('url', value => {
console.log(this.url, value);
                if(!value){
                    return;
                }

                this.loading = true;
                this.open = true;

                DMS.Ajax.getHtml(value, this.params, function(ok){

                    this.message = ok;
                    this.loading = false;

				}.bind(this), function(obj, err, text){

                    var error = ErrorResponse(obj, err, text);
                    this.errorMessage = error.htmlMessage;
                    this.loading = false;

				}.bind(this));
            })
        }
    }
}