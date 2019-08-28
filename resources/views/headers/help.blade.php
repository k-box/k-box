
@component('components.dropdown', ['classes' => 'inline-block mr-2', 'title' => trans('pages.help')])

    @materialicon('action', 'help_outline', 'inline fill-current m-0 p-0')

    @materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow')

    @slot('panel')
        <ul class="">
            <li><a href="{{ route('help') }}">{{trans('pages.help')}}</a></li>
            <li><a href="{{ route('privacy.summary') }}">{{trans('pages.privacy')}}</a></li>
            <li><a href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a></li>
            <li><a href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
        </ul>
    @endslot
    
@endcomponent


{{-- <div class="inline-block" data-dropdown>

    <button type="button" data-dropdown-trigger class="flex hover:text-blue-600 items-center  mr-2" title="{{trans('pages.help')}}">
        @materialicon('action', 'help_outline', 'inline fill-current m-0 p-0')

        @materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow')
    </button>

    <div  data-dropdown-panel class="absolute shadow hidden w-full sm:w-56 right-0 block p-2 mt-1 text-white bg-gray-700 rounded">

        <ul class="">
            <li><a href="{{ route('help') }}">{{trans('pages.help')}}</a></li>
            <li><a href="{{ route('privacy.summary') }}">{{trans('pages.privacy')}}</a></li>
            <li><a href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a></li>
            <li><a href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
        </ul>

    </div>

</div> --}}