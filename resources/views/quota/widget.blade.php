@auth

    @unless ($unlimited)
        
        @component('components.progress', ['percentage' => $percentage ?? 0, 'height' => 'h-1'])
            <p class="text-sm text-gray-700">
                {{ trans('widgets.storage.title') }}
            </p>
            <p class="text-sm">
                <a href="{{ route('profile.storage.index') }}" class="no-underline text-gray-700">{{ trans('widgets.storage.used', ['used' => $used, 'total' => $total]) }}</a>
            </p>
        @endcomponent
    @endunless

@endauth