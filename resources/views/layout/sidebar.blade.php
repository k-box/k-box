@extends('global')

@section('content')
<div class="md:flex md:flex-row h-page  relative">

    <div class="js-drawer transition flex-shrink-0 min-h-0 h-page absolute md:relative overflow-y-auto scrolling-touch overflow-x-hidden -translate-100 pointer-events-none md:pointer-events-auto md:translate-0 mr-4 opacity-0 md:opacity-100 w-screen md:w-64 xl:w-1/5 bg-white md:bg-transparent z-10">
        
        <div class="inline-block md:hidden fixed top-0 right-0 z-10 ">
            <button title="{{ __('Close navigation sidebar') }}" class="js-drawer-trigger sticky p-2 hover:text-blue-500 focus:text-blue-700">
				@materialicon('navigation', 'close', 'fill-current m-0 p-0')
            </button>
        </div>
        
        <div class="mb-4">
            @yield('sidebar')
        </div>

        @hasSection('sidebar_bottom')
            <div class="pt-4 pb-8">
                @yield('sidebar_bottom')
            </div>
        @endif
    </div>
    
    <div class="flex-grow min-h-0 overflow-auto ">

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
                        drawer.addClass('-translate-100');
                        drawerOpened = false;
                    }
                }

				trigger.on('click', function(evt){

					if(!drawerOpened){
                        drawer.addClass('sidebar-open');
                        drawer.removeClass('-translate-100');
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