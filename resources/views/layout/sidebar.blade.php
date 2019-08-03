@extends('global')

@section('content')
<div class="md:flex md:flex-row max-h-available-header relative">

    <div class="js-drawer transition flex-shrink-0 min-h-0 absolute md:relative overflow-auto -translate-100 pointer-events-none md:pointer-events-auto md:translate-0 pr-4 opacity-0 md:opacity-100 md:w-64 xl:w-1/5 bg-white md:bg-transparent z-10 flex">
        <div class="flex-grow">
            @yield('sidebar')
        </div>
    
        <div class="">
            <button title="{{ __('Close navigation sidebar') }}" class="js-drawer-trigger md:hidden p-2 inline-block hover:text-blue-500 focus:text-blue-700">
				@materialicon('navigation', 'close', 'fill-current m-0 p-0')
            </button>
        </div>
    </div>
    
    <div class="flex-grow mt-2 min-h-0 overflow-auto ">

        <div class="py-2 border-b border-gray-400 text-sm flex flex-no-wrap">
            <button title="{{ __('Open navigation sidebar') }}" class="js-drawer-trigger md:hidden mr-4 pr-4 border-r border-gray-400 inline-block hover:text-blue-500 focus:text-blue-700">
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
</div>

@endsection

@push('js')

    <script>

        (function(){

        
            var drawer = $('.js-drawer');
            var drawerOpened = false;

			if(drawer && drawer.length === 1){
                var trigger = $(".js-drawer-trigger");

                function close(){
                    if(drawerOpened){
                        drawer.removeClass('sidebar-open');
                        drawerOpened = false;
                    }
                }

				trigger.on('click', function(evt){

					if(!drawerOpened){
						drawer.addClass('sidebar-open');
						drawerOpened = true;
					}
					else {
						close();
					}

                });
                
                if(typeof window.matchMedia === 'function'){
                    // registering for "md" media query
                    // to close the eventually open
                    // sidebar menu

                    var mediaQueryList = window.matchMedia("(min-width: 768px)");

                    mediaQueryList.addListener(close);
                }

			}

        })();
    </script>

@endpush