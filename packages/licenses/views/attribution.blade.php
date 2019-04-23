
{{--  License notice component. For printing out the license short help text  --}}

<span class="license-attribution">

    @if(isset($icon))
    {{ $icon }}
    @endif

    <span>
        {{ $owner ?? '' }}<br/>
            
        {{ $slot }}
    </span>
        
</span>
