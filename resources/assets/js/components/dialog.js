export default function() {

    return {
        open: false,
        url: null,
        params: {},
        message: '',
        loading: false,
        
        show () {
            this.open = true;
        },
        
        hide () {
            this.open = false;
            this.url = null;
            this.params = {};
            this.message = '';
            this.loading = true;
        },

        showDialog ($event) {
            this.url = $event.detail.url;
            this.params = $event.detail.params;
        },

        init () {

            this.$watch('url', value => {

                if(!this.url){
                    return;
                }

                this.loading = true;
                this.message = "Loading...";
                this.open = true;

                DMS.Ajax.getHtml(this.url, this.params, function(ok){

                    this.message = ok;
                    this.loading = false;

				}.bind(this), function(obj, err, text){

                    console.error(obj, err, text);
                    this.message = "Error";
                    this.loading = false;

				}.bind(this));
            })
        }
    }
}