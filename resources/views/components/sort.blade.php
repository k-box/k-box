@props(['sorter'])

@if(!is_null($sorter ?? null))
    <div {{ $attributes->merge(['class' => 'relative']) }}  x-data="{ open: false }">

        <button type="button" @click="open = true" class="flex items-center button" title="{{ trans('sort.button') }}">
            @materialicon('navigation', $sorter->isDesc() ? 'arrow_upward' : 'arrow_downward', 'fill-current text-gray-400 w-4 h-4')

            {{ trans("sort.labels.{$sorter->field}") }}
            
            @materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow', [':class' => "{ 'rotate-180': open }"])
        </button>

        <div @click.away="open = false" x-show.transition="open" x-cloak class="absolute shadow-lg w-screen sm:w-56 p-2 mt-1 text-white bg-white rounded z-10 sm:right-0 }}">
            <ul class="text-black">

                @foreach ($sorter->sortables as $key => $opts)

                    <li class="flex items-center justify-center">
                        <a class="no-underline p-2 {{ $sorter->current($key) && $sorter->isAsc() ? 'text-accent-600' : 'text-gray-600' }} hover:bg-blue-100 active:bg-blue-200 focus:bg-blue-100 focus:outline-none" 
                            title="{{ trans("sort.directions.{$sorter->type($key)}.a") }}"
                            href="{{ $sorter->url(['sc' => $key, 'o' => \KBox\Sorter::ORDER_ASCENDING]) }}">
                            @materialicon('navigation', 'arrow_downward', 'fill-current')
                        </a>
                        <span class="flex-grow text-center no-underline {{ $sorter->current($key) ? 'text-accent-600' : 'text-black' }}">
                            {{ trans("sort.labels.{$key}") }}
                        </span>
                        <a class="no-underline p-2 {{ $sorter->current($key) && $sorter->isDesc() ? 'text-accent-600' : 'text-gray-600' }} hover:bg-blue-100 active:bg-blue-200 focus:bg-blue-100 focus:outline-none" 
                            title="{{ trans("sort.directions.{$sorter->type($key)}.d") }}"
                            href="{{ $sorter->url(['sc' => $key, 'o' => \KBox\Sorter::ORDER_DESCENDING]) }}">
                            @materialicon('navigation', 'arrow_upward', 'fill-current')
                        </a>
                    </li>
                        
                @endforeach

            </ul>

        </div>
    </div>
@endif