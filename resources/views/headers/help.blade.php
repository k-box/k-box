@component('components.dropdown', ['classes' => 'inline-block mr-2', 'title' => trans('pages.help')])

    @materialicon('action', 'help_outline', 'inline fill-current m-0 p-0')

    @materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow')

    @slot('panel')
        <ul class="">
            <li><a class="block p-2 -mx-2 mb-1 text-white hover:bg-gray-600 active:bg-gray-500 focus:bg-gray-500" href="{{ route('help') }}">{{trans('pages.help')}}</a></li>
            <li><a class="block p-2 -mx-2 mb-1 text-white hover:bg-gray-600 active:bg-gray-500 focus:bg-gray-500" href="{{ route('privacy.summary') }}">{{trans('pages.privacy')}}</a></li>
            <li><a class="block p-2 -mx-2 mb-1 text-white hover:bg-gray-600 active:bg-gray-500 focus:bg-gray-500" href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a></li>
            <li><a class="block p-2 -mx-2 mb-1 text-white hover:bg-gray-600 active:bg-gray-500 focus:bg-gray-500" href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
        </ul>
    @endslot
    
@endcomponent