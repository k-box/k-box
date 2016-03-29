@if(isset($errors) && $errors->any())

    <div class="alert error">

    	@if( $errors->has('error') )

	        <p>{{ implode(",", $errors->get('error'))  }}</p>
	    
	    @else

        <p>You have some errors, please correct them before proceeding</p>

        @endif

    </div>

@endif