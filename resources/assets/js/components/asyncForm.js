import ErrorResponse from "../utils/errorResponse";
import dispatch from "../utils/dispatch";

export default function(defaults) {

    return {

        ...defaults,

        submitting: false,
        errors: null,
        
        submit (evt) {

            if(this.submitting){
                return;
            }

            this.errors = null;
            this.submitting = true;

            var $form = $(evt.target);
            
            var params = $form.serializeJSON();

            return $.ajax({
                url: $form.attr('action'),
                type: 'post',
                dataType: 'json',
                data: params,
                success: function(data){
                    this.errors = null;
                    this.submitting = false;

                    dispatch(evt.target, 'form-submitted', {data: data});

                }.bind(this),
                error: function(obj, err, text){
                    
                    this.submitting = false;

                    var errors = ErrorResponse(obj, err, text);

                    this.errors = errors.message;

                    dispatch(evt.target, 'form-errored', {errors: errors.message});
                }.bind(this)
            });
        
        }
    }
}