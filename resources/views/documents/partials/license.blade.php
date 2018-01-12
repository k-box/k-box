{{--  licese hint for the details panel  --}}

@if($license)

<div class="license">

    <div class="license__attribution">

        @component('license::attribution')

            @slot('icon')
                {!! $license->icon or '' !!}
            @endslot
            
            @slot('owner')
                {{ $owner->get('name', '') }}. 
            @endslot
        
            <button class="button button--link" data-action="showCopyrightUsageDescription">
                @if($license->id === 'C')
                {{ trans('license::attribution.copyright') }}
                @elseif($license->id === 'PD')
                    {{ trans('license::attribution.publicdomain') }}
                @else
                    {{ trans('license::attribution.licensed', ['license' => $license->title]) }}
                @endif
            </button>

        @endcomponent

    </div>

    <div class="license__details js-license-details">

        @component('license::deed')
            
            @slot('owner')
                <span>{{ $owner->get('name', '') }}</span>
                
                @if($owner->get('email', null))
                    <span>{{ $owner->get('email') }}</span>
                @endif

                @if($owner->get('website', null))
                    <span><a href="{{ $owner->get('website') }}" target="_blank" rel="noopener noreferrer nofollow">{{ $owner->get('website') }}</a></span>
                @endif

                @if($owner->get('address', null))
                    <span>{{ $owner->get('address') }}</span>
                @endif

                @if($license->license)
                    <span><a href="{{ $license->license }}">{{ $license->short_title }}</a></span>
                @endif

            @endslot
            
            @slot('description')

                @if($license->license)
                    <span>{!! trans('license::help.deed_intro', ['link' => $license->license]) !!}</span>
                @endif

                {!! Markdown::convertToHtml($license->description) !!}
            @endslot
    
        @endcomponent

    </div>
</div>

@endif
