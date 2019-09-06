@component('components.dropdown', ['classes' => 'inline-block mr-2 relative', 'title' => trans('pages.help')])

    @materialicon('action', 'help_outline', 'inline fill-current m-0 p-0')

    @materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow')

    @slot('panel')
        <ul class="">
            <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" href="{{ route('help') }}">{{trans('pages.help')}}</a></li>
            <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" href="{{ route('privacy.summary') }}">{{trans('pages.privacy')}}</a></li>
            <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a></li>
            <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
        </ul>
    @endslot
    
@endcomponent