@extends('global')

@section('header-secondary')
    
@endsection



@section('content')

	<div class="c-login">

		<div class="c-login__container w-full shadow-lg md:bg-gray-200  md:w-4/5 mt-8">
			<div class="c-login__image">
				<img  class="c-login__background" src="{{asset('images/land-medium.jpg')}}">
		
				<div class="c-login__credit text-white">
					{{ trans('dashboard.welcome.photo_by') }} Yuri Skochilov, <a href="https://ecocentre.tj/en/" class="text-white" target="_blank" rel="noopener noreferrer">Youth Ecological Centre</a>
				</div>
			</div>
			
			<div class="c-login__form p-4">
				@yield('form')	
			</div>
		</div>
		
	</div>
@stop

@section('footer')

	@include('footer')

@stop