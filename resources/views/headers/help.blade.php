@component('components.dropdown', ['classes' => 'inline-block mr-2 sm:relative', 'title' => trans('pages.help')])

    @materialicon('action', 'help_outline', 'inline fill-current m-0 p-0')

    @materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow')

    @slot('panel')
        <ul class="">
            <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" @if(! (\Request::is('*help') || \Request::is('*privacy*') || \Request::is('*terms') || \Request::is('*contact'))) target="_blank" @endif rel="nopener noreferrer" href="{{ route('help') }}">{{trans('pages.help')}}</a></li>
            @if(support_active('mail'))
                <li>
                    @includeWhen(support_active('mail'), 'support.mail', [
                        'class' => 'no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none',
                        'feedback_user' => optional(auth()->user())->uuid,
                        'product' => config('app.name'),
                        'version' => config("dms.version"),
                        'route' => ! is_null(\Route::getCurrentRoute()->getName()) ? \Route::getCurrentRoute()->getName() : \Route::getCurrentRoute()->getPath(),
                    ])
                    <a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" @if(! (\Request::is('*help') || \Request::is('*privacy*') || \Request::is('*terms') || \Request::is('*contact'))) target="_blank" @endif rel="nopener noreferrer" href="{{ route('contact') }}">{{trans('pages.contact')}}</a>
                </li>
            @endif        
            @haspage(\KBox\Pages\Page::PRIVACY_POLICY_LEGAL)
                <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" @if(! (\Request::is('*help') || \Request::is('*privacy*') || \Request::is('*terms') || \Request::is('*contact'))) target="_blank" @endif rel="nopener noreferrer" href="{{ route('privacy.legal') }}">{{trans('pages.privacy')}}</a></li>
            @endhaspage
            @haspage(\KBox\Pages\Page::TERMS_OF_SERVICE)
                <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" @if(! (\Request::is('*help') || \Request::is('*privacy*') || \Request::is('*terms') || \Request::is('*contact'))) target="_blank" @endif rel="nopener noreferrer" href="{{ route('terms') }}">{{trans('pages.terms_long')}}</a></li>
            @endhaspage
            @if(\KBox\Option::areContactsConfigured())
                <li><a class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-gray-300 active:bg-gray-400 focus:bg-gray-400 focus:outline-none" @if(! (\Request::is('*help') || \Request::is('*privacy*') || \Request::is('*terms') || \Request::is('*contact'))) target="_blank" @endif rel="nopener noreferrer" href="{{ route('contact') }}">{{trans('pages.contact')}}</a></li>
            @endif
        </ul>
    @endslot
    
@endcomponent