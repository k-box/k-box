{{-- Anonymous component that handles star actions. Used on:
    - documents area (list-item.blade.php)
    - documents detail panel (properties.blade.php)
    - document detail panel on the preview page (preview_properties.blade.php).
    Properties required to be passed are star id, document id, and star count. --}}
@props(['starID', 'documentID', 'count' => null])


<button type="button" {{ $attributes->merge(['class' => 'inline-flex items-center']) }} 
    x-data="Star({starID: '{{$starID}}', documentID: '{{$documentID}}', count: {{(int)$count ?? 'null'}} })" 
    x-on:click.stop="star" 
    :class="{ 'item__star--starred': starred === true, 'item__star--starring' :  inProgress === true  }"
    x-bind:title="starred ? '{{ trans('starred.remove') }}' : '{{ trans('starred.add')}}'">

        @materialicon('toggle', 'star_border', ' star star--not-starred')
        @materialicon('toggle', 'star', ' star star--starred')

        @if ($count)
            <span class="inline-block ml-2">
                {{trans_choice('starred.starred_count_alt', $count, ['number' => $count])}}
            </span>
        @endif

        <!-- upon error -->
        <span x-cloak class="field-error" x-show="error && !starred">{{trans('starred.errors.unable_to_star')}}</span>
        <span x-cloak class="field-error" x-show="error && starred">{{trans('starred.errors.unable_to_unstar')}}</span>
</button>

        