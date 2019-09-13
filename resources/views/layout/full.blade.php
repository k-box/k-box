@extends('global')

@section('content')
<div class="max-h-available-header relative">

        <div class="py-2 border-b border-gray-400 text-sm flex flex-no-wrap">
            <button title="{{ __('Open navigation sidebar') }}" class="js-drawer-trigger md:hidden mr-2 pr-2 inline-block hover:text-blue-500 focus:text-blue-700">
				@materialicon('navigation', 'menu', 'fill-current m-0 p-0')
            </button>
            
            @yield('breadcrumbs')
        </div>

        @if(Session::has('flash_message'))
            <div class="c-message c-message--success mt-2">
                {{session('flash_message')}}
            </div>      
        @endif
        
        @hasSection('action-menu')
            <div id="action-bar" class="h-12 flex flex-no-wrap items-center py-1 actions js-drawer-action-bar mt-2">
                @yield('action-menu')
            </div>
        @endif
        
        @yield('page')
        
    
</div>

@endsection
