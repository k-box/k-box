@if(isset($errors) && $errors->any())

    <div class="c-message c-message--error">

    	@if( $errors->has('error') )

	        <p>{!! implode(",", $errors->get('error'))  !!}</p>
	    
	    @else

        <p>{{trans('errors.generic_form_error')}}</p>

        @endif

    </div>

@endif