
{{--  License short help component. For printing out the license short help text  --}}

<div class="license-help">

    <div class="license__owner">
        {{ $owner or '' }}
    </div>
        
    <div class="license__description">
        {{ $description }}
    </div>

    <em>{{ trans('license::help.description_disclaimer') }}</em>
        
</div>
