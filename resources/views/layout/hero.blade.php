@extends('layout.shell')


@section('application')
<div class="relative bg-gray-50 overflow-hidden min-h-screen">
  <div class="max-w-screen-xl mx-auto  ">
    <div class="relative min-h-screen z-10 bg-gray-50 lg:max-w-2xl lg:w-full">
      <svg class="hidden lg:block absolute right-0 inset-y-0 min-h-screen h-full w-48 text-gray-50 transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
        <polygon points="50,0 100,0 50,100 0,100" />
      </svg>

      <main class="relative pt-10 mx-auto max-w-screen-2xl px-4 sm:pt-12 sm:px-6 md:pt-24 lg:pt-32 lg:px-8 xl:pt-36">
        <div class="sm:text-center lg:text-left">
          @yield('content')
        </div>
	    </main>
	  
	    @include('footer')
    </div>
  </div>
  <div class="hidden lg:block lg:min-h-screen lg:fixed bg-gray-900 lg:inset-y-0 lg:right-0 lg:w-1/2">
    <img class="w-full object-cover lg:w-full h-full" src="https://images.unsplash.com/photo-1563654727148-d7e9d1ed2a86?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="">
  </div>
</div>

@endsection
{{-- 
    <img class="w-full object-cover lg:w-full h-full" src="https://images.unsplash.com/photo-1496043549741-8815b378cd48?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="">
    <img class="w-full object-cover lg:w-full h-full" src="https://images.unsplash.com/photo-1563654726935-ec8371114841?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="">
    <img class="w-full object-cover lg:w-full h-full" src="https://images.unsplash.com/photo-1563654727148-d7e9d1ed2a86?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="">
 --}}
