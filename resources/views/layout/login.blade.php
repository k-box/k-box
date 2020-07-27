@extends('global')

@section('header-secondary')
    
@endsection



@section('content')

	<div class="c-login">

		@yield('form')	
		
	</div>
@stop

@section('footer')

	@include('footer')

@stop