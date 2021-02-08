@props(['starID', 'documentID'])


<button type="button" {{ $attributes->merge(['class' => 'button inline-flex items-center']) }} 
    x-data="Star({starID: '{{$starID}}', documentID: '{{$documentID}}'})" 
    x-on:click.stop="star" 
    :class="{ 'bg-green-300 border-green-700': starred === true, 'item__star--starring' : inProgress === true  }"
    title="{{ trans('starred.add') }}"> <!-- TODO: check for appropriate default string for the button -->

        <!-- image effects of the button change depending on the state. TODO: find out appropriate params here -->
        <span class="" x-show="!starred">@materialicon('toggle', 'star_border', ' star star--not-starred')</span>
        <span class="" x-show="starred">@materialicon('toggle', 'star', ' star star--starred')</span>

        <!-- text of the button changes depending on the state -->
        <span class="hidden md:inline md:ml-1" x-show="!starred">{{ trans('starred.add') }}</span>
        <span class="hidden md:inline md:ml-1" x-show="starred">{{ trans('starred.remove') }}</span>
        
        <!-- upon error -->
        <span class="field-error" x-show="error && !starred">{{trans('starred.errors.unable_to_star')}}</span>
        <span class="field-error" x-show="error && starred">{{trans('starred.errors.unable_to_unstar')}}</span>
</button>

        