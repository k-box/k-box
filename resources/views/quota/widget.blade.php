@auth

    @unless ($unlimited)
        
        @component('components.progress', ['percentage' => $percentage ?? 0, 'height' => 'h-1'])
            <p class="text-sm">
                {{ trans('widgets.storage.title') }}
            </p>
            <p class="text-sm">
                {{ trans('widgets.storage.used', ['used' => $used, 'total' => $total]) }}
            </p>
        @endcomponent
    @endunless

@endauth