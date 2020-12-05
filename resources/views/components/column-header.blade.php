<div {{ $attributes->merge(['class' => 'relative flex']) }}>
    <span>{{ $slot }}</span>

    @if(!is_null($key ?? null) && !is_null($sort ?? null) && $sort->isSortable($key))

        <x-dropdown position="left-0" classes="ml-2">

            <x-slot name="panel">
                <ul class="text-black">
                    <li><a class="no-underline flex p-2 -mx-2 mb-1 {{ $sort->current($key) && $sort->isAsc() ? 'text-accent-600' : 'text-gray-600' }} hover:bg-blue-100 active:bg-blue-200 focus:bg-blue-100 focus:outline-none" href="{{ $sort->url(['sc' => $key, 'o' => \KBox\Sorter::ORDER_ASCENDING]) ?? '#' }}">
                        @materialicon('navigation', 'arrow_downward', 'fill-current mr-2') {{ trans("sort.directions.{$sort->type($key)}.a") }}
                    </a></li>
                    <li><a class="no-underline flex p-2 -mx-2 mb-1 {{ $sort->current($key) && $sort->isDesc() ? 'text-accent-600' : 'text-gray-600' }} hover:bg-blue-100 active:bg-blue-200 focus:bg-blue-100 focus:outline-none" href="{{ $sort->url(['sc' => $key, 'o' => \KBox\Sorter::ORDER_DESCENDING]) ?? '#' }}">
                        @materialicon('navigation', 'arrow_upward', 'fill-current mr-2') {{ trans("sort.directions.{$sort->type($key)}.d") }}
                    </a></li>
                </ul>
            </x-slot>
        
            <div class="inline-flex items-center" title="{{ trans('sort.change_direction') }}">
                @if($sort->current($key))
                    @materialicon('navigation', $sort->isDesc() ? 'arrow_upward' : 'arrow_downward', 'fill-current text-gray-400 w-4 h-4')
                @endif
                @materialicon('navigation', 'arrow_drop_down', 'inline fill-current arrow', [':class' => "{ 'rotate-180': open }"])
            </div>

        </x-dropdown>
    @endif
</div>