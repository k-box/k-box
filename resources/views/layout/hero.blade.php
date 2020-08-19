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
          <x-logo height="h-7" class="inline-block mb-2" />
          
          @yield('content')
        </div>
	    </main>
	  
	    @include('footer')
    </div>
  </div>
  <x-hero-image />
</div>

@endsection
