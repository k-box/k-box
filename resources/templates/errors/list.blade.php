@if($errors->any())

    <div class="alert error">

    	@if( $errors->has('error') )

	        <p>{{ implode(",", $errors->get('error'))  }}</p>
	    
	    @else

        <p>You have some errors, please correct them before proceeding</p>

        @endif

        <!-- <ul>
        
            @foreach($errors->all() as $error)

                <li>{{$error}}</li>

            @endforeach
        </ul> -->

    </div>

@endif