@extends('layout.shell')


@section('application')
<div class="relative bg-gray-50 overflow-hidden min-h-screen">
  <div class="max-w-screen-xl mx-auto  ">
    <div class="relative min-h-screen z-10 bg-gray-50 lg:max-w-2xl lg:w-full">

      <main class="relative pt-10 mx-auto max-w-screen-2xl px-4 sm:pt-12 sm:px-6 md:pt-24 lg:pt-32 lg:px-8 xl:pt-36">
        <div class="sm:text-center lg:text-left">
          <x-logo height="h-7" class="inline-block mb-2" />
          
          @yield('content')
        </div>
	    </main>
	  
	    @include('footer')
    </div>
  </div>
</div>

@endsection
